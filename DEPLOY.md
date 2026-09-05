# Deploy — Laravel API (branch `api`)

Репозиторий: https://github.com/ogonek2/ebot_24_2027.git  
Ветка: **`api`**  
Тест: **https://enot-api.qpanel-erp.online**  
Фронт (отдельный хост): **https://enot.qpanel-erp.online** (`main`)

Полная инструкция и таблица URL: см. `DEPLOY.md` в ветке `main` (frontend) или раздел ниже.

---

## Где менять ссылки на PROD

Файл **`.env` на сервере** (не коммитится). Ориентир — `.env.example`.

| Ключ | Сейчас (TEST) | На PROD |
|------|---------------|---------|
| `APP_URL` | `https://enot-api.qpanel-erp.online` | прод-URL API |
| `FRONTEND_URL` | `https://enot.qpanel-erp.online` | прод-URL SPA |
| `SEO_SITE_URL` | `https://enot.qpanel-erp.online` | канонический сайт |
| `CORS_ALLOWED_ORIGINS` | origin SPA | origin прод-SPA |
| `SANCTUM_STATEFUL_DOMAINS` | `enot.qpanel-erp.online` | хост прод-SPA |
| `SESSION_DOMAIN` | `.qpanel-erp.online` | `null` или `.ваш-домен` |
| `SESSION_SAME_SITE` | `none` (кросс-поддомен) | `lax` если один сайт |
| `SESSION_SECURE_COOKIE` | `true` | `true` |

После правок: `php artisan config:clear && php artisan config:cache`.

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
