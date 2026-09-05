# Frontend ↔ Backend

Репозиторий: https://github.com/ogonek2/ebot_24_2027.git

| Branch | Path (локально) | Хост (тест) |
|--------|-----------------|-------------|
| `main` | `frontend/` | https://enot.qpanel-erp.online |
| `api` | Laravel root | https://enot-api.qpanel-erp.online |

Подробнее: [DEPLOY.md](./DEPLOY.md).

## Development

```bash
# Terminal 1 — API
php artisan serve

# Terminal 2 — SPA
cd ../frontend   # или отдельный clone main
npm install && npm run dev
```

`VITE_API_PROXY` в `frontend/.env` → Laravel.

## Production build (SPA)

```bash
cd frontend
npm run build          # → dist/  (для enot.qpanel-erp.online)
# npm run build:laravel  # опционально → public/build/
```

API URL задаётся в `frontend/.env.production` (`VITE_API_URL`).
