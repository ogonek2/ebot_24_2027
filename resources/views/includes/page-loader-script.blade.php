@if(request()->routeIs('welcome') && config('seo.show_page_loader', true))
<script>
(function () {
    const loader = document.getElementById('page-loader');
    const body = document.body;
    if (!loader) {
        return;
    }

    const storageKey = 'enot_loader_shown_at';
    const ttlMs = {{ max(1, (int) config('seo.page_loader_ttl_days', 7)) }} * 24 * 60 * 60 * 1000;
    const lastShown = parseInt(localStorage.getItem(storageKey) || '0', 10);

    if (lastShown && (Date.now() - lastShown) < ttlMs) {
        loader.remove();
        return;
    }

    body.classList.add('loading');

    let hidden = false;
    const hideLoader = function () {
        if (hidden) {
            return;
        }
        hidden = true;
        loader.classList.add('hidden');
        body.classList.remove('loading');
        localStorage.setItem(storageKey, String(Date.now()));
        setTimeout(function () {
            if (loader.parentNode) {
                loader.remove();
            }
        }, 400);
    };

    if (document.readyState === 'complete') {
        setTimeout(hideLoader, 200);
    } else {
        window.addEventListener('load', function () {
            setTimeout(hideLoader, 200);
        }, { once: true });
        setTimeout(hideLoader, 2500);
    }
})();
</script>
@endif
