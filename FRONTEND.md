# Backend (API + admin)

Публичный React-сайт живёт в ветке **`main`** репозитория  
https://github.com/ogonek2/ebot_24_2027.git и хостится отдельно.

Этот Laravel-проект (ветка **`api`**) отдаёт:

- JSON API (`/api/...`)
- Filament admin (`/admin`)
- Copywriter dashboard (`/dashboard/copywriter`)
- Sitemap / blog RSS (URL страниц указывают на `SEO_SITE_URL`)

Корень `/` → редирект на `/admin`.

См. [DEPLOY.md](./DEPLOY.md).
