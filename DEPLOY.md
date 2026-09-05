# Deploy — ЄНОТ 24 (2027)

Репозиторий: https://github.com/ogonek2/ebot_24_2027.git

| Branch | Содержимое | Тестовый хост |
|--------|------------|---------------|
| `main` | React SPA (`frontend/`) | https://enot.qpanel-erp.online |
| `api`  | Laravel API (`enot_24_git_version/`) | https://enot-api.qpanel-erp.online |

---

## Где менять ссылки при выходе на PROD

### 1. Frontend (`main`) — файл `.env.production`

```env
# TEST
VITE_API_URL=https://enot-api.qpanel-erp.online

# PROD — заменить на прод-API (часто тот же домен, что и сайт):
# VITE_API_URL=https://enot-24.com.ua
```

После смены: `npm run build` и задеплоить содержимое `dist/`.

Также см. `.env.example` (комментарии).

### 2. API (`api`) — файл `.env` на сервере (не в git)

| Переменная | TEST | PROD (пример) |
|------------|------|----------------|
| `APP_URL` | `https://enot-api.qpanel-erp.online` | `https://enot-24.com.ua` |
| `FRONTEND_URL` | `https://enot.qpanel-erp.online` | `https://enot-24.com.ua` |
| `SEO_SITE_URL` | `https://enot.qpanel-erp.online` | `https://enot-24.com.ua` |
| `CORS_ALLOWED_ORIGINS` | `https://enot.qpanel-erp.online,...` | `https://enot-24.com.ua,...` |
| `SANCTUM_STATEFUL_DOMAINS` | `enot.qpanel-erp.online,...` | `enot-24.com.ua,...` |
| `SESSION_DOMAIN` | `.qpanel-erp.online` | `null` или `.enot-24.com.ua` |
| `SESSION_SAME_SITE` | `none` | `lax` если SPA и API на одном сайте |
| `SESSION_SECURE_COOKIE` | `true` | `true` |

Шаблон: `.env.example` в ветке `api`.

**Важно:** если на проде фронт и API на **одном** домене — `SESSION_SAME_SITE=lax`, `SESSION_DOMAIN=null`, `VITE_API_URL=` (пустой, same-origin).

Если остаются **разные** поддомены — как на тесте: `SESSION_SAME_SITE=none`, `SESSION_SECURE_COOKIE=true`, общий родительский `SESSION_DOMAIN` (иначе session cookie не шарится → CSRF / пуста корзина).

SPA: `GET /sanctum/csrf-cookie` → `GET /api/csrf-token` → заголовок `X-CSRF-TOKEN`.

---

## Деплой API (`api`)

1. Клонировать ветку на сервер:
   ```bash
   git clone -b api https://github.com/ogonek2/ebot_24_2027.git
   cd ebot_24_2027
   ```
2. Скопировать `.env.example` → `.env`, заполнить `APP_KEY`, БД, Telegram.
3. ```bash
   composer install --no-dev -o
   php artisan key:generate   # если ключа ещё нет
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   ```
4. Document root → `public/`.
5. HTTPS + корректные `TrustProxies` у хостинга.

После смены `.env`:
```bash
php artisan config:clear && php artisan config:cache
```

---

## Деплой Frontend (`main`)

1. ```bash
   git clone -b main https://github.com/ogonek2/ebot_24_2027.git enot-frontend
   cd enot-frontend
   npm ci
   npm run build
   ```
2. Залить содержимое `dist/` в document root `enot.qpanel-erp.online`.
3. SPA fallback уже в `public/.htaccess` (попадёт в `dist/`).

### Nginx (если не Apache)

```nginx
location / {
  try_files $uri $uri/ /index.html;
}
```

---

## Локальная разработка

```bash
# API
cd enot_24_git_version && php artisan serve

# Frontend
cd frontend
cp .env.example .env   # VITE_API_PROXY=http://127.0.0.1:8000
npm run dev            # http://127.0.0.1:5173
```

Опционально встроить сборку в Laravel: `npm run build:laravel` → `public/build/`.

---

## Чеклист перед PROD

- [ ] `frontend/.env.production` → прод `VITE_API_URL`
- [ ] API `.env` → `APP_URL`, `FRONTEND_URL`, CORS, Sanctum, Session
- [ ] `npm run build` и выкладка `dist/`
- [ ] `php artisan config:cache` на API
- [ ] Проверить cookie корзины / CSRF с продакшен-домена
- [ ] `storage:link` и доступность `/storage/...` с API-хоста
