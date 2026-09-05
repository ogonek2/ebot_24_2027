# Deploy — Laravel API (branch `api`)

Репозиторий: https://github.com/ogonek2/ebot_24_2027.git  
Ветка: **`api`**  
Тест: **https://enot-api.qpanel-erp.online**  
Публичный сайт: **https://enot.qpanel-erp.online** (`main`)

Этот хост — **только API + админка**. Открытие `/` редиректит на `/admin` (Filament).

---

## Где менять ссылки на PROD

Файл **`.env` на сервере** (не коммитится). Ориентир — `.env.example`.

| Ключ | TEST | PROD |
|------|------|------|
| `APP_URL` | `https://enot-api.qpanel-erp.online` | URL API |
| `FRONTEND_URL` | `https://enot.qpanel-erp.online` | URL SPA |
| `SEO_SITE_URL` | `https://enot.qpanel-erp.online` | канонический сайт (sitemap/SEO) |
| `CORS_ALLOWED_ORIGINS` | origin SPA | origin прод-SPA |
| `SANCTUM_STATEFUL_DOMAINS` | `enot.qpanel-erp.online` | хост SPA |
| `SESSION_DOMAIN` | `.qpanel-erp.online` | `null` или `.ваш-домен` |
| `SESSION_SAME_SITE` | `none` | `lax` если один сайт |
| `SESSION_SECURE_COOKIE` | `true` | `true` |

После правок: `php artisan config:clear && php artisan config:cache`.

SPA CSRF: `GET /sanctum/csrf-cookie` → `GET /api/csrf-token` → заголовок `X-CSRF-TOKEN`.  
Для разных поддоменов обязателен родительский `SESSION_DOMAIN` (и `SameSite=none` + `Secure`).

---

## Быстрый деплой

```bash
git clone -b api https://github.com/ogonek2/ebot_24_2027.git
cd ebot_24_2027
cp .env.example .env   # заполнить секреты
composer install --no-dev -o
php artisan key:generate
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

Document root = `public/`.

Админка: `https://enot-api.qpanel-erp.online/admin`  
Копирайтер-дашборд: `/dashboard/copywriter`  
JSON API: `/api/...`
