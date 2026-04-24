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
- T012: Media library foundation selesai.
- T013: News module (admin) selesai.
- T014: Announcement module (admin) selesai.
- T015: Gallery module (admin) selesai.
- T016: School profile module (admin) selesai.
- T017: Theme settings module selesai.
- T018: Navigation management module selesai.
- T019: Audit log admin viewer selesai.
- T020: Public frontend layout + homepage selesai.
- T021: Public news pages selesai.
- T022: Public announcement pages selesai.
- T023: Public gallery pages selesai.
- T024: QA hardening auth/permission/audit selesai.
- T025: QA hardening media/settings selesai.
- T026: Deployment prep staging selesai.
- T027: Production readiness & handover selesai.

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
- Sejak T013, quick link `News` sudah aktif dan mengarah ke `/admin/news`.
- Sejak T014, quick link `Announcements` sudah aktif dan mengarah ke `/admin/announcements`.
- Sejak T015, quick link `Galleries` sudah aktif dan mengarah ke `/admin/galleries`.
- Sejak T016, quick link `School Profile` sudah aktif dan mengarah ke `/admin/school-profile`.
- Sejak T017, quick link `Theme Settings` sudah aktif dan mengarah ke `/admin/theme-settings`.
- Sejak T018, quick link `Navigation` sudah aktif dan mengarah ke `/admin/navigation-menus`.
- Sejak T019, quick link `Audit Logs` sudah aktif dan mengarah ke `/admin/audit-logs`.

## Catatan Media T012
- Modul media library tersedia di admin panel (`/admin/media-assets`).
- Upload media tervalidasi (MIME whitelist, batas ukuran per tipe, dan checksum).
- Replace/delete media melakukan cleanup file di storage dan audit event tercatat.

## Catatan News T013
- Modul news tersedia di admin panel (`/admin/news`) dengan CRUD lengkap.
- Slug berita dijaga unik server-side dan tervalidasi oleh constraint database.
- Publish/unpublish diproteksi backend authorization (`news.publish`) dan tidak hanya dibatasi UI.
- Audit mutasi berita aktif (`news.created`, `news.updated`, `news.deleted`, `news.published`, `news.unpublished`).

## Catatan Announcements T014
- Modul announcements tersedia di admin panel (`/admin/announcements`) dengan CRUD lengkap.
- Validasi publish window aktif: `publish_end_at` wajib sama atau setelah `publish_start_at`.
- Slug pengumuman dijaga unik server-side dan tervalidasi oleh constraint database.
- Publish/unpublish diproteksi backend authorization (`announcements.publish`) dan tidak hanya dibatasi UI.
- Audit mutasi pengumuman aktif (`announcement.created`, `announcement.updated`, `announcement.deleted`, `announcement.published`, `announcement.unpublished`).

## Catatan Galleries T015
- Modul galleries tersedia di admin panel (`/admin/galleries`) dengan CRUD album lengkap.
- Item manager gallery mendukung add/remove/reorder item media per album.
- Sort order item dijaga unik dan stabil di level backend/database.
- Publish/unpublish diproteksi backend authorization (`galleries.publish`) dan tidak hanya dibatasi UI.
- Audit mutasi gallery aktif (`gallery.created`, `gallery.updated`, `gallery.deleted`, `gallery.published`, `gallery.unpublished`, `gallery.item_added`, `gallery.item_removed`, `gallery.items_reordered`).

## Catatan School Profile T016
- Modul school profile tersedia di admin panel (`/admin/school-profile`) sebagai single-record settings.
- Validasi data kontak aktif di backend untuk email dan telepon.
- Pembuatan record profil kedua ditolak server-side (single-row enforcement).
- Update profil diproteksi backend authorization (`school_profile.update`).
- Audit perubahan profil aktif (`school_profile_update`).

## Catatan Theme Settings T017
- Modul theme settings tersedia di admin panel (`/admin/theme-settings`) untuk branding logo + warna.
- Validasi backend HEX 6 digit aktif untuk `primary_color`, `secondary_color`, dan `accent_color`.
- Fallback aman aktif untuk palette warna serta logo default jika konfigurasi kosong/invalid.
- Update theme diproteksi backend authorization (`theme.update`) dan tidak hanya dibatasi UI.
- Audit mutasi theme aktif (`theme.updated`, `theme.logo_changed`).

## Catatan Navigation Management T018
- Modul navigation management tersedia di admin panel (`/admin/navigation-menus`, `/admin/navigation-items`) untuk pengelolaan menu publik.
- Backend menegakkan validasi anti-circular parent-child dan validasi sort order unik per parent.
- Backend menegakkan validasi link item untuk URL/path internal maupun route name Laravel.
- Mutasi navigation diproteksi backend authorization (`navigation.update`) dan tidak hanya dibatasi UI.
- Audit mutasi navigation aktif (`navigation.menu_updated`, `navigation.item_created`, `navigation.item_updated`, `navigation.item_deleted`, `navigation.items_reordered`).

## Catatan Audit Log Viewer T019
- Viewer audit log tersedia di admin panel (`/admin/audit-logs`) dengan mode read-only.
- Filter audit mendukung actor, module, dan rentang tanggal untuk kebutuhan forensic baseline.
- Akses viewer diproteksi backend authorization (`audit.view`) dan tidak hanya dibatasi UI.

## Catatan Public Frontend T020
- Homepage publik tersedia di route root (`/`) dengan layout Blade SSR dinamis.
- Branding publik (logo + warna) menggunakan theme settings aktif dengan fallback aman.
- Informasi profil sekolah (nama, deskripsi, kontak, sosial) dikonsumsi dari school profile dengan fallback aman.
- Navigasi publik header/footer dikonsumsi dari navigation management; item hidden dan route invalid tidak dirender.

## Catatan Public News T021
- Halaman berita publik tersedia di `/berita` (listing) dan `/berita/{slug}` (detail).
- Query publik hanya merender berita dengan status `published` dan `published_at` terisi.
- Detail slug non-published (draft/archived/tanpa tanggal publish) mengembalikan HTTP 404.
- Listing publik dipaginasi (9 item per halaman) dan tetap membawa parameter query.

## Catatan Public Announcements T022
- Halaman pengumuman publik tersedia di `/pengumuman` (listing) dan `/pengumuman/{slug}` (detail).
- Query publik memakai rule `visibleOnPublic()` sehingga hanya menampilkan pengumuman `published` dengan `published_at` terisi dan publish window aktif.
- Detail slug yang tidak lolos visibilitas publik (draft/archived/belum mulai/sudah lewat/tanpa tanggal publish) mengembalikan HTTP 404.
- Listing publik dipaginasi (9 item per halaman) dan tetap membawa parameter query.

## Catatan Public Galleries T023
- Halaman galeri publik tersedia di `/galeri` (listing) dan `/galeri/{slug}` (detail).
- Query publik hanya merender album dengan status `published` dan `published_at` terisi.
- Detail slug non-published (draft/archived/tanpa tanggal publish) mengembalikan HTTP 404.
- Listing publik dipaginasi (9 item per halaman) dan tetap membawa parameter query.
- URL media gallery diselesaikan dengan fallback aman ke placeholder (`/assets/theme/default-gallery-image.svg`) jika item/media tidak tersedia atau file tidak dapat diakses.

## Catatan QA Hardening T024
- Regression test lintas modul ditambahkan untuk memastikan guest selalu diarahkan ke `/admin/login` pada route admin kritikal.
- Matrix permission lintas modul konten ditambahkan untuk memverifikasi role `siswa` ditolak server-side pada route manajemen konten.
- Hardening publish visibility lintas surface publik (`/berita`, `/pengumuman`, `/galeri`) ditambahkan untuk menutup kebocoran konten non-visible.
- Verifikasi audit lifecycle lintas modul konten ditambahkan untuk menjamin event `created/published/unpublished/deleted` tercatat lengkap.

## Catatan QA Hardening T025
- Regression test edge-case media ditambahkan untuk validasi backend ukuran file per MIME pattern, sanitasi nama file, dan checksum.
- Resolver theme aktif diperkuat agar kondisi logo media hilang tetap menandai `is_fallback=true` (bukan hanya mengganti URL logo).
- Regression test navigation ditambahkan untuk menolak konfigurasi link berbahaya/invalid di resolver publik, normalisasi label kosong, dan pembatasan kedalaman tree publik.
- Validasi sort order navigation pada level parent yang sama diverifikasi ulang untuk edge-case sibling duplicate.

## Catatan Deployment Prep T026
- Pipeline deployment staging ditambahkan di GitHub Actions (`.github/workflows/deploy-staging.yml`).
- Job deploy staging menjalankan checkout SHA rilis, install dependency produksi, build asset, migrate, cache optimize, dan queue restart.
- Smoke validation staging diotomasi via `scripts/smoke-staging.sh` untuk endpoint kritikal (`/`, `/berita`, `/pengumuman`, `/galeri`, `/admin/login`).
- Dokumentasi operasional staging deployment dan smoke checklist ditambahkan untuk eksekusi release verification.

## Catatan Production Readiness T027
- Checklist go-live production ditambahkan untuk validasi pra-rilis, cutover, pasca-rilis, dan sign-off internal.
- Runbook operasional production ditambahkan mencakup prosedur deploy, monitoring, klasifikasi incident, dan rollback (aplikasi + database).
- Dokumen handover admin production ditambahkan untuk SOP operasional harian, publikasi konten, manajemen akses, dan audit keamanan.

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
- Media library foundation: `docs/security/media-library-foundation.md`
- News module admin: `docs/security/news-module-admin.md`
- Announcement module admin: `docs/security/announcement-module-admin.md`
- Gallery module admin: `docs/security/gallery-module-admin.md`
- School profile module admin: `docs/security/school-profile-module-admin.md`
- Theme settings module admin: `docs/security/theme-settings-module-admin.md`
- Navigation management module admin: `docs/security/navigation-management-module-admin.md`
- Audit log admin viewer: `docs/security/audit-log-admin-viewer.md`
- QA hardening auth/permission/audit: `docs/security/qa-hardening-auth-permission-audit.md`
- QA hardening media/settings: `docs/security/qa-hardening-media-settings.md`
- Staging deployment baseline: `docs/staging-deployment.md`
- Staging smoke checklist: `docs/staging-smoke-checklist.md`
- Production go-live checklist: `docs/production-go-live-checklist.md`
- Production operations & rollback runbook: `docs/production-operations-runbook.md`
- Production admin handover: `docs/production-admin-handover.md`
- Production sign-off record: `docs/production-signoff-record.md`
- Release PR notes T027: `docs/release-pr-notes-t027.md`
- Struktur modular: `docs/architecture/modular-monolith-structure.md`

## Workflow
- `1 task = 1 branch = 1 PR`
- Branch: `feature/<task-id>-<slug>`
- `main` selalu deployable
