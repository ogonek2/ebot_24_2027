<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-site-verification" content="-Tye_Cwi5cK0K8x7A1C8Heuxg5Nmxgjh-H5j3vGd6gQ" />
    
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('storage/src/logo/enot-white-bg.png') }}">
    <link rel="shortcut icon" href="{{ asset('storage/src/logo/logo-enot24.png') }}" type="image/x-icon">
    <title>@yield('title')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Russo+One&display=swap" rel="stylesheet">
    {{-- CSS Styles compiled via Mix --}}
    {{-- Tailwind CSS - используем скомпилированный через Mix (не в manifest, поэтому asset) --}}
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">
    {{-- SwiperJs CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    {{-- For style --}}
    @yield('styles')
    {{-- Seo --}}
    @hasSection('seo_tags')
        @yield('seo_tags')
    @else
        @include('includes.seo.meta', [
            'pageTitle' => trim($__env->yieldContent('title')),
            'pageDescription' => config('seo.default_description'),
            'pageUrl' => seo_canonical(),
        ])
    @endif

    <style>
        body{
            background-color: #F3F2FF !important;
            background-image: url('/storage/src/ill/lines.svg') !important;
            background-size: contain !important;
        }
        
        
        /* Rich text from service editor – списки, ссылки, заголовки и т.д. */
        .rich-text-content {
            font-size: 1rem;
            line-height: 1.7;
            color: #111827;
        }

        .enot-breadcrumbs {
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .enot-breadcrumbs::-webkit-scrollbar {
            height: 4px;
        }

        .enot-breadcrumbs::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .rich-text-content p {
            margin-bottom: 0.75rem;
        }

        .rich-text-content ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .rich-text-content ul ul { list-style-type: circle; }
        .rich-text-content ul ul ul { list-style-type: square; }

        /* Top-level ordered list (custom marker to avoid browser nesting quirks) */
        .rich-text-content ol:not(ol ol) {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0.75rem;
            counter-reset: main;
        }

        .rich-text-content ol:not(ol ol) > li {
            counter-increment: main;
            counter-reset: sub;
            position: relative;
            padding-left: 2.2rem;
            margin-bottom: 0.25rem;
        }

        .rich-text-content ol:not(ol ol) > li::before {
            content: counter(main) ".";
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            text-align: right;
            color: #111827;
        }

        /* Nested ordered lists use 1.1 / 2.1 / 2.2 ... */
        .rich-text-content ol ol {
            list-style: none !important;
            padding-left: 0 !important;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            counter-reset: sub;
        }

        .rich-text-content ol ol > li {
            position: relative;
            padding-left: 2.2rem;
            counter-increment: sub;
        }

        .rich-text-content ol ol > li::marker {
            content: "" !important;
        }

        .rich-text-content ol ol > li::before {
            content: counter(main) "." counter(sub);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            text-align: right;
            color: #111827;
        }

        /* Ensure all nested levels stay numeric (no a/i/A) */
        .rich-text-content ol ol ol,
        .rich-text-content ol ol ol ol,
        .rich-text-content ol ol ol ol ol,
        .rich-text-content ol ol ol ol ol ol {
            list-style: none;
        }

        .rich-text-content li {
            margin-bottom: 0.25rem;
        }

        .rich-text-content a {
            color: #ec4899;
            text-decoration: underline;
        }

        .rich-text-content strong,
        .rich-text-content b {
            font-weight: 600;
        }

        .rich-text-content em,
        .rich-text-content i {
            font-style: italic;
        }

        .rich-text-content h1,
        .rich-text-content h2,
        .rich-text-content h3,
        .rich-text-content h4,
        .rich-text-content h5,
        .rich-text-content h6 {
            font-weight: 700;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
            color: #111827;
            line-height: 1.3;
        }

        .rich-text-content h1 {
            font-size: 1.875rem;
        }

        .rich-text-content h2 {
            font-size: 1.5rem;
        }

        .rich-text-content h3 {
            font-size: 1.25rem;
        }

        .rich-text-content h4 {
            font-size: 1.125rem;
        }

        .rich-text-content h5 {
            font-size: 1rem;
        }

        .rich-text-content h6 {
            font-size: 0.875rem;
        }

        .rich-text-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.9375rem;
        }

        .rich-text-content th,
        .rich-text-content td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            text-align: left;
        }

        .rich-text-content th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        
        /* Отступ для контента, чтобы не перекрывался fixed навбаром */
        .app-container-elements {
            padding-top:40px !important;
        }
        
        @media (max-width: 1280px) {
            .app-container-elements {
                padding-top: 30px !important;
            }
        }
        
        @media (max-width: 768px) {
            .app-container-elements {
                padding-top: 0px !important;
            }
        }
        
        /* Page Loader Styles */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s ease-out, visibility 0.6s ease-out;
            overflow: hidden;
        }
        
        #page-loader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        
        .loader-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            position: relative;
            z-index: 10;
        }
        
        .loader-logo {
            width: 180px;
            height: 180px;
            flex-shrink: 0;
            animation: logoAppear 0.8s ease-out;
        }
        
        @keyframes logoAppear {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .loader-brand {
            display: flex;
            align-items: start;
            gap: 4px;
            overflow: hidden;
            width: 0;
            animation: brandReveal 1.2s ease-out 0.5s forwards;
        }
        
        @keyframes brandReveal {
            0% {
                width: 0;
            }
            100% {
                width: 280px;
            }
        }
        
        .loader-brand-text {
            font-family: 'Namu', sans-serif;
            font-size: 64px;
            font-weight: 700;
            color: #000000;
            white-space: nowrap;
            line-height: 1;
        }
        
        .loader-brand-number {
            font-family: 'Namu', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #000000;
            line-height: 1;
            margin-left: 10px;
        }
        
        /* Bubbles Animation */
        .loader-bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .bubble {
            position: absolute;
            bottom: -100px;
            border-radius: 50%;
            background: rgba(116, 112, 191, 0.1);
            border: 2px solid rgba(116, 112, 191, 0.2);
            animation: bubbleRise 8s infinite ease-in;
        }
        
        .bubble::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 20%;
            width: 30%;
            height: 30%;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
        }
        
        @keyframes bubbleRise {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 0.7;
            }
            90% {
                opacity: 0.7;
            }
            100% {
                transform: translateY(-100vh) translateX(50px);
                opacity: 0;
            }
        }
        
        /* Create multiple bubbles */
        .bubble:nth-child(1) {
            width: 40px;
            height: 40px;
            left: 10%;
            animation-duration: 6s;
            animation-delay: 0s;
        }
        
        .bubble:nth-child(2) {
            width: 60px;
            height: 60px;
            left: 20%;
            animation-duration: 8s;
            animation-delay: 1s;
        }
        
        .bubble:nth-child(3) {
            width: 30px;
            height: 30px;
            left: 35%;
            animation-duration: 7s;
            animation-delay: 0.5s;
        }
        
        .bubble:nth-child(4) {
            width: 50px;
            height: 50px;
            left: 50%;
            animation-duration: 9s;
            animation-delay: 1.5s;
        }
        
        .bubble:nth-child(5) {
            width: 35px;
            height: 35px;
            left: 65%;
            animation-duration: 7.5s;
            animation-delay: 0.8s;
        }
        
        .bubble:nth-child(6) {
            width: 45px;
            height: 45px;
            left: 80%;
            animation-duration: 8.5s;
            animation-delay: 1.2s;
        }
        
        .bubble:nth-child(7) {
            width: 55px;
            height: 55px;
            left: 90%;
            animation-duration: 6.5s;
            animation-delay: 0.3s;
        }
        
        .bubble:nth-child(8) {
            width: 25px;
            height: 25px;
            left: 15%;
            animation-duration: 7s;
            animation-delay: 2s;
        }
        
        .bubble:nth-child(9) {
            width: 40px;
            height: 40px;
            left: 75%;
            animation-duration: 8s;
            animation-delay: 1.8s;
        }
        
        .bubble:nth-child(10) {
            width: 35px;
            height: 35px;
            left: 45%;
            animation-duration: 9s;
            animation-delay: 2.5s;
        }
        
        /* Sparkle/Shine effect on brand text */
        .loader-brand-wrapper {
            position: relative;
            overflow: hidden;
        }
        
        .loader-brand-wrapper::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(116, 112, 191, 0.3), transparent);
            animation: shine 3s infinite 2s;
            pointer-events: none;
        }
        
        @keyframes shine {
            0% {
                left: -100%;
            }
            50% {
                left: 100%;
            }
            100% {
                left: 100%;
            }
        }
        
        /* Additional cleaning effect - steam/vapor */
        .loader-steam {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 5;
        }
        
        .steam-particle {
            position: absolute;
            width: 4px;
            height: 20px;
            background: linear-gradient(to top, rgba(116, 112, 191, 0.3), transparent);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: steamRise 4s infinite ease-out;
        }
        
        .steam-particle:nth-child(1) {
            left: 20%;
            animation-delay: 0s;
        }
        
        .steam-particle:nth-child(2) {
            left: 50%;
            animation-delay: 0.5s;
        }
        
        .steam-particle:nth-child(3) {
            left: 80%;
            animation-delay: 1s;
        }
        
        @keyframes steamRise {
            0% {
                transform: translateY(100vh) scale(0.5);
                opacity: 0;
            }
            20% {
                opacity: 0.6;
            }
            80% {
                opacity: 0.4;
            }
            100% {
                transform: translateY(-20vh) scale(1.5);
                opacity: 0;
            }
        }
        
        /* Cleaning wave effect */
        .loader-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 200px;
            background: linear-gradient(to top, rgba(116, 112, 191, 0.1), transparent);
            animation: waveMove 3s ease-in-out infinite;
            z-index: 1;
        }
        
        @keyframes waveMove {
            0%, 100% {
                transform: translateY(0);
                opacity: 0.3;
            }
            50% {
                transform: translateY(-20px);
                opacity: 0.5;
            }
        }
        
        /* Prevent body scroll during loader */
        body.loading {
            overflow: hidden;
        }
        
        /* Logo transition animation */
        .logo-transition {
            position: fixed;
            z-index: 10000;
            pointer-events: none;
            will-change: transform, opacity;
            /* Smooth easing function for natural movement */
            transition: transform 1.4s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                        opacity 1.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            /* Preserve 3D for better performance */
            transform-style: preserve-3d;
            backface-visibility: hidden;
        }
        
        /* Hide navbar logo initially during transition */
        body.loading #navbar img {
            opacity: 0;
            transition: opacity 0s;
        }
        
        body.loader-complete #navbar img {
            opacity: 1;
            transition: opacity 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.7s;
        }
        
        /* Hide loader content during transition */
        body.loading .loader-content {
            transition: opacity 0.3s ease-out;
        }
    </style>
    
        <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-N29G5VKN');</script>
    <!-- End Google Tag Manager -->
    
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N29G5VKN"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
</head>

<body>
    @if(request()->routeIs('welcome') && config('seo.show_page_loader', true))
    {{-- Page Loader (тільки головна; не блокує LCP на послугах і блозі) --}}
    <div id="page-loader">
        {{-- Wave Effect at Bottom --}}
        <div class="loader-wave"></div>
        
        {{-- Bubbles Background --}}
        <div class="loader-bubbles">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
        </div>
        
        {{-- Steam/Vapor Effect --}}
        <div class="loader-steam">
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
            <div class="steam-particle"></div>
        </div>
        
        {{-- Main Content --}}
        <div class="loader-content">
            <img src="{{ asset('storage/src/logo/nobg_enot24.svg') }}" alt="ЄНОТ" class="loader-logo">
            <div class="loader-brand-wrapper">
                <div class="loader-brand">
                    <span class="loader-brand-text">ЄНОТ</span>
                    <span class="loader-brand-number">24</span>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- First element --}}
    <div id="app">
        @include('includes.windows.feedback_form-fd')
        @include('includes.windows.success-modal')
        <div class="app-container px-0 md:px-6">
            <div class="app-container-navigator">
                @include('includes.fixed.navigator')
            </div>
            <div class="app-container-elements">
                @yield('content')
            </div>
        </div>
        <div class="app-container-footer">
            @include('includes.fixed.footer')
        </div>
        {{-- Vue компоненты монтируются точечно через кастомные теги --}}
        <vue-cart-container></vue-cart-container>
    </div>

    {{-- Back to Top Button --}}
    {{-- <button id="back-to-top" class="fixed bottom-6 right-6 bg-primary hover:bg-accent text-white rounded-full w-12 h-12 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 z-50 opacity-0 invisible">
        <i class="fas fa-arrow-up text-lg"></i>
    </button> --}}

    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    {{-- InputMask --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.9/jquery.inputmask.min.js"
        integrity="sha512-F5Ul1uuyFlGnIT1dk2c4kB4DBdi5wnBJjVhL7gQlGh46Xn0VhvD8kgxLtjdZ5YN83gybk/aASUAlpdoWUjRR3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- SwiperJs CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    {{-- Modern UI JavaScript --}}
    <script src="{{ asset('js/modern-ui.js') }}"></script>
    {{-- Vue.js App - загружается после других скриптов с defer для правильного порядка --}}
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('scripts')
    <script>
        $(document).ready(function() {
            // Hide all modals on page load
            $('.modal').hide();
            
            // Initialize modal functionality
            initializeModals();
        });

        function initializeModals() {
            // Open modal
            $(document).on('click', '.modal_fade', function(e) {
                e.preventDefault();
                let modalId = $(this).data('modal');
                let modal = $(`#modalfade_${modalId}`);
                
                if (modal.length) {
                    openModal(modal);
                }
            });

            // Close modal
            $(document).on('click', '.close-modal-md', function(e) {
                e.preventDefault();
                let modalId = $(this).data('modal');
                let modal = $(`#modalfade_${modalId}`);
                
                if (modal.length) {
                    closeModal(modal);
                }
            });

            // Close modal on overlay click
            $(document).on('click', '.modal .overlay', function(e) {
                e.preventDefault();
                let modal = $(this).closest('.modal');
                closeModal(modal);
            });

            // Close modal on Escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Check for success modal first (higher priority)
                    const successModal = $('#successModal:visible, #successModal:not(.hidden)');
                    if (successModal.length) {
                        closeSuccessModal();
                        return;
                    }
                    
                    // Then check for other modals
                    let openModal = $('.modal:visible, .modal:not(.hidden)').not('#successModal');
                    if (openModal.length) {
                        closeModal(openModal);
                    }
                }
            });

            // Prevent modal content clicks from closing modal
            $(document).on('click', '.modal .modal-container', function(e) {
                e.stopPropagation();
            });
        }

        function openModal(modal) {
            // Remove hidden class (Tailwind) and ensure display
            modal.removeClass('hidden');
            modal.css('display', 'flex');
            
            // Small delay to ensure display is set before animation
            setTimeout(function() {
                // Add active class for animations
                modal.addClass('active');
            }, 10);
            
            // Prevent body scroll
            $('body').addClass('modal-open');
            
            // Focus on first input after animation completes
            setTimeout(function() {
                modal.find('input, textarea, select').first().focus();
            }, 350);
        }

        function closeModal(modal) {
            // Remove active class for fade out animation
            modal.removeClass('active');
            
            // After animation, hide and add hidden class back
            setTimeout(function() {
                modal.css('display', 'none');
                modal.addClass('hidden');
            }, 300);
            
            // Allow body scroll
            $('body').removeClass('modal-open');
            
            // Clear form if it's a feedback modal
            if (modal.hasClass('feedback')) {
                modal.find('form')[0].reset();
                modal.find('.error').removeClass('error');
                modal.find('.error-message').removeClass('show');
                modal.find('#successMessage').removeClass('show');
                modal.find('form').show(); // Show form again if it was hidden
            }
        }

        // Feedback form handling
        $(document).on('submit', '#feedbackForm', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let submitBtn = form.find('#submitBtn');
            let modal = form.closest('.modal');
            
            // Clear previous errors
            form.find('.error').removeClass('error');
            form.find('.error-message').removeClass('show');
            
            // No loading state to reset
            
            // Validate form
            let isValid = true;
            let name = form.find('#name').val().trim();
            let phone = form.find('#phone').val().trim();
            let message = form.find('#message').val().trim();
            
            // Validate name
            if (!name) {
                form.find('#name').addClass('error');
                form.find('#nameError').text('Ім\'я є обов\'язковим полем').addClass('show');
                isValid = false;
            }
            
            // Validate phone
            if (!phone) {
                form.find('#phone').addClass('error');
                form.find('#phoneError').text('Номер телефону є обов\'язковим полем').addClass('show');
                isValid = false;
            } else if (!isValidPhone(phone)) {
                form.find('#phone').addClass('error');
                form.find('#phoneError').text('Введіть коректний номер телефону').addClass('show');
                isValid = false;
            }
            
            if (!isValid) {
                return;
            }
            
            // No loading state - submit immediately
            
            // Send AJAX request
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Hide form and show success message
                    form.hide();
                    modal.find('#successMessage').addClass('show');
                    
                    // Auto close modal after 3 seconds
                    setTimeout(function() {
                        closeModal(modal);
                    }, 3000);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors || {};
                    let errorMessage = xhr.responseJSON?.message || 'Виникла помилка при відправці повідомлення';
                    
                    // Show server validation errors
                    Object.keys(errors).forEach(function(field) {
                        let fieldElement = form.find('#' + field);
                        let errorElement = form.find('#' + field + 'Error');
                        
                        if (fieldElement.length && errorElement.length) {
                            fieldElement.addClass('error');
                            errorElement.text(errors[field][0]).addClass('show');
                        }
                    });
                    
                    // Show general error message if no specific field errors
                    if (Object.keys(errors).length === 0) {
                        alert(errorMessage);
                    }
                    
                    // No loading state to reset
                },
                complete: function() {
                    // No loading state to reset
                }
            });
        });
        
        // Phone validation function
        function isValidPhone(phone) {
            // Clean phone number - remove all non-digits
            let cleanPhone = phone.replace(/\D/g, '');
            
            // Ukrainian phone number patterns
            // +380XXXXXXXXX (12 digits) or 0XXXXXXXXX (10 digits)
            if (cleanPhone.startsWith('380') && cleanPhone.length === 12) {
                return true;
            }
            if (cleanPhone.startsWith('0') && cleanPhone.length === 10) {
                return true;
            }
            
            // Check if it's a complete formatted number with InputMask
            // +380 (XX) XXX-XX-XX = 18 characters total
            if (phone.length === 18 && phone.includes('+380') && phone.includes('(') && phone.includes(')') && !phone.includes('_')) {
                let digits = phone.replace(/\D/g, '');
                return digits.length === 12 && digits.startsWith('380');
            }
            
            return false;
        }
        
        // Initialize phone mask using InputMask library
        $(document).ready(function() {
            $('#phone').inputmask({
                mask: '+380 (99) 999-99-99',
                placeholder: '_',
                showMaskOnHover: false,
                showMaskOnFocus: true,
                clearIncomplete: true,
                onBeforePaste: function (pastedValue, opts) {
                    // Remove all non-digits
                    var processedValue = pastedValue.replace(/\D/g, '');
                    // Remove leading 380 if present
                    if (processedValue.startsWith('380')) {
                        processedValue = processedValue.substring(3);
                    }
                    return processedValue;
                }
            });
        });
        
        // Clear errors when user starts typing in any field
        $(document).on('input', '#feedbackForm input, #feedbackForm textarea', function() {
            $(this).removeClass('error');
            $(this).siblings('.error-message').removeClass('show');
        });
        
        // Clear errors when user starts typing in consultation form fields
        $(document).on('input', '#consultationForm input, #consultationForm textarea', function() {
            $(this).removeClass('error');
            $(this).siblings('.error-message').removeClass('show');
        });
        
        // Clear errors when user starts typing in courier form fields
        $(document).on('input', '#courierForm input, #courierForm textarea', function() {
            $(this).removeClass('error');
            $(this).siblings('.error-message').removeClass('show');
        });
        
        // Clear errors when user starts typing in phone fields (for InputMask)
        $(document).on('keyup', '#phone, #phone_fd, #courier_phone', function() {
            $(this).removeClass('error');
            $(this).siblings('.error-message').removeClass('show');
        });
        
        // Initialize phone masks for all forms
        $(document).ready(function() {
            // Phone mask for feedback modal form
            $('#phone').inputmask({
                mask: '+380 (99) 999-99-99',
                placeholder: '_',
                showMaskOnHover: false,
                showMaskOnFocus: true,
                clearIncomplete: true,
                onBeforePaste: function (pastedValue, opts) {
                    var processedValue = pastedValue.replace(/\D/g, '');
                    if (processedValue.startsWith('380')) {
                        processedValue = processedValue.substring(3);
                    }
                    return processedValue;
                }
            });
            
            // Phone mask for consultation form
            $('#phone_fd').inputmask({
                mask: '+380 (99) 999-99-99',
                placeholder: '_',
                showMaskOnHover: false,
                showMaskOnFocus: true,
                clearIncomplete: true,
                onBeforePaste: function (pastedValue, opts) {
                    var processedValue = pastedValue.replace(/\D/g, '');
                    if (processedValue.startsWith('380')) {
                        processedValue = processedValue.substring(3);
                    }
                    return processedValue;
                }
            });
            
            // Phone mask for courier form
            $('#courier_phone').inputmask({
                mask: '+380 (99) 999-99-99',
                placeholder: '_',
                showMaskOnHover: false,
                showMaskOnFocus: true,
                clearIncomplete: true,
                onBeforePaste: function (pastedValue, opts) {
                    var processedValue = pastedValue.replace(/\D/g, '');
                    if (processedValue.startsWith('380')) {
                        processedValue = processedValue.substring(3);
                    }
                    return processedValue;
                }
            });
        });
        
        // Consultation form handling
        $(document).on('submit', '#consultationForm', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let submitBtn = form.find('#consultationSubmitBtn');
            
            // Clear previous errors
            form.find('.error').removeClass('error');
            form.find('.error-message').removeClass('show');
            
            // Validate form
            let isValid = true;
            let name = form.find('#name_fd').val().trim();
            let phone = form.find('#phone_fd').val().trim();
            
            // Validate name
            if (!name) {
                form.find('#name_fd').addClass('error');
                form.find('#name_fdError').text('Ім\'я є обов\'язковим полем').addClass('show');
                isValid = false;
            }
            
            // Validate phone
            if (!phone) {
                form.find('#phone_fd').addClass('error');
                form.find('#phone_fdError').text('Номер телефону є обов\'язковим полем').addClass('show');
                isValid = false;
            } else if (!isValidPhone(phone)) {
                form.find('#phone_fd').addClass('error');
                form.find('#phone_fdError').text('Введіть коректний номер телефону').addClass('show');
                isValid = false;
            }
            
            if (!isValid) {
                return;
            }
            
            // Send AJAX request
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Show success modal
                    showSuccessModal();
                    form[0].reset();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors || {};
                    let errorMessage = xhr.responseJSON?.message || 'Виникла помилка при відправці повідомлення';
                    
                    // Show server validation errors
                    Object.keys(errors).forEach(function(field) {
                        let fieldElement = form.find('#' + field + '_fd');
                        let errorElement = form.find('#' + field + '_fdError');
                        
                        if (fieldElement.length && errorElement.length) {
                            fieldElement.addClass('error');
                            errorElement.text(errors[field][0]).addClass('show');
                        }
                    });
                    
                    // Show general error message if no specific field errors
                    if (Object.keys(errors).length === 0) {
                        alert(errorMessage);
                    }
                }
            });
        });
        
        // Courier form handling
        $(document).on('submit', '#courierForm', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let submitBtn = form.find('#courierSubmitBtn');
            
            // Clear previous errors
            form.find('.error').removeClass('error');
            form.find('.error-message').removeClass('show');
            
            // Validate form
            let isValid = true;
            let name = form.find('#courier_name').val().trim();
            let phone = form.find('#courier_phone').val().trim();
            let message = form.find('#courier_message').val().trim();
            
            // Validate name
            if (!name) {
                form.find('#courier_name').addClass('error');
                form.find('#courier_nameError').text('Ім\'я є обов\'язковим полем').addClass('show');
                isValid = false;
            }
            
            // Validate phone
            if (!phone) {
                form.find('#courier_phone').addClass('error');
                form.find('#courier_phoneError').text('Номер телефону є обов\'язковим полем').addClass('show');
                isValid = false;
            } else if (!isValidPhone(phone)) {
                form.find('#courier_phone').addClass('error');
                form.find('#courier_phoneError').text('Введіть коректний номер телефону').addClass('show');
                isValid = false;
            }
            
            if (!isValid) {
                return;
            }
            
            // Send AJAX request
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Show success modal
                    showSuccessModal();
                    form[0].reset();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors || {};
                    let errorMessage = xhr.responseJSON?.message || 'Виникла помилка при відправці повідомлення';
                    
                    // Show server validation errors
                    Object.keys(errors).forEach(function(field) {
                        let fieldElement = form.find('#courier_' + field);
                        let errorElement = form.find('#courier_' + field + 'Error');
                        
                        if (fieldElement.length && errorElement.length) {
                            fieldElement.addClass('error');
                            errorElement.text(errors[field][0]).addClass('show');
                        }
                    });
                    
                    // Show general error message if no specific field errors
                    if (Object.keys(errors).length === 0) {
                        alert(errorMessage);
                    }
                }
            });
        });

        // Success Modal Functions
        function showSuccessModal() {
            const modal = $('#successModal');
            modal.removeClass('hidden');
            modal.css('display', 'flex');
            
            // Small delay to ensure display is set before animation
            setTimeout(function() {
                modal.addClass('active');
            }, 10);
            
            // Prevent body scroll
            $('body').addClass('modal-open');
        }

        function closeSuccessModal() {
            const modal = $('#successModal');
            modal.removeClass('active');
            
            // After animation, hide and add hidden class back
            setTimeout(function() {
                modal.css('display', 'none');
                modal.addClass('hidden');
                $('body').removeClass('modal-open');
            }, 500);
        }

        // Close success modal handlers
        $(document).on('click', '#closeSuccessModal', function(e) {
            e.preventDefault();
            closeSuccessModal();
        });

        $(document).on('click', '.success .success-overlay', function(e) {
            e.preventDefault();
            closeSuccessModal();
        });
    </script>
    <script>
        $(document).on('click', '.burger-btn', function() {
            $(this).toggleClass('active')
            $('.fixed-container-burger-bar').toggleClass('fade')
        })
    </script>

    {{-- Back to Top Button Script --}}
    <script>
        $(document).ready(function() {
            // Show/hide back to top button based on scroll position
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#back-to-top').removeClass('opacity-0 invisible').addClass('opacity-100 visible');
                } else {
                    $('#back-to-top').removeClass('opacity-100 visible').addClass('opacity-0 invisible');
                }
            });

            // Smooth scroll to top when button is clicked
            $('#back-to-top').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800, 'easeInOutQuart');
                return false;
            });

            // Add easing function for smooth scroll
            $.easing.easeInOutQuart = function (x, t, b, c, d) {
                if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
                return -c/2 * ((t-=2)*t*t*t - 2) + b;
            };
        });
    </script>
    
    {{-- Reviews Slider Initialization --}}
    <script>
        $(document).ready(function() {
            // Initialize Reviews Swiper
            function initReviewsSwiper() {
                if (typeof Swiper !== 'undefined' && $('.reviews-swiper').length > 0) {
                    const reviewsSwiper = new Swiper('.reviews-swiper', {
                        slidesPerView: 2,
                        spaceBetween: 0,
                        centeredSlides: true,
                        loop: true,
                        autoplay: {
                            delay: 4000,
                            disableOnInteraction: false,
                            pauseOnMouseEnter: true,
                            stopOnLastSlide: false,
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                            dynamicBullets: false,
                        },
                        breakpoints: {
                            640: {
                                slidesPerView: 1,
                                spaceBetween: 10,
                            },
                            768: {
                                slidesPerView: 1,
                                spaceBetween: 10,
                            },
                            1024: {
                                slidesPerView: 2,
                                spaceBetween: 10,
                            }
                        },
                        effect: 'slide',
                        speed: 800,
                        watchSlidesProgress: true,
                        watchSlidesVisibility: true,
                        freeMode: false,
                        allowTouchMove: true,
                        grabCursor: true,
                    });
                    
                    // Event handlers
                    reviewsSwiper.on('autoplayStart', function () {
                        console.log('Reviews autoplay started');
                    });
                    
                    reviewsSwiper.on('slideChange', function () {
                        console.log('Reviews slide changed to:', reviewsSwiper.activeIndex);
                    });
                    
                    // Manual start if needed
                    setTimeout(function() {
                        if (reviewsSwiper && !reviewsSwiper.autoplay.running) {
                            reviewsSwiper.autoplay.start();
                            console.log('Manually started reviews autoplay');
                        }
                    }, 1000);
                    
                    console.log('Reviews Swiper initialized successfully');
                } else if (typeof Swiper === 'undefined') {
                    setTimeout(initReviewsSwiper, 100);
                }
            }
            
            // Start initialization
            initReviewsSwiper();
        });
    </script>

    {{-- Слайдер останніх статей блогу (головна та інші сторінки з include) --}}
    <script>
        $(document).ready(function () {
            function initBlogSliderSwiper() {
                var root = document.querySelector('.blog-slider-swiper');
                if (!root) {
                    return;
                }
                if (typeof Swiper === 'undefined') {
                    setTimeout(initBlogSliderSwiper, 100);
                    return;
                }
                if (root.getAttribute('data-swiper-initialized') === '1') {
                    return;
                }
                root.setAttribute('data-swiper-initialized', '1');
                var paginationEl = root.querySelector('.blog-slider-pagination');
                var opts = {
                    slidesPerView: 1,
                    spaceBetween: 16,
                    loop: false,
                    autoHeight: true,
                    watchOverflow: true,
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 16 },
                        1024: { slidesPerView: 3, spaceBetween: 20 },
                    },
                };
                if (paginationEl) {
                    opts.pagination = { el: paginationEl, clickable: true };
                }
                new Swiper(root, opts);
            }
            initBlogSliderSwiper();
        });
    </script>
    
    <!-- Модальное окно для акций -->
    @include('includes.elements.promotion-modal')
    @include('includes.elements.scheduled-popup-modals')
    @include('includes.page-loader-script')

    {{-- Floating Phone Button --}}
    <button 
        id="floating-phone-btn" 
        class="fixed bottom-6 right-6 z-[9998] w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full shadow-2xl flex items-center justify-center text-white hover:scale-110 transition-all duration-300 modal_fade group relative" 
        data-modal="feedbackmd"
        aria-label="Зателефонувати нам">
        <i class="fas fa-phone text-2xl relative z-10"></i>
        {{-- Notification Badge --}}
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center z-20">
            <span class="absolute w-full h-full bg-red-500 rounded-full animate-ping opacity-75"></span>
            <span class="relative w-2 h-2 bg-white rounded-full"></span>
        </span>
    </button>

    <style>
        /* Floating Phone Button Animation */
        @keyframes phonePulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(176, 168, 254, 0.7),
                            0 0 0 0 rgba(196, 126, 147, 0.7);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(176, 168, 254, 0),
                            0 0 0 30px rgba(196, 126, 147, 0);
            }
        }

        @keyframes phoneRing {
            0%, 100% {
                transform: rotate(0deg);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: rotate(-8deg);
            }
            20%, 40%, 60%, 80% {
                transform: rotate(8deg);
            }
        }

        @keyframes phoneBounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        #floating-phone-btn {
            animation: phonePulse 2s ease-in-out infinite, phoneBounce 2s ease-in-out infinite 0.5s;
        }

        #floating-phone-btn:hover {
            animation: phonePulse 1s ease-in-out infinite, phoneRing 0.4s ease-in-out infinite, phoneBounce 1s ease-in-out infinite;
        }

        /* Mobile responsive - adjust position to avoid navbar */
        @media (max-width: 768px) {
            #floating-phone-btn {
                width: 56px;
                height: 56px;
                bottom: 5rem;
                right: 1rem;
            }
            
            #floating-phone-btn i {
                font-size: 1.5rem;
            }
        }

        /* Very small screens */
        @media (max-width: 480px) {
            #floating-phone-btn {
                width: 52px;
                height: 52px;
                bottom: 4.5rem;
                right: 0.75rem;
            }
        }

        /* Hide on very small screens if needed */
        @media (max-width: 320px) {
            #floating-phone-btn {
                width: 48px;
                height: 48px;
                bottom: 4rem;
                right: 0.5rem;
            }
        }

        /* Ensure button is visible above content but below modals */
        #floating-phone-btn {
            position: fixed !important;
            z-index: 9998 !important;
        }

        /* Hide button when modal is open */
        body.modal-open #floating-phone-btn {
            opacity: 0.5;
            pointer-events: none;
            animation-play-state: paused !important;
        }

        /* Notification badge animation */
        #floating-phone-btn span[class*="bg-red-500"]:first-child {
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>
</body>

</html>
