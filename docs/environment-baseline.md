# Environment Baseline (T004)

Dokumen ini menetapkan baseline konfigurasi environment untuk local, staging, dan production.

## File Environment
- `.env.example` -> baseline local docker (PostgreSQL + Redis + MinIO)
- `.env.staging.example` -> baseline staging
- `.env.production.example` -> baseline production

## Prinsip Konfigurasi
1. Jangan commit secret real ke repository.
2. Gunakan PostgreSQL sebagai database utama.
3. Gunakan Redis untuk cache dan queue.
4. Gunakan S3-compatible storage untuk media.
5. Session auth tetap web session-based.

## Matrix Konfigurasi Inti
| Area | Local | Staging | Production |
|---|---|---|---|
| APP_ENV | local | staging | production |
| APP_DEBUG | true | false | false |
| DB_CONNECTION | pgsql | pgsql | pgsql |
| CACHE_STORE | redis | redis | redis |
| QUEUE_CONNECTION | redis | redis | redis |
| FILESYSTEM_DISK | s3 (MinIO) | s3 | s3 |
| AWS_ENDPOINT | `http://minio:9000` | kosong / endpoint cloud | kosong / endpoint cloud |
| AWS_USE_PATH_STYLE_ENDPOINT | true | false (umum) | false (umum) |

## Checklist Validasi T004
- [ ] Variabel environment local/staging/production tersedia.
- [ ] Tidak ada secret real di file template.
- [ ] Konfigurasi cocok dengan stack terkunci master plan.
- [ ] Dokumen setup cukup untuk onboarding developer baru.

## Catatan Security
- Production wajib `APP_DEBUG=false`.
- Gunakan secret manager untuk key sensitif.
- Jangan gunakan kredensial default MinIO di environment selain local.
