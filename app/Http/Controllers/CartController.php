<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Category;
use App\Models\locations;
use App\Models\Order;
use App\Models\RepairItem;

class CartController extends Controller
{
    /**
     * Получить корзину
     */
    public function getCart()
    {
        $built = $this->buildCartPayload(session('cart', []));

        return response()->json([
            'items' => $built['items'],
            'total' => $built['total'],
            'count' => count($built['items']),
        ]);
    }

    /**
     * Добавить товар в корзину
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'repair_item_id' => 'nullable|exists:repair_items,id',
            'quantity' => 'required|integer|min:1',
            'cleaning_type' => 'nullable|in:individual,stream,repair',
        ]);

        $cart = session('cart', []);

        if ($request->filled('repair_item_id')) {
            RepairItem::findOrFail($request->repair_item_id);
            $key = 'repair_' . $request->repair_item_id;
            if (isset($cart[$key])) {
                $cart[$key]['quantity'] += $request->quantity;
            } else {
                $cart[$key] = [
                    'type' => 'repair',
                    'repair_item_id' => (int) $request->repair_item_id,
                    'quantity' => (int) $request->quantity,
                    'cleaning_type' => 'repair',
                ];
            }
        } else {
            $request->validate([
                'service_id' => 'required|exists:services,id',
                'cleaning_type' => 'required|in:individual,stream',
            ]);

            $service = Service::with('categories')->findOrFail($request->service_id);

            if ($request->cleaning_type === 'individual' && (!$service->individual_price || $service->individual_price <= 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Індивідуальна чистка недоступна для цієї послуги'
                ], 400);
            }

            $key = $this->generateCartKey($request->service_id, $request->cleaning_type);

            if (isset($cart[$key])) {
                $cart[$key]['quantity'] += $request->quantity;
            } else {
                $cart[$key] = [
                    'type' => 'service',
                    'service_id' => $request->service_id,
                    'quantity' => $request->quantity,
                    'cleaning_type' => $request->cleaning_type,
                ];
            }
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Товар додано до корзини',
            'cart_count' => count($cart),
        ]);
    }

    /**
     * Обновить количество товара в корзине
     */
    public function updateCart(Request $request, $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);

        if (!isset($cart[$key])) {
            return response()->json([
                'success' => false,
                'message' => 'Товар не знайдено в корзині'
            ], 404);
        }

        $cart[$key]['quantity'] = $request->quantity;
        session(['cart' => $cart]);

        return $this->getCart();
    }

    /**
     * Удалить товар из корзины
     */
    public function removeFromCart($key)
    {
        $cart = session('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session(['cart' => $cart]);
        }

        return $this->getCart();
    }

    /**
     * Очистить корзину
     */
    public function clearCart()
    {
        session(['cart' => []]);
        return response()->json([
            'success' => true,
            'message' => 'Корзина очищена'
        ]);
    }

    /**
     * Effective unit price for cart: sale_* first, else base, else category % on base only when no sale.
     */
    private function resolveCartUnitPrice(Service $service, string $cleaningType): float
    {
        if ($cleaningType === 'individual' && floatval($service->individual_price ?? 0) > 0) {
            $sale = floatval($service->individual_sale_price ?? 0);
            if ($sale > 0) {
                return $sale;
            }
            return floatval($service->individual_price);
        }

        $base = floatval($service->price ?? 0);
        $sale = floatval($service->sale_price ?? 0);
        if ($sale > 0) {
            return $sale;
        }

        $category = $service->categories->first();
        if ($category && $category->hasActiveDiscount()) {
            return floatval($category->calculateDiscountedPrice($base));
        }

        return $base;
    }

    /**
     * @param  array<string, array<string, mixed>>  $cart
     * @return array{items: list<array<string, mixed>>, total: float}
     */
    private function buildCartPayload(array $cart): array
    {
        $cartItems = [];
        $total = 0.0;

        foreach ($cart as $key => $item) {
            $isRepair = ($item['type'] ?? null) === 'repair' || !empty($item['repair_item_id']) || str_starts_with((string) $key, 'repair_');

            if ($isRepair) {
                $repairId = (int) ($item['repair_item_id'] ?? (int) str_replace('repair_', '', (string) $key));
                $repair = RepairItem::with('section.priceList.category')->find($repairId);
                if (!$repair) {
                    continue;
                }
                $price = (float) $repair->price;
                $categoryName = $repair->section?->priceList?->category?->name
                    ?? $repair->section?->title
                    ?? 'Ремонт взуття';

                $cartItems[] = [
                    'key' => $key,
                    'service_id' => null,
                    'repair_item_id' => $repair->id,
                    'service_name' => $repair->name,
                    'category_name' => $categoryName,
                    'category_icon' => null,
                    'quantity' => $item['quantity'],
                    'cleaning_type' => 'repair',
                    'price' => $price,
                    'price_from' => filled($repair->price_prefix),
                    'total' => $price * $item['quantity'],
                ];
                $total += $price * $item['quantity'];
                continue;
            }

            $service = Service::with('categories')->find($item['service_id'] ?? null);
            if (!$service) {
                continue;
            }
            $category = $service->categories->first();
            $price = $this->resolveCartUnitPrice($service, $item['cleaning_type'] ?? 'stream');

            $cartItems[] = [
                'key' => $key,
                'service_id' => $service->id,
                'repair_item_id' => null,
                'service_name' => $service->name,
                'category_name' => $category->name ?? 'Послуга',
                'category_icon' => $category->category_img ?? null,
                'quantity' => $item['quantity'],
                'cleaning_type' => $item['cleaning_type'],
                'price' => $price,
                'price_from' => false,
                'total' => $price * $item['quantity'],
            ];
            $total += $price * $item['quantity'];
        }

        return ['items' => $cartItems, 'total' => $total];
    }

    /**
     * Сгенерировать ключ корзины
     */
    private function generateCartKey($serviceId, $cleaningType)
    {
        return $serviceId . '_' . $cleaningType;
    }

    /**
     * Отправить заказ.
     * Корзина приходит с клиента (browser storage) — цены пересчитываем из БД.
     * Не зависит от session cookies (кросс-домен SPA↔API).
     */
    public function submitOrder(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:40',
            'delivery_method' => 'required|in:self,courier',
            'items' => 'required|array|min:1|max:100',
            'items.*.service_id' => 'nullable|integer|exists:services,id',
            'items.*.repair_item_id' => 'nullable|integer|exists:repair_items,id',
            'items.*.cleaning_type' => 'required|in:individual,stream,repair',
            'items.*.quantity' => 'required|integer|min:1|max:99',
        ];

        if ($request->delivery_method === 'self') {
            $rules['pickup_location_id'] = 'required|exists:locations,id';
        } elseif ($request->delivery_method === 'courier') {
            $rules['delivery_address'] = 'required|string|max:500';
        }

        $validated = $request->validate($rules);

        foreach ($validated['items'] as $idx => $row) {
            $hasService = !empty($row['service_id']);
            $hasRepair = !empty($row['repair_item_id']);
            if ($hasService === $hasRepair) {
                return response()->json([
                    'success' => false,
                    'message' => 'Некоректний склад кошика (позиція #' . ($idx + 1) . ')',
                ], 422);
            }
            if (!empty($row['cleaning_type']) && $row['cleaning_type'] === 'repair' && !$hasRepair) {
                return response()->json([
                    'success' => false,
                    'message' => 'Некоректний склад кошика (ремонт без id)',
                ], 422);
            }
        }

        $cart = $this->normalizeClientCartItems($validated['items']);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Корзина порожня або містить невалідні позиції',
            ], 400);
        }

        $built = $this->buildCartPayload($cart);
        $cartItems = array_map(static function (array $row) {
            return [
                'service_id' => $row['service_id'] ?? null,
                'repair_item_id' => $row['repair_item_id'] ?? null,
                'service_name' => $row['service_name'],
                'category_name' => $row['category_name'],
                'quantity' => $row['quantity'],
                'cleaning_type' => $row['cleaning_type'],
                'price' => $row['price'],
                'price_from' => !empty($row['price_from']),
                'total' => $row['total'],
            ];
        }, $built['items']);
        $total = $built['total'];

        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Корзина порожня',
            ], 400);
        }

        $pickupLocation = null;
        if ($request->delivery_method === 'self' && isset($validated['pickup_location_id'])) {
            $pickupLocation = locations::with('cityRelation')->find($validated['pickup_location_id']);
        }

        $orderId = 'ENOT-' . date('Ymd') . '-' . strtoupper(uniqid());

        $order = Order::create([
            'order_id' => $orderId,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'delivery_method' => $validated['delivery_method'],
            'pickup_location_id' => $validated['pickup_location_id'] ?? null,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'items' => $cartItems,
            'total' => $total,
            'status' => 'new',
        ]);

        $publicOrder = [
            'id' => $orderId,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'delivery_method' => $validated['delivery_method'],
            'pickup_location' => $pickupLocation ? [
                'street' => $pickupLocation->street,
                'city' => $pickupLocation->cityRelation->name ?? 'Невідомо',
                'working_hours' => $pickupLocation->workinghourse ?? '',
            ] : null,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'items' => $cartItems,
            'total' => $total,
            'created_at' => now()->format('d.m.Y H:i'),
        ];

        try {
            $this->sendOrderTelegramNotification($order, $pickupLocation);
        } catch (\Exception $e) {
            \Log::error('Failed to send Telegram notification for order: ' . $orderId, [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
        }

        // Order is persisted in DB before Telegram; response.order is the SPA source of truth
        // (no session cookies — cross-site SPA↔API).
        return response()->json([
            'success' => true,
            'message' => 'Замовлення успішно оформлено',
            'order_id' => $orderId,
            'order' => $publicOrder,
        ]);
    }

    /**
     * Normalize SPA cart lines into internal cart map. Prices are NEVER taken from client.
     *
     * @param  list<array<string, mixed>>  $items
     * @return array<string, array<string, mixed>>
     */
    private function normalizeClientCartItems(array $items): array
    {
        $cart = [];

        foreach ($items as $row) {
            $qty = max(1, min(99, (int) ($row['quantity'] ?? 1)));
            $cleaning = (string) ($row['cleaning_type'] ?? 'stream');
            $repairId = isset($row['repair_item_id']) ? (int) $row['repair_item_id'] : 0;
            $serviceId = isset($row['service_id']) ? (int) $row['service_id'] : 0;

            if ($repairId > 0 || $cleaning === 'repair') {
                if ($repairId <= 0) {
                    continue;
                }
                $key = 'repair_' . $repairId;
                if (isset($cart[$key])) {
                    $cart[$key]['quantity'] += $qty;
                } else {
                    $cart[$key] = [
                        'type' => 'repair',
                        'repair_item_id' => $repairId,
                        'quantity' => $qty,
                        'cleaning_type' => 'repair',
                    ];
                }
                continue;
            }

            if ($serviceId <= 0 || !in_array($cleaning, ['individual', 'stream'], true)) {
                continue;
            }

            $key = $this->generateCartKey($serviceId, $cleaning);
            if (isset($cart[$key])) {
                $cart[$key]['quantity'] += $qty;
            } else {
                $cart[$key] = [
                    'type' => 'service',
                    'service_id' => $serviceId,
                    'quantity' => $qty,
                    'cleaning_type' => $cleaning,
                ];
            }
        }

        return $cart;
    }

    /**
     * Отправить заявку на консультацию (с корзины / legacy endpoint)
     */
    public function submitConsultation(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:40',
            'message' => 'nullable|string|max:1000',
        ], [
            'phone.required' => 'Номер телефону є обов\'язковим полем',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Reuse the contact pipeline so Telegram delivery is guaranteed
        $forward = Request::create('/api/contact', 'POST', [
            'name' => $request->input('name') ?: 'Клієнт',
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
            'source' => 'consultation',
        ]);
        $forward->headers->replace($request->headers->all());

        return app(FeedbackController::class)->submit($forward);
    }

    /**
     * Получить приемные пункты для выпадающего списка
     */
    public function getPickupLocations()
    {
        $locations = locations::with('cityRelation')
            ->orderBy('city')
            ->orderBy('street')
            ->get();

        return response()->json([
            'locations' => $locations->map(function($location) {
                return [
                    'id' => $location->id,
                    'street' => $location->street,
                    'district' => $location->district,
                    'city' => $location->cityRelation->city ?? 'Невідомо',
                    'working_hours' => $location->workinghourse ?? '',
                ];
            })
        ]);
    }

    /**
     * Отримати замовлення за order_id з БД (без session cookies).
     */
    public function getLastOrder(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId || !is_string($orderId)) {
            return response()->json(['order' => null], 404);
        }

        $order = Order::where('order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['order' => null], 404);
        }

        $pickupLocation = null;
        if ($order->delivery_method === 'self' && $order->pickup_location_id) {
            $pickupLocation = locations::with('cityRelation')->find($order->pickup_location_id);
        }

        return response()->json([
            'order' => [
                'id' => $order->order_id,
                'name' => $order->name,
                'phone' => $order->phone,
                'delivery_method' => $order->delivery_method,
                'pickup_location' => $pickupLocation ? [
                    'street' => $pickupLocation->street,
                    'city' => $pickupLocation->cityRelation->name ?? 'Невідомо',
                    'working_hours' => $pickupLocation->workinghourse ?? '',
                ] : null,
                'delivery_address' => $order->delivery_address,
                'items' => $order->items ?? [],
                'total' => (float) $order->total,
                'created_at' => optional($order->created_at)->format('d.m.Y H:i') ?? '',
            ],
        ]);
    }

    /**
     * Скачать инвойс в PDF
     */
    public function downloadInvoice($orderId)
    {
        $order = session('last_order');

        if (!$order || $order['id'] !== $orderId) {
            abort(404, 'Замовлення не знайдено');
        }

        // Для простоты используем HTML-to-PDF через view
        // Можно также использовать библиотеку DomPDF или MPDF
        return view('invoice-pdf', [
            'order' => $order,
        ]);
    }

    /**
     * Отправить уведомление о заказе в Telegram
     */
    private function sendOrderTelegramNotification($order, $pickupLocation = null)
    {
        // Проверяем, включены ли уведомления
        if (!config('telegram.enabled', true)) {
            return;
        }

        $botToken = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');

        if (!$botToken || !$chatId) {
            \Log::warning('Telegram bot token or chat ID not configured');
            return;
        }

        // Формируем текст сообщения
        $text = "🛒 *Нове замовлення!*\n\n";
        $text .= "📋 *Номер замовлення:* " . $order->order_id . "\n\n";
        $text .= "👤 *Клієнт:* " . $order->name . "\n";
        $text .= "📞 *Телефон:* " . $order->phone . "\n\n";

        // Информация о доставке
        if ($order->delivery_method === 'self') {
            $text .= "📍 *Спосіб отримання:* Самовивіз\n";
            if ($pickupLocation) {
                $text .= "🏪 *Приймальний пункт:* " . $pickupLocation->street;
                if ($pickupLocation->cityRelation) {
                    $text .= ", " . $pickupLocation->cityRelation->name;
                }
                $text .= "\n";
            }
        } else {
            $text .= "🚚 *Спосіб отримання:* Кур'єрська доставка\n";
            if ($order->delivery_address) {
                $text .= "📍 *Адреса доставки:* " . $order->delivery_address . "\n";
            }
        }

        $text .= "\n📦 *Товари:*\n";
        foreach ($order->items as $item) {
            $text .= "• " . $item['service_name'];
            if (isset($item['category_name'])) {
                $text .= " (" . $item['category_name'] . ")";
            }
            $text .= "\n";
            $typeLabel = match ($item['cleaning_type'] ?? '') {
                'individual' => 'Індивідуальна',
                'repair' => 'Ремонт',
                default => 'Потокова',
            };
            $pricePrefix = !empty($item['price_from']) ? 'від ' : '';
            $text .= "  Тип: " . $typeLabel . "\n";
            $text .= "  Кількість: " . $item['quantity'] . " × " . $pricePrefix . number_format($item['price'], 0, ',', ' ') . "₴ = " . $pricePrefix . number_format($item['total'], 0, ',', ' ') . "₴\n\n";
        }

        $text .= "💰 *Загальна сума:* " . number_format($order->total, 0, ',', ' ') . "₴\n\n";
        $text .= "⏰ *Час оформлення:* " . $order->created_at->format('d.m.Y H:i:s');

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
            \Log::error('Telegram notification failed for order', [
                'order_id' => $order->order_id,
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $result
            ]);
            throw new \Exception('Failed to send Telegram notification: ' . $curlError);
        }

        return $result;
    }
}

