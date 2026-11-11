<?php

namespace App\Http\Controllers;

use App\Models\ConsumerReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConsumerReviewController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:40',
            'email' => 'nullable|email|max:255',
            'order_number' => 'nullable|string|max:100',
            'type' => 'nullable|in:review,claim,suggestion',
            'message' => 'required|string|max:4000',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp,heic|max:8192',
        ], [
            'name.required' => 'Ім\'я є обов\'язковим полем',
            'phone.required' => 'Номер телефону є обов\'язковим полем',
            'email.email' => 'Введіть коректний email',
            'message.required' => 'Напишіть текст звернення',
            'message.max' => 'Текст не може перевищувати 4000 символів',
            'type.in' => 'Невірний тип звернення',
            'photo.image' => 'Файл має бути зображенням',
            'photo.mimes' => 'Допустимі формати: JPG, PNG, WEBP',
            'photo.max' => 'Розмір фото не більше 8 МБ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Перевірте заповнення форми',
            ], 422);
        }

        $type = $request->input('type', 'review') ?: 'review';
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('consumer-reviews', 'public');
        }

        $review = ConsumerReview::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'order_number' => $request->order_number,
            'type' => $type,
            'message' => $request->message,
            'photo' => $photoPath,
            'status' => 'new',
        ]);

        try {
            $this->sendTelegramNotification($review);
        } catch (\Exception $e) {
            \Log::error('Consumer review Telegram failed', [
                'error' => $e->getMessage(),
                'review_id' => $review->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Дякуємо! Ваше звернення отримано. Ми зв\'яжемося з вами найближчим часом.',
            'id' => $review->id,
        ]);
    }

    private function sendTelegramNotification(ConsumerReview $review): void
    {
        $typeLabel = ConsumerReview::TYPES[$review->type] ?? $review->type;

        $text = "📝 Куточок споживача — нове звернення\n\n";
        $text .= "🏷 Тип: " . $typeLabel . "\n";
        $text .= "👤 Ім'я: " . $review->name . "\n";
        $text .= "📞 Телефон: " . $review->phone . "\n";

        if ($review->email) {
            $text .= "✉️ Email: " . $review->email . "\n";
        }
        if ($review->order_number) {
            $text .= "🧾 Замовлення: " . $review->order_number . "\n";
        }

        $text .= "💬 Текст:\n" . $review->message . "\n";
        $text .= "\n🆔 ID: " . $review->id;
        $text .= "\n⏰ Час: " . now()->format('d.m.Y H:i:s');

        if ($review->photo && Storage::disk('public')->exists($review->photo)) {
            $absolute = Storage::disk('public')->path($review->photo);
            $this->dispatchTelegramPhoto($text, $absolute);
            return;
        }

        $this->dispatchTelegram($text);
    }

    private function telegramCredentials(): array
    {
        if (!config('telegram.enabled', true)) {
            throw new \Exception('Telegram notifications are disabled');
        }

        $botToken = trim((string) config('telegram.bot_token'));
        $chatId = trim((string) config('telegram.chat_id'));

        if ($botToken === '' || $chatId === '') {
            throw new \Exception('Telegram is not configured');
        }

        return [$botToken, $chatId];
    }

    private function dispatchTelegram(string $text): void
    {
        [$botToken, $chatId] = $this->telegramCredentials();

        $this->curlTelegram(
            "https://api.telegram.org/bot{$botToken}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $text,
            ]
        );
    }

    private function dispatchTelegramPhoto(string $caption, string $absolutePath): void
    {
        [$botToken, $chatId] = $this->telegramCredentials();

        if (!is_readable($absolutePath)) {
            $this->dispatchTelegram($caption . "\n\n🖼 Фото збережено, але не вдалося надіслати файл.");
            return;
        }

        // Caption limit for Telegram photos is 1024 chars
        $caption = mb_substr($caption, 0, 1024);

        $post = [
            'chat_id' => $chatId,
            'caption' => $caption,
            'photo' => new \CURLFile($absolutePath),
        ];

        $this->curlTelegram(
            "https://api.telegram.org/bot{$botToken}/sendPhoto",
            $post,
            true
        );
    }

    private function curlTelegram(string $url, array $data, bool $multipart = false): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $multipart ? $data : http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $ok = false;
        if (is_string($result) && $result !== '') {
            $decoded = json_decode($result, true);
            $ok = is_array($decoded) && !empty($decoded['ok']);
        }

        if ($httpCode !== 200 || !$ok) {
            throw new \Exception('Failed to send Telegram notification: ' . ($curlError ?: "HTTP {$httpCode}"));
        }
    }
}
