@extends('layouts.app')

@section('title')
    Оформлення замовлення - Екочистка одягу та домашнього текстилю
@endsection

@section('seo_tags')
    <meta name="robots" content="{{ config('seo.robots_noindex') }}">
@endsection

@php
    $breadcrumbs = [
        breadcrumb_home(),
        ['name' => 'Послуги та ціни', 'url' => route('services')],
        ['name' => 'Оформлення замовлення'],
    ];
@endphp

@section('content')
    <div class="py-8 md:py-12">
        <div class="container mx-auto px-4 md:px-8">
            @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'wrapperClass' => 'px-0', 'withSchema' => false])

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8">Оформлення замовлення</h1>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Checkout Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Контактна інформація</h2>
                        
                        <form id="checkout-form" class="space-y-6">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Ваше ім'я <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                    placeholder="Іван Іванов">
                            </div>
                            
                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Номер телефону <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                    placeholder="+380991234567">
                            </div>
                            
                            {{-- Delivery Method --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Спосіб отримання <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary transition-colors">
                                        <input 
                                            type="radio" 
                                            name="delivery_method" 
                                            value="self" 
                                            required
                                            class="mr-3 text-primary"
                                            checked>
                                        <div class="flex-1">
                                            <span class="font-semibold text-gray-800 block">Занесу сам</span>
                                            <span class="text-sm text-gray-500">Самовивіз з приймального пункту</span>
                                        </div>
                                    </label>
                                    
                                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-primary transition-colors">
                                        <input 
                                            type="radio" 
                                            name="delivery_method" 
                                            value="courier" 
                                            required
                                            class="mr-3 text-primary">
                                        <div class="flex-1">
                                            <span class="font-semibold text-gray-800 block">Кур'єрська доставка</span>
                                            <span class="text-sm text-gray-500">Доставка за вашою адресою</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            {{-- Pickup Location (conditional) - Custom Select with Search --}}
                            <div id="pickup-location-container">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Приймальний пункт <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    {{-- Hidden input for form submission --}}
                                    <input type="hidden" id="pickup_location_id" name="pickup_location_id" required>
                                    
                                    {{-- Custom Select Button --}}
                                    <button 
                                        type="button"
                                        id="pickup-location-toggle"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-left bg-white hover:border-primary/50 flex items-center justify-between">
                                        <span id="pickup-location-display" class="text-gray-500">Оберіть приймальний пункт...</span>
                                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="pickup-location-arrow"></i>
                                    </button>
                                    
                                    {{-- Dropdown with Search --}}
                                    <div 
                                        id="pickup-location-dropdown"
                                        class="hidden absolute z-50 w-full mt-2 bg-white border-2 border-gray-200 rounded-lg shadow-xl max-h-80 overflow-hidden">
                                        {{-- Search Input --}}
                                        <div class="p-3 border-b border-gray-200 bg-gray-50">
                                            <input 
                                                type="text"
                                                id="pickup-location-search"
                                                placeholder="Пошук приймального пункту..."
                                                class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 text-sm">
                                        </div>
                                        
                                        {{-- Options List --}}
                                        <div id="pickup-location-options" class="overflow-y-auto max-h-64">
                                            {{-- Options will be loaded here --}}
                                        </div>
                                        
                                        {{-- No Results --}}
                                        <div id="pickup-location-no-results" class="hidden p-4 text-center text-gray-500 text-sm">
                                            Нічого не знайдено
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Delivery Address (conditional) --}}
                            <div id="delivery-address-container" class="hidden">
                                <label for="delivery_address" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Адреса доставки <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    id="delivery_address" 
                                    name="delivery_address"
                                    rows="3"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                    placeholder="Введіть повну адресу доставки..."></textarea>
                            </div>
                            
                            {{-- Submit Button --}}
                            <button 
                                type="submit" 
                                id="submit-order-btn"
                                class="w-full py-4 px-6 bg-primary text-white rounded-lg font-semibold text-lg hover:bg-primary/90 transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-transform">
                                Оформити замовлення
                            </button>
                        </form>
                    </div>
                </div>
                
                {{-- Cart Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 sticky top-4">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ваше замовлення</h2>
                        
                        <div id="checkout-cart-items" class="space-y-4">
                            {{-- Cart items will be loaded via JavaScript --}}
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-spinner fa-spin text-4xl mb-3 text-gray-300"></i>
                                <p>Завантаження...</p>
                            </div>
                        </div>
                        
                        <div id="checkout-cart-total" class="mt-6 pt-6 border-t-2 border-gray-200">
                            {{-- Total will be loaded via JavaScript --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Loading Modal --}}
    <div id="loading-modal" class="hidden fixed inset-0 z-[10002] flex items-center justify-center bg-black bg-opacity-60">
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 max-w-md w-full mx-4 text-center">
            {{-- Raccoon Animation --}}
            <div class="mb-6">
                <div class="relative inline-block">
                    <div class="w-24 h-24 md:w-32 md:h-32 mx-auto relative">
                        {{-- Raccoon Face --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center">
                            {{-- Eyes --}}
                            <div class="flex gap-4 -mt-2">
                                <div class="w-3 h-3 bg-black rounded-full animate-blink"></div>
                                <div class="w-3 h-3 bg-black rounded-full animate-blink" style="animation-delay: 0.1s"></div>
                            </div>
                            {{-- Nose --}}
                            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-black rounded-full"></div>
                            {{-- Mask --}}
                            <div class="absolute top-2 left-1/2 transform -translate-x-1/2 w-20 h-10 bg-black rounded-full opacity-30"></div>
                        </div>
                        {{-- Ears --}}
                        <div class="absolute -top-2 -left-2 w-8 h-8 bg-gray-400 rounded-full"></div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gray-400 rounded-full"></div>
                    </div>
                    {{-- Sparkles around raccoon --}}
                    <div class="absolute -top-4 -left-4 w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                    <div class="absolute -bottom-2 -right-4 w-2 h-2 bg-secondary rounded-full animate-pulse" style="animation-delay: 0.3s"></div>
                    <div class="absolute top-1/2 -right-8 w-2 h-2 bg-primary rounded-full animate-pulse" style="animation-delay: 0.6s"></div>
                </div>
            </div>
            
            {{-- Loading Text --}}
            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-3">Очікуйте...</h3>
            <p class="text-gray-600 text-lg mb-4">
                Наші єнотики готують ваше замовлення до відправки! 🦝
            </p>
            
            {{-- Loading Bar --}}
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div id="loading-progress" class="h-full bg-gradient-to-r from-primary via-secondary to-primary rounded-full animate-loading-progress" style="width: 0%"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load cart items
            loadCheckoutCart();
            
            // Load pickup locations
            loadPickupLocations();
            
            // Handle delivery method change
            const deliveryMethodInputs = document.querySelectorAll('input[name="delivery_method"]');
            deliveryMethodInputs.forEach(input => {
                input.addEventListener('change', handleDeliveryMethodChange);
            });
            
            // Handle form submit
            const checkoutForm = document.getElementById('checkout-form');
            checkoutForm.addEventListener('submit', handleFormSubmit);
            
            function loadCheckoutCart() {
                axios.get('/api/cart')
                    .then(response => {
                        const items = response.data.items || [];
                        const total = response.data.total || 0;
                        
                        renderCartItems(items);
                        renderCartTotal(total);
                    })
                    .catch(error => {
                        console.error('Error loading cart:', error);
                        document.getElementById('checkout-cart-items').innerHTML = 
                            '<div class="text-center py-8 text-red-500">Помилка завантаження корзини</div>';
                    });
            }
            
            function renderCartItems(items) {
                const container = document.getElementById('checkout-cart-items');
                
                if (items.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-shopping-cart text-4xl mb-3 text-gray-300"></i>
                            <p>Корзина порожня</p>
                        </div>
                    `;
                    return;
                }
                
                container.innerHTML = items.map(item => `
                    <div class="flex items-start gap-4 pb-4 border-b border-gray-200">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800 mb-1">${escapeHtml(item.service_name)}</h4>
                            <p class="text-xs text-gray-500 mb-1">${escapeHtml(item.category_name)}</p>
                            <p class="text-xs text-gray-500">
                                Тип: ${item.cleaning_type === 'individual' ? 'Індивідуальна' : 'Потокова'}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600 mb-1">Кількість: ${item.quantity}</div>
                            <div class="text-sm text-gray-500">${parseFloat(item.price || 0).toFixed(0)}₴ × ${item.quantity}</div>
                            <div class="font-bold text-primary mt-1">${parseFloat(item.total || 0).toFixed(0)}₴</div>
                        </div>
                    </div>
                `).join('');
            }
            
            function renderCartTotal(total) {
                const container = document.getElementById('checkout-cart-total');
                container.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-semibold text-gray-700">Разом:</span>
                        <span class="text-3xl font-bold text-primary">${parseFloat(total || 0).toFixed(0)}₴</span>
                    </div>
                `;
            }
            
            // Store locations globally
            let allLocations = [];
            let filteredLocations = [];
            
            function loadPickupLocations() {
                // Проверяем существование контейнера перед загрузкой
                const container = document.getElementById('pickup-location-container');
                if (!container || container.classList.contains('hidden')) {
                    // Контейнер скрыт (выбрана доставка курьером), загружаем данные, но не рендерим
                    axios.get('/api/pickup-locations')
                        .then(response => {
                            allLocations = response.data.locations || [];
                            filteredLocations = allLocations;
                        })
                        .catch(error => {
                            console.error('Error loading pickup locations:', error);
                        });
                    return;
                }
                
                axios.get('/api/pickup-locations')
                    .then(response => {
                        allLocations = response.data.locations || [];
                        filteredLocations = allLocations;
                        
                        // Инициализируем селект перед рендерингом
                        initCustomSelect();
                        
                        // Рендерим после небольшой задержки, чтобы убедиться что элементы существуют
                        setTimeout(() => {
                            renderPickupLocations();
                        }, 50);
                    })
                    .catch(error => {
                        console.error('Error loading pickup locations:', error);
                    });
            }
            
            function renderPickupLocations(locations = filteredLocations) {
                const optionsContainer = document.getElementById('pickup-location-options');
                const noResults = document.getElementById('pickup-location-no-results');
                
                // Проверяем существование элементов
                if (!optionsContainer || !noResults) {
                    console.error('Pickup location elements not found');
                    return;
                }
                
                if (locations.length === 0) {
                    optionsContainer.classList.add('hidden');
                    noResults.classList.remove('hidden');
                    return;
                }
                
                optionsContainer.classList.remove('hidden');
                noResults.classList.add('hidden');
                
                optionsContainer.innerHTML = locations.map(location => {
                    const locationText = `${escapeHtml(location.street)}, ${escapeHtml(location.city)}${location.working_hours ? ' (' + escapeHtml(location.working_hours) + ')' : ''}`;
                    return `
                    <button 
                        type="button"
                        class="w-full px-4 py-3 text-left hover:bg-primary/10 transition-colors duration-200 border-b border-gray-100 last:border-b-0 pickup-location-option"
                        data-location-id="${location.id}"
                        data-location-text="${locationText}">
                        <div class="font-semibold text-gray-800">${escapeHtml(location.street)}</div>
                        <div class="text-sm text-gray-600">${escapeHtml(location.city)}</div>
                        ${location.working_hours ? `<div class="text-xs text-gray-500 mt-1">${escapeHtml(location.working_hours)}</div>` : ''}
                    </button>
                `;
                }).join('');
                
                // Add click handlers
                document.querySelectorAll('.pickup-location-option').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const locationId = this.dataset.locationId;
                        const locationText = this.dataset.locationText;
                        
                        const hiddenInput = document.getElementById('pickup_location_id');
                        const displayElement = document.getElementById('pickup-location-display');
                        
                        if (!hiddenInput || !displayElement) {
                            console.error('Required elements not found');
                            return;
                        }
                        
                        hiddenInput.value = locationId;
                        displayElement.textContent = locationText;
                        displayElement.classList.remove('text-gray-500');
                        displayElement.classList.add('text-gray-800', 'font-semibold');
                        
                        togglePickupDropdown();
                    });
                });
            }
            
            function initCustomSelect() {
                const toggle = document.getElementById('pickup-location-toggle');
                const dropdown = document.getElementById('pickup-location-dropdown');
                const searchInput = document.getElementById('pickup-location-search');
                const arrow = document.getElementById('pickup-location-arrow');
                
                // Проверяем существование элементов
                if (!toggle || !dropdown || !searchInput || !arrow) {
                    console.error('Custom select elements not found');
                    return;
                }
                
                // Toggle dropdown
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    togglePickupDropdown();
                });
                
                // Search functionality
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    if (searchTerm === '') {
                        filteredLocations = allLocations;
                    } else {
                        filteredLocations = allLocations.filter(location => {
                            const street = (location.street || '').toLowerCase();
                            const city = (location.city || '').toLowerCase();
                            const workingHours = (location.working_hours || '').toLowerCase();
                            return street.includes(searchTerm) || city.includes(searchTerm) || workingHours.includes(searchTerm);
                        });
                    }
                    
                    renderPickupLocations();
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (toggle && dropdown && !toggle.contains(e.target) && !dropdown.contains(e.target)) {
                        closePickupDropdown();
                    }
                });
            }
            
            function togglePickupDropdown() {
                const dropdown = document.getElementById('pickup-location-dropdown');
                const arrow = document.getElementById('pickup-location-arrow');
                const searchInput = document.getElementById('pickup-location-search');
                
                if (!dropdown || !arrow || !searchInput) {
                    console.error('Dropdown elements not found');
                    return;
                }
                
                if (dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('hidden');
                    arrow.classList.add('rotate-180');
                    // Focus search input
                    setTimeout(() => {
                        if (searchInput) {
                            searchInput.focus();
                        }
                    }, 100);
                } else {
                    closePickupDropdown();
                }
            }
            
            function closePickupDropdown() {
                const dropdown = document.getElementById('pickup-location-dropdown');
                const arrow = document.getElementById('pickup-location-arrow');
                const searchInput = document.getElementById('pickup-location-search');
                
                if (!dropdown || !arrow || !searchInput) {
                    return;
                }
                
                dropdown.classList.add('hidden');
                arrow.classList.remove('rotate-180');
                searchInput.value = '';
                filteredLocations = allLocations;
                renderPickupLocations();
            }
            
            function handleDeliveryMethodChange(e) {
                const method = e.target.value;
                const pickupContainer = document.getElementById('pickup-location-container');
                const deliveryContainer = document.getElementById('delivery-address-container');
                const pickupInput = document.getElementById('pickup_location_id');
                const deliveryAddress = document.getElementById('delivery_address');
                const pickupDisplay = document.getElementById('pickup-location-display');
                
                if (method === 'self') {
                    pickupContainer.classList.remove('hidden');
                    deliveryContainer.classList.add('hidden');
                    
                    if (pickupInput) {
                        pickupInput.setAttribute('required', 'required');
                    }
                    if (deliveryAddress) {
                        deliveryAddress.removeAttribute('required');
                        deliveryAddress.value = '';
                    }
                    
                    // Инициализируем кастомный селект если ещё не инициализирован
                    if (allLocations.length === 0) {
                        loadPickupLocations();
                    } else {
                        // Если данные уже загружены, просто инициализируем селект и рендерим
                        setTimeout(() => {
                            initCustomSelect();
                            renderPickupLocations();
                        }, 50);
                    }
                } else {
                    pickupContainer.classList.add('hidden');
                    deliveryContainer.classList.remove('hidden');
                    
                    if (pickupInput) {
                        pickupInput.removeAttribute('required');
                        pickupInput.value = '';
                    }
                    if (deliveryAddress) {
                        deliveryAddress.setAttribute('required', 'required');
                    }
                    if (pickupDisplay) {
                        pickupDisplay.textContent = 'Оберіть приймальний пункт...';
                        pickupDisplay.classList.remove('text-gray-800', 'font-semibold');
                        pickupDisplay.classList.add('text-gray-500');
                    }
                    
                    closePickupDropdown();
                }
            }
            
            function handleFormSubmit(e) {
                e.preventDefault();
                
                const formData = new FormData(e.target);
                const deliveryMethod = formData.get('delivery_method');
                
                // Подготовка данных с правильной обработкой null значений
                const data = {
                    name: formData.get('name'),
                    phone: formData.get('phone'),
                    delivery_method: deliveryMethod,
                };
                
                // Добавляем поля в зависимости от способа доставки
                if (deliveryMethod === 'self') {
                    const pickupLocationId = formData.get('pickup_location_id');
                    // Убеждаемся, что это не пустая строка
                    if (pickupLocationId && pickupLocationId.trim() !== '') {
                        data.pickup_location_id = pickupLocationId;
                    } else {
                        showNotification('Будь ласка, оберіть приймальний пункт', 'error');
                        return;
                    }
                } else if (deliveryMethod === 'courier') {
                    const deliveryAddress = formData.get('delivery_address');
                    // Убеждаемся, что это не пустая строка
                    if (deliveryAddress && deliveryAddress.trim() !== '') {
                        data.delivery_address = deliveryAddress.trim();
                    } else {
                        showNotification('Будь ласка, введіть адресу доставки', 'error');
                        return;
                    }
                }
                
                // Показываем ошибки валидации если есть
                if (!data.name || !data.phone) {
                    showNotification('Будь ласка, заповніть всі обов\'язкові поля', 'error');
                    return;
                }
                
                // Show loading modal
                showLoadingModal();
                
                const submitBtn = document.getElementById('submit-order-btn');
                submitBtn.disabled = true;
                
                axios.post('/api/order/submit', data)
                    .then(response => {
                        if (response.data.success) {
                            // Hide loading modal after delay
                            setTimeout(() => {
                                hideLoadingModal();
                                // Redirect to thank you page with order ID
                                const orderId = response.data.order_id || response.data.orderId || null;
                                if (orderId) {
                                    window.location.href = `/order-success/${orderId}`;
                                } else {
                                    window.location.href = '/order-success';
                                }
                            }, 1500);
                        } else {
                            hideLoadingModal();
                            showNotification(response.data.message || 'Помилка оформлення замовлення', 'error');
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting order:', error);
                        hideLoadingModal();
                        
                        // Обработка ошибок валидации
                        let message = 'Помилка оформлення замовлення';
                        
                        if (error.response?.data?.errors) {
                            // Собираем все ошибки валидации
                            const errors = error.response.data.errors;
                            const errorMessages = [];
                            
                            for (const field in errors) {
                                if (errors[field]) {
                                    errorMessages.push(errors[field].join(', '));
                                }
                            }
                            
                            message = errorMessages.join('. ') || message;
                        } else if (error.response?.data?.message) {
                            message = error.response.data.message;
                        }
                        
                        showNotification(message, 'error');
                        submitBtn.disabled = false;
                    });
            }
            
            function showLoadingModal() {
                const modal = document.getElementById('loading-modal');
                const progressBar = document.getElementById('loading-progress');
                
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    
                    // Animate progress bar
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += 2;
                        if (progressBar) {
                            progressBar.style.width = progress + '%';
                        }
                        if (progress >= 90) {
                            clearInterval(interval);
                        }
                    }, 50);
                }
            }
            
            function hideLoadingModal() {
                const modal = document.getElementById('loading-modal');
                const progressBar = document.getElementById('loading-progress');
                
                if (modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
                
                if (progressBar) {
                    progressBar.style.width = '100%';
                    setTimeout(() => {
                        progressBar.style.width = '0%';
                    }, 300);
                }
            }
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-20 right-4 z-[10001] px-6 py-4 rounded-lg shadow-lg transition-all duration-300 ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                notification.textContent = message;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
        });
    </script>
    
    <style>
        /* Custom Select Animations */
        #pickup-location-dropdown {
            animation: fadeInDown 0.2s ease-out;
        }
        
        #pickup-location-dropdown.hidden {
            animation: fadeOutUp 0.2s ease-out;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeOutUp {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
        
        /* Scrollbar Styling */
        #pickup-location-options::-webkit-scrollbar {
            width: 6px;
        }
        
        #pickup-location-options::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        #pickup-location-options::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        #pickup-location-options::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Option Hover Effect */
        .pickup-location-option {
            position: relative;
        }
        
        .pickup-location-option::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-color, #7470BF);
            transform: scaleY(0);
            transition: transform 0.2s ease-out;
        }
        
        .pickup-location-option:hover::before {
            transform: scaleY(1);
        }
        
        /* Loading Modal Animations */
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        
        .animate-blink {
            animation: blink 1.5s infinite;
        }
        
        @keyframes loading-progress {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .animate-loading-progress {
            animation: loading-progress 2s ease-in-out infinite;
            background-size: 200% 100%;
        }
    </style>
@endsection

