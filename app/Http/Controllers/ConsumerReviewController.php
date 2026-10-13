<?php

namespace App\Http\Controllers;

use App\Models\ConsumerReview;
use Illuminate\Http\Request;
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
        ], [
            'name.required' => 'Ім\'я є обов\'язковим полем',
            'phone.required' => 'Номер телефону є обов\'язковим полем',
            'email.email' => 'Введіть коректний email',
            'message.required' => 'Напишіть текст звернення',
            'message.max' => 'Текст не може перевищувати 4000 символів',
            'type.in' => 'Невірний тип звернення',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Перевірте заповнення форми',
            ], 422);
        }

        $type = $request->input('type', 'review') ?: 'review';

        $review = ConsumerReview::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'order_number' => $request->order_number,
            'type' => $type,
            'message' => $request->message,
            'status' => 'new',
        ]);

        try {
            $this->sendTelegramNotification($review);
        } catch (\Exception $e) {
            \Log::error('Consumer review Telegram failed', [
                'error' => $e->getMessage(),
                'review_id' => $review->id,
            ]);
            // Review is already saved — still return success so the lead is not lost
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

        $this->dispatchTelegram($text);
    }

    private function dispatchTelegram(string $text): void
    {
        if (!config('telegram.enabled', true)) {
            throw new \Exception('Telegram notifications are disabled');
        }

        $botToken = trim((string) config('telegram.bot_token'));
        $chatId = trim((string) config('telegram.chat_id'));

        if ($botToken === '' || $chatId === '') {
            throw new \Exception('Telegram is not configured');
        }

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);

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
