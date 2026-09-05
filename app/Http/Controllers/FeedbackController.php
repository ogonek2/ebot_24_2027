<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * FeedbackController
 * 
 * Для настройки Telegram уведомлений добавьте в файл .env:
 * TELEGRAM_BOT_TOKEN=ваш_токен_бота
 * TELEGRAM_CHAT_ID=ваш_id_группы
 * TELEGRAM_ENABLED=true
 */

class FeedbackController extends Controller
{
    public function submitCourierOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'type' => 'required|in:courier,pickup',
            'address' => 'nullable|required_if:type,courier|string|max:500',
            'date' => 'nullable|string|max:20',
            'time' => 'nullable|string|max:50',
            'comment' => 'nullable|string|max:2000',
        ], [
            'name.required' => 'Ім\'я є обов\'язковим полем',
            'name.max' => 'Ім\'я не може перевищувати 255 символів',
            'phone.required' => 'Номер телефону є обов\'язковим полем',
            'phone.max' => 'Номер телефону не може перевищувати 30 символів',
            'type.required' => 'Оберіть спосіб отримання',
            'type.in' => 'Невірний спосіб отримання',
            'address.required_if' => 'Адреса забору є обов\'язковою для кур\'єра',
            'address.max' => 'Адреса не може перевищувати 500 символів',
            'date.max' => 'Невірний формат дати',
            'time.max' => 'Невірний формат часу',
            'comment.max' => 'Коментар не може перевищувати 2000 символів',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->sendCourierTelegramNotification(
                $request->name,
                $request->phone,
                $request->type,
                $request->address,
                $request->date,
                $request->time,
                $request->comment
            );

            return response()->json([
                'success' => true,
                'message' => 'Дякуємо! Ми зв\'яжемося з вами протягом 30 хвилин.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Виникла помилка при відправці повідомлення. Спробуйте пізніше.',
            ], 500);
        }
    }

    public function submitB2bProposal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|max:255',
            'volume' => 'required|string|max:100',
            'comment' => 'nullable|string|max:2000',
        ], [
            'company.required' => 'Назва компанії є обов\'язковим полем',
            'company.max' => 'Назва компанії не може перевищувати 255 символів',
            'name.required' => 'Ім\'я є обов\'язковим полем',
            'name.max' => 'Ім\'я не може перевищувати 255 символів',
            'phone.required' => 'Номер телефону є обов\'язковим полем',
            'phone.max' => 'Номер телефону не може перевищувати 30 символів',
            'email.required' => 'Email є обов\'язковим полем',
            'email.email' => 'Введіть коректний email',
            'email.max' => 'Email не може перевищувати 255 символів',
            'volume.required' => 'Оберіть приблизний обсяг',
            'volume.max' => 'Значення обсягу занадто довге',
            'comment.max' => 'Коментар не може перевищувати 2000 символів',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->sendB2bTelegramNotification(
                $request->company,
                $request->name,
                $request->phone,
                $request->email,
                $request->volume,
                $request->comment
            );

            return response()->json([
                'success' => true,
                'message' => 'Дякуємо! Наш менеджер зв\'яжеться з вами протягом 2 годин.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Виникла помилка при відправці повідомлення. Спробуйте пізніше.',
            ], 500);
        }
    }

    public function submit(Request $request)
    {
        if ($request->input('popup_modal_id') === '' || $request->input('popup_modal_id') === null) {
            $request->merge(['popup_modal_id' => null]);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:1000',
            'source' => 'nullable|string|max:50',
            'popup_modal_id' => 'nullable|integer|exists:popup_modals,id',
        ], [
            'name.required' => 'Ім\'я є обов\'язковим полем',
            'name.max' => 'Ім\'я не може перевищувати 255 символів',
            'phone.required' => 'Номер телефону є обов\'язковим полем',
            'phone.max' => 'Номер телефону не може перевищувати 20 символів',
            'message.max' => 'Повідомлення не може перевищувати 1000 символів',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Determine form type based on request
            $formType = $this->determineFormType($request);
            
            // Send notification to Telegram
            $this->sendTelegramNotification(
                $request->name,
                $request->phone,
                $request->message,
                $formType,
                $request->source,
                $request->input('popup_modal_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Дякуємо! Ми зв\'яжемося з вами найближчим часом.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Виникла помилка при відправці повідомлення. Спробуйте пізніше.'
            ], 500);
        }
    }

    /**
     * Determine form type based on request
     */
    private function determineFormType($request)
    {
        // Check if it's from promotion modal
        if ($request->has('source') && $request->source === 'promotion_modal') {
            return 'promotion_modal';
        }

        if ($request->has('source') && $request->source === 'scheduled_popup_modal') {
            return 'scheduled_popup_modal';
        }
        
        // Check if it's consultation form (has name_fd field)
        if ($request->has('name_fd')) {
            return 'consultation';
        }
        
        // Check if it's courier form (has message field and specific structure)
        if ($request->has('message') && !$request->has('name_fd')) {
            return 'courier';
        }
        
        // Default to feedback form
        return 'feedback';
    }

    /**
     * Send notification to Telegram
     */
    private function sendTelegramNotification($name, $phone, $message, $formType = 'feedback', $source = null, $popupModalId = null)
    {
        // Check if Telegram notifications are enabled
        if (!config('telegram.enabled', true)) {
            return;
        }
        
        $botToken = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');
        
        // Different messages for different form types
        $formTitles = [
            'feedback' => "🆕 *Нове повідомлення зворотнього зв'язку*",
            'consultation' => "📞 *Заявка на консультацію*",
            'courier' => "🚚 *Заявка на консультацію*",
            'promotion_modal' => "🎁 *Заявка з модального вікна акції*",
            'scheduled_popup_modal' => "🪟 *Заявка з запланованого банерного поп-апу*",
        ];
        
        $text = $formTitles[$formType] . "\n\n";
        $text .= "👤 *Ім'я:* " . $name . "\n";
        $text .= "📞 *Телефон:* " . $phone . "\n";
        
        if (!empty($message)) {
            $text .= "💬 *Повідомлення:* " . $message . "\n";
        }

        if (!empty($popupModalId)) {
            $text .= "🆔 *Поп-ап ID:* " . $popupModalId . "\n";
        }
        
        $text .= "\n⏰ *Час:* " . now()->format('d.m.Y H:i:s');
        
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ];
        
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            \Log::error('Telegram notification failed', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $result
            ]);
            throw new \Exception('Failed to send Telegram notification: ' . $curlError);
        }
        
        return $result;
    }

    /**
     * Send B2B corporate proposal notification to Telegram
     */
    private function sendB2bTelegramNotification(
        $company,
        $name,
        $phone,
        $email,
        $volume,
        $comment = null
    ) {
        if (!config('telegram.enabled', true)) {
            return;
        }

        $botToken = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');

        $text = "🏢 *Корпоративна заявка B2B*\n\n";
        $text .= "🏭 *Компанія:* " . $company . "\n";
        $text .= "👤 *Контакт:* " . $name . "\n";
        $text .= "📞 *Телефон:* " . $phone . "\n";
        $text .= "✉️ *Email:* " . $email . "\n";
        $text .= "📊 *Обсяг на місяць:* " . $volume . "\n";

        if (!empty($comment)) {
            $text .= "💬 *Коментар:* " . $comment . "\n";
        }

        $text .= "\n⏰ *Час:* " . now()->format('d.m.Y H:i:s');

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            \Log::error('Telegram B2B notification failed', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $result,
            ]);
            throw new \Exception('Failed to send Telegram notification: ' . $curlError);
        }

        return $result;
    }

    /**
     * Send courier / pickup order notification to Telegram
     */
    private function sendCourierTelegramNotification(
        $name,
        $phone,
        $type,
        $address = null,
        $date = null,
        $time = null,
        $comment = null
    ) {
        if (!config('telegram.enabled', true)) {
            return;
        }

        $botToken = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');

        $typeLabel = $type === 'courier' ? "Кур'єр до дверей" : 'Самовивіз';

        $text = "🚚 *Заявка на хімчистку*\n\n";
        $text .= "👤 *Ім'я:* " . $name . "\n";
        $text .= "📞 *Телефон:* " . $phone . "\n";
        $text .= "📦 *Спосіб:* " . $typeLabel . "\n";

        if (!empty($address)) {
            $text .= "📍 *Адреса:* " . $address . "\n";
        }

        if (!empty($date)) {
            $text .= "📅 *Дата:* " . $date . "\n";
        }

        if (!empty($time)) {
            $text .= "🕐 *Час:* " . $time . "\n";
        }

        if (!empty($comment)) {
            $text .= "💬 *Коментар:* " . $comment . "\n";
        }

        $text .= "\n⏰ *Час заявки:* " . now()->format('d.m.Y H:i:s');

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            \Log::error('Telegram courier notification failed', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $result,
            ]);
            throw new \Exception('Failed to send Telegram notification: ' . $curlError);
        }

        return $result;
    }
}
