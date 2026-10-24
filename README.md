# Inventori Gudang

Sistem inventori gudang: REST API Laravel + Sanctum dan antarmuka Vue 3.

| Folder | Isi |
|--------|-----|
| `backend/` | REST API Laravel 13 + Sanctum, test, Dockerfile, Postman collection |
| `frontend/` | Aplikasi Vue 3 (Vite, Vue Router, axios), Dockerfile, konfigurasi Nginx |
| `docker-compose.yml` | Menjalankan db (MySQL), backend, dan frontend |

CI/CD: `.github/workflows/ci.yml` (test backend, build frontend, lalu deploy image ke ghcr.io untuk push ke `main`).

## Menjalankan seluruh sistem dengan Docker

Prasyarat: Docker Desktop (Compose v2).

```bash
cp .env.example .env
# isi APP_KEY (php artisan key:generate --show), DB_PASSWORD, dan DB_ROOT_PASSWORD di .env
docker compose up -d --build
docker compose ps
```

Buka http://localhost:8080 dan login dengan `admin@example.com` / `password123`.

- Frontend (Nginx) di port 8080 meneruskan `/api` ke service `backend` lewat jaringan Compose.
- Backend terhubung ke MySQL lewat nama service `db`.
- Data MySQL tersimpan di named volume `db_data`, jadi aman setelah `docker compose down`
  lalu `docker compose up -d`. Jangan pakai `docker compose down -v` karena akan menghapus volume.
- API juga bisa diuji langsung (Postman) di http://localhost:8000/api.

Perintah berguna: `docker compose logs -f backend`, `docker compose down`.

## Menjalankan tanpa Docker (pengembangan)

```bash
# Terminal 1
cd backend && composer install && cp .env.example .env && php artisan key:generate
php artisan migrate --seed && php artisan serve

# Terminal 2
cd frontend && npm install && cp .env.example .env && npm run dev
```

Buka http://localhost:5173.

## Test dan build

```bash
cd backend && php artisan test
cd frontend && npm ci && npm run build
```

## Alur kerja Git

Perubahan masuk ke `dev` dan `main` lewat Pull Request. Branch fitur dibuat dari `dev`
dengan awalan `feature/`.
