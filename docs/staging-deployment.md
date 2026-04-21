# Staging Deployment (T026)

Dokumen ini menjelaskan baseline deployment ke staging untuk CMS Sekolah Dinamis.

## Tujuan
- Menyediakan pipeline deploy staging yang konsisten via GitHub Actions.
- Menjamin deploy tervalidasi oleh smoke test endpoint kritikal.

## Workflow GitHub Actions
- File: `.github/workflows/deploy-staging.yml`
- Trigger:
  - `push` ke branch `main`
  - `workflow_dispatch` (manual)
- Jobs:
  - `deploy-staging`: deploy aplikasi ke server staging via SSH.
  - `smoke-staging`: verifikasi endpoint kritikal setelah deploy.

## Environment GitHub yang Dibutuhkan
Gunakan environment `staging` di GitHub repository settings.

### Secrets (Environment: staging)
- `STAGING_SSH_HOST`
- `STAGING_SSH_PORT` (opsional, default `22`)
- `STAGING_SSH_USER`
- `STAGING_SSH_KEY`
- `STAGING_APP_DIR`

### Variables (Environment: staging)
- `STAGING_APP_URL` (contoh: `https://staging.cms-sekolah.example`)

## Proses Deploy pada Server Staging
Pipeline menjalankan langkah berikut pada server:
1. `git fetch`, checkout commit SHA yang dideploy.
2. `composer install --no-dev --optimize-autoloader`.
3. `npm ci` dan `npm run build`.
4. `php artisan migrate --force` (bisa dilewati saat manual dispatch bila `skip_migrate=true`).
5. Rebuild cache Laravel (`config`, `route`, `view`) dan `queue:restart`.

## Menjalankan Manual Deploy
1. Buka workflow `Deploy Staging` di GitHub Actions.
2. Klik `Run workflow`.
3. Pilih opsi:
   - `run_smoke=true` untuk jalankan smoke test otomatis.
   - `skip_migrate=true` hanya jika dibutuhkan untuk verifikasi non-skema.

## Catatan Keamanan dan Operasional
- SSH key staging disimpan sebagai environment secret, bukan repository secret umum.
- Environment `staging` disarankan memakai required reviewer sebelum eksekusi deploy.
- Semua deploy staging wajib tervalidasi smoke check sebelum dianggap sukses.
