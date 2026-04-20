# Local Development with Docker Compose (T003)

Dokumen ini adalah baseline environment lokal untuk CMS Sekolah Dinamis.

## Service yang Dijalankan
- `app` (PHP-FPM Laravel)
- `nginx`
- `postgres`
- `redis`
- `minio`
- `minio-init` (inisialisasi bucket)

## Prasyarat
- Docker Engine + Docker Compose plugin tersedia.

## Langkah Menjalankan
1. Salin env baseline:
   ```bash
   cp .env.example .env
   ```
2. Jalankan stack:
   ```bash
   docker compose up -d --build
   ```
3. Install dependency di container app:
   ```bash
   docker compose exec app composer install
   ```
4. Generate key dan migrate:
   ```bash
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate
   ```

## URL Penting
- Aplikasi: `http://localhost:8000`
- Filament Admin: `http://localhost:8000/admin`
- MinIO API: `http://localhost:9000`
- MinIO Console: `http://localhost:9001`

## Verifikasi Cepat
```bash
docker compose ps
docker compose logs -f nginx
docker compose exec app php artisan about
```

## Troubleshooting
- Port bentrok: ubah `APP_PORT`, `DB_FORWARD_PORT`, `REDIS_FORWARD_PORT`, `MINIO_API_PORT`, `MINIO_CONSOLE_PORT` di `.env`.
- Gagal konek DB: pastikan `DB_HOST=postgres` untuk konteks container.
- Gagal upload S3 lokal: pastikan `FILESYSTEM_DISK=s3`, `AWS_ENDPOINT=http://minio:9000`, `AWS_USE_PATH_STYLE_ENDPOINT=true`.

## Catatan
`minio-init` membuat bucket default dan mengatur akses download anonim untuk kebutuhan media publik CMS.
