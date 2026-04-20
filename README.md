# CMS Sekolah Dinamis

Repository ini berisi implementasi CMS Sekolah Dinamis berbasis Laravel 13 + Filament 5 dengan arsitektur Modular Monolith.

## Stack Utama (Terkunci)
- Laravel 13
- Filament 5
- Blade SSR
- Alpine.js / Livewire
- PostgreSQL
- Redis
- S3-compatible storage

## Dokumentasi Perencanaan
- Manifest: `project-planning/README.md`
- Roadmap task: `project-planning/19-master-task-roadmap.md`

## Status Eksekusi
- T001: Repository governance baseline selesai.
- T002: Skeleton Laravel + Filament + modular structure baseline selesai.
- T003: Docker Compose local stack selesai.
- T004: Environment configuration baseline selesai.
- T005: CI baseline GitHub Actions selesai.
- T006: Session authentication foundation selesai.
- T007: Role & permission foundation selesai.
- T008: Gate/Policy enforcement selesai.
- T009: Audit log foundation selesai.
- T010: User management module selesai.
- T011: Admin dashboard core selesai.

## Menjalankan Proyek (Default: Docker Compose)
1. Salin environment local:
```bash
cp .env.example .env
```
2. Jalankan stack:
```bash
docker compose up -d --build
```
3. Install dependency + setup app:
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

Akses aplikasi:
- Web: `http://localhost:8000`
- Admin Filament: `http://localhost:8000/admin`

## Menjalankan Tanpa Docker (Opsional)
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

Seed default akan membuat akun super admin berdasarkan variabel:
- `SEED_SUPER_ADMIN_NAME`
- `SEED_SUPER_ADMIN_EMAIL`
- `SEED_SUPER_ADMIN_PASSWORD`

## Catatan Authorization T008
- Akses backend admin panel mewajibkan kombinasi role admin panel (`super_admin`/`guru`) dan permission `dashboard.view`.
- Enforcement dilakukan server-side melalui Laravel Gate/Policy (bukan hanya visibilitas UI).

## Catatan Audit T009
- Event auth inti (`login`, `failed_login`, `logout`) dicatat ke tabel `audit_logs`.
- Mutasi role/permission dasar juga dicatat ke `audit_logs` melalui event Spatie.
- Audit log bersifat append-only di level aplikasi (immutable secara operasional).

## Catatan User Management T010
- Modul users tersedia di admin panel (`/admin/users`) dengan CRUD user + assign role.
- Validasi email unik dan password minimum aktif pada backend.
- User nonaktif tidak dapat mengakses panel admin.
- Penghapusan Super Admin terakhir ditolak oleh policy backend.

## Catatan Dashboard T011
- Dashboard admin memiliki widget ringkasan operasional.
- Quick links modul inti disaring otomatis berdasarkan permission user.
- Modul yang belum diimplementasikan tetap ditandai `Segera Hadir` tanpa membuka akses backend baru.

## Referensi Operasional
- Local docker setup: `docs/local-development-docker.md`
- Environment baseline: `docs/environment-baseline.md`
- CI baseline: `docs/ci-baseline.md`
- Auth session foundation: `docs/security/auth-session-foundation.md`
- Roles & permissions foundation: `docs/security/roles-permissions-foundation.md`
- Gate & policy enforcement: `docs/security/gate-policy-enforcement.md`
- Audit log foundation: `docs/security/audit-log-foundation.md`
- User management foundation: `docs/security/user-management-foundation.md`
- Admin dashboard core: `docs/security/admin-dashboard-core.md`
- Struktur modular: `docs/architecture/modular-monolith-structure.md`

## Workflow
- `1 task = 1 branch = 1 PR`
- Branch: `feature/<task-id>-<slug>`
- `main` selalu deployable
