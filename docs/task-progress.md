# Task Progress Tracker

Referensi roadmap: `project-planning/19-master-task-roadmap.md`

## Status Ringkas
- [x] T001 - Repository Baseline & Branch Rules
- [x] T002 - Laravel 13 Skeleton + Filament 5 Bootstrap
- [x] T003 - Docker Compose Local Stack
- [x] T004 - Environment Configuration Baseline
- [x] T005 - CI Baseline GitHub Actions
- [x] T006 - Session Authentication Foundation
- [x] T007 - Role & Permission Foundation
- [x] T008 - Gate/Policy Enforcement
- [x] T009 - Audit Log Foundation
- [x] T010 - User Management Module
- [x] T011 - Admin Dashboard Core
- [x] T012 - Media Library Foundation
- [x] T013 - News Module (Admin)
- [x] T014 - Announcement Module (Admin)
- [x] T015 - Gallery Module (Admin)
- [x] T016 - School Profile Module (Admin)
- [x] T017 - Theme Settings Module
- [x] T018 - Navigation Management Module
- [x] T019 - Audit Log Admin Viewer
- [x] T020 - Public Frontend Layout + Homepage
- [x] T021 - Public News Pages
- [x] T022 - Public Announcement Pages
- [x] T023 - Public Gallery Pages
- [x] T024 - QA Hardening Auth/Permission/Audit
- [x] T025 - QA Hardening Media and Settings
- [x] T026 - Deployment Prep (Staging)
- [x] T027 - Production Readiness & Handover

## Catatan T002
- Laravel 13 berhasil bootstrap.
- Filament 5 terpasang dengan panel provider `AdminPanelProvider`.
- Route admin tersedia: `/admin`, `/admin/login`, `/admin/logout`.
- Struktur modular baseline dibuat di `app/Modules`.

## Catatan T003
- `docker-compose.yml` dibuat dengan service: app, nginx, postgres, redis, minio, minio-init.
- Dockerfile PHP baseline tersedia di `docker/php/Dockerfile`.
- Nginx config baseline tersedia di `docker/nginx/conf.d/default.conf`.

## Catatan T004
- `.env.example` disesuaikan untuk PostgreSQL + Redis + MinIO.
- Template environment staging dan production dibuat.
- Dokumen baseline environment tersedia di `docs/environment-baseline.md`.

## Catatan T005
- Workflow CI baseline dibuat di `.github/workflows/ci.yml`.
- Job wajib: `lint` (composer validate + pint), `test` (migrate + feature tests).
- Branch protection checklist diperbarui dengan required status checks: `CI / lint`, `CI / test`.

## Catatan T006
- Login admin tetap session-based melalui Filament panel (`/admin/login`).
- Login throttling dapat dikonfigurasi lewat `AUTH_LOGIN_MAX_ATTEMPTS` dan `AUTH_LOGIN_DECAY_SECONDS`.
- Event auth (login, failed_login, logout) dicatat sebagai baseline audit.
- Feature tests auth session dan auth audit logging ditambahkan.

## Catatan T007
- Package `spatie/laravel-permission` terintegrasi dan migration/config dipublish.
- Role wajib `super_admin`, `guru`, `siswa`, `orang_tua` disediakan via seeder foundation.
- Permission baseline modular disediakan via katalog `PermissionName`.
- Akses panel admin dibatasi ke role admin (`super_admin`, `guru`) melalui `User::canAccessPanel()`.
- Seed super admin default ditambahkan pada `DatabaseSeeder`.

## Catatan T008
- Gate dan Policy foundation aktif melalui `AuthServiceProvider`.
- Enforcement backend admin panel memakai gate `panel.access` (bukan hanya pembatasan UI).
- Dashboard admin memakai gate `panel.dashboard.view`.
- Policy foundation (`UserPolicy`, `RolePolicy`, `PermissionPolicy`) disiapkan untuk konsistensi modul lanjutan.
- Feature tests authorization allow/deny ditambahkan untuk memverifikasi pass/fail secara otomatis.

## Catatan T009
- Tabel audit terstruktur `audit_logs` ditambahkan sebagai fondasi append-only.
- Action `RecordAuditLogAction` ditambahkan dengan redaksi data sensitif.
- Event auth (`login`, `failed_login`, `logout`) direkam ke audit table.
- Event mutasi dasar role/permission (`role_change`, `permission_change`) direkam dari event Spatie.
- Feature tests audit foundation ditambahkan untuk verifikasi immutable + pass/fail event logging.

## Catatan T010
- Modul manajemen user admin (`/admin/users`) ditambahkan melalui Filament Resource.
- CRUD user mencakup nama, email unik, password, status aktif (`is_active`), dan assign role.
- Enforcement policy backend aktif untuk users module + guard delete super admin terakhir.
- User nonaktif ditolak mengakses panel admin.
- Audit perubahan user (`user.created`, `user.updated`, `user.status_changed`, `user.deleted`) ditambahkan via observer.
- Feature tests user management ditambahkan untuk skenario allow/deny, validasi email unik, dan audit status/role.

## Catatan T011
- Dashboard admin core diperkuat dengan widget ringkasan operasional.
- Quick links modul inti ditambahkan dan difilter berdasarkan permission user.
- Resource navigation (`Users`) dipastikan mengikuti policy `viewAny` sehingga hanya tampil untuk role berhak.
- Feature tests dashboard core ditambahkan untuk verifikasi filtering quick links dan guard menu.

## Catatan T012
- Modul media library admin (`/admin/media-assets`) ditambahkan melalui Filament Resource.
- Upload media tervalidasi server-side (MIME whitelist + batas ukuran file per tipe + checksum SHA-256).
- Metadata media terstruktur disimpan pada tabel `media_assets`.
- Replace/delete media melakukan cleanup object storage dan mencatat audit event (`media_upload`, `media_replace`, `media_delete`).
- Feature tests media library ditambahkan untuk upload valid, invalid MIME, replace/delete, dan authorization access.

## Catatan T013
- Modul berita admin (`/admin/news`) ditambahkan melalui Filament Resource.
- CRUD berita mencakup relasi author, cover media opsional, status lifecycle (`draft`, `published`, `archived`), dan slug unik server-side.
- Policy `NewsPolicy` aktif untuk enforce `view/create/update/delete/publish` dengan own-vs-any scope.
- Aksi publish/unpublish kini diverifikasi server-side dan tidak bergantung pada UI visibility.
- Audit event berita aktif via observer (`news.created`, `news.updated`, `news.deleted`, `news.published`, `news.unpublished`).
- Feature tests news ditambahkan untuk skenario pass/fail authorization, slug uniqueness, dan audit publish/unpublish.

## Catatan T014
- Modul pengumuman admin (`/admin/announcements`) ditambahkan melalui Filament Resource.
- CRUD pengumuman mencakup status lifecycle (`draft`, `published`, `archived`), publish window (`publish_start_at`, `publish_end_at`), dan slug unik server-side.
- Validasi backend `publish_end_at >= publish_start_at` aktif pada form announcement.
- Policy `AnnouncementPolicy` aktif untuk enforce `view/create/update/delete/publish` dengan own-vs-any scope.
- Aksi publish/unpublish diverifikasi server-side dan tercatat audit event (`announcement.created`, `announcement.updated`, `announcement.deleted`, `announcement.published`, `announcement.unpublished`).
- Scope model `visibleOnPublic()` disiapkan untuk fondasi visibilitas publik berdasarkan status + publish window.
- Feature tests announcement ditambahkan untuk authorization, publish window validation, slug uniqueness, audit event, dan visibilitas publik berbasis waktu.

## Catatan T015
- Modul gallery admin (`/admin/galleries`) ditambahkan melalui Filament Resource.
- CRUD gallery mencakup status lifecycle (`draft`, `published`, `archived`) dan slug unik server-side.
- Item manager gallery aktif (add/remove/reorder) dengan relasi media valid ke `media_assets`.
- Urutan item dijaga stabil dengan `sort_order` unik per album dan normalisasi urutan backend.
- Policy `GalleryPolicy` aktif untuk enforce `view/create/update/delete/publish` dengan own-vs-any scope.
- Aksi publish/unpublish diverifikasi server-side dan tercatat audit event (`gallery.created`, `gallery.updated`, `gallery.deleted`, `gallery.published`, `gallery.unpublished`, `gallery.item_added`, `gallery.item_removed`, `gallery.items_reordered`).
- Scope model `published()` disiapkan sebagai fondasi filter visibilitas publik gallery.
- Feature tests gallery ditambahkan untuk authorization, slug uniqueness, sorting stability, media relation validity, dan audit event.

## Catatan T016
- Modul school profile admin (`/admin/school-profile`) ditambahkan melalui Filament Resource.
- Implementasi memakai pola single-record config table `school_profile` dengan `singleton_key` unik.
- Validasi backend kontak aktif untuk field email dan telepon.
- Policy `SchoolProfilePolicy` aktif untuk enforce `school_profile.view` dan `school_profile.update`.
- Pembuatan record profil kedua ditolak server-side untuk menjaga pola single-row.
- Audit perubahan profil aktif via observer dengan event `school_profile_update`.
- Feature tests school profile ditambahkan untuk skenario allow/deny, validasi kontak, single-record enforcement, dan audit update.

## Catatan T017
- Modul theme settings admin (`/admin/theme-settings`) ditambahkan melalui Filament Resource.
- Implementasi mencakup pemilihan logo dari media library + token warna `primary/secondary/accent`.
- Validasi backend HEX 6 digit aktif pada seluruh input warna.
- Fallback aman aktif melalui `ResolveActiveThemeSettingsAction` untuk logo default dan palette default ketika data kosong/invalid.
- Policy `ThemeSettingPolicy` aktif untuk enforce `theme.view` dan `theme.update`.
- Audit perubahan tema aktif via observer dengan event `theme.updated` dan `theme.logo_changed`.
- Feature tests theme settings ditambahkan untuk skenario authorization allow/deny, validasi pass/fail HEX, fallback, dan audit event.

## Catatan T018
- Modul navigation management admin aktif melalui resource `navigation-menus` dan `navigation-items`.
- CRUD menu + item hierarkis tersedia dengan dukungan parent-child item.
- Validasi backend aktif untuk mencegah circular reference parent-child.
- Validasi sort order unik per parent aktif (termasuk root level) dengan dukungan normalisasi urutan backend.
- Validasi link item mendukung URL/path internal dan route name Laravel yang valid.
- Policy `NavigationMenuPolicy` dan `NavigationItemPolicy` aktif untuk enforce `navigation.view` dan `navigation.update`.
- Audit event navigation aktif via observer (`navigation.menu_updated`, `navigation.item_created`, `navigation.item_updated`, `navigation.item_deleted`, `navigation.items_reordered`).
- Feature tests navigation ditambahkan untuk authorization allow/deny, validasi URL/route, validasi hierarchy/order, serta audit event.

## Catatan T019
- Halaman audit log viewer admin (`/admin/audit-logs`) ditambahkan melalui Filament Resource read-only.
- Viewer mendukung filter actor, module, dan rentang tanggal `created_at`.
- Policy `AuditLogPolicy` dipakai penuh untuk enforce akses `audit.view` pada backend.
- Relasi actor pada model `AuditLog` ditambahkan untuk filter user yang konsisten.
- Dashboard quick link `Audit Logs` kini berstatus ready dan mengarah ke viewer audit logs.
- Feature tests audit log viewer ditambahkan untuk skenario allow/deny dan verifikasi filter actor/module/date.

## Catatan T020
- Public layout frontend dinamis aktif di route root (`/`) melalui Blade SSR.
- Homepage mengonsumsi data theme settings, school profile, dan navigation menu publik dengan fallback aman saat data belum tersedia.
- Item navigasi hidden dan route invalid tidak dirender di frontend publik.
- Feature tests homepage publik ditambahkan untuk fallback, render konfigurasi aktif, dan skip route invalid.

## Catatan T021
- Public news pages aktif pada route `/berita` (listing) dan `/berita/{slug}` (detail).
- Query publik hanya menampilkan news berstatus `published` dengan `published_at` terisi.
- Detail slug non-published (draft/archived/tanpa tanggal publish) ditolak dengan HTTP 404.
- Listing publik dipaginasi 9 item per halaman dan mempertahankan query string.
- Feature tests public news ditambahkan untuk filter visibilitas, empty state, detail published, guard 404, dan pagination.

## Catatan T022
- Public announcement pages aktif pada route `/pengumuman` (listing) dan `/pengumuman/{slug}` (detail).
- Query publik menggunakan scope `visibleOnPublic()` sehingga aturan status publish + publish window konsisten dengan requirement.
- Detail slug yang tidak visible di publik ditolak dengan HTTP 404.
- Listing publik dipaginasi 9 item per halaman dan mempertahankan query string.
- Feature tests public announcements ditambahkan untuk filter visibilitas berbasis publish window, empty state, detail visible, guard 404, dan pagination.

## Catatan T023
- Public gallery pages aktif pada route `/galeri` (listing) dan `/galeri/{slug}` (detail).
- Query publik hanya mengambil gallery berstatus `published` dengan `published_at` terisi.
- Detail slug non-published ditolak dengan HTTP 404.
- Listing publik dipaginasi 9 item per halaman dan mempertahankan query string.
- Resolver media publik gallery menambahkan fallback aman ke placeholder ketika item media kosong, file media tidak tersedia, atau disk tidak dapat diakses.
- Feature tests public galleries ditambahkan untuk filter visibilitas, empty state, detail published, guard 404, pagination, serta fallback media.

## Catatan T024
- Test hardening lintas modul ditambahkan pada `tests/Feature/QA/QAHardeningAuthPermissionAuditTest.php`.
- Guest redirect ke `/admin/login` diverifikasi pada route admin kritikal lintas modul.
- Matrix permission role `siswa` lintas route manajemen konten (`news`, `announcements`, `galleries`) diverifikasi dengan expected `403`.
- Hardening publish leakage lintas surface publik diverifikasi agar konten non-visible tidak bocor.
- Audit completeness lifecycle lintas modul konten diverifikasi untuk event `created/published/unpublished/deleted`.

## Catatan T025
- Test hardening media/settings ditambahkan pada `tests/Feature/QA/QAHardeningMediaSettingsTest.php`.
- Edge-case upload media diverifikasi: batas ukuran MIME-specific, sanitasi nama file, dan konsistensi checksum.
- Resolver theme aktif diperkuat untuk menandai fallback saat logo media tidak dapat diakses (`is_fallback=true`).
- Resolver navigation publik diverifikasi untuk skip item invalid/berbahaya, normalisasi label kosong, dan pembatasan kedalaman tree.
- Validasi sort order navigation pada sibling level diverifikasi ulang untuk mencegah duplikasi order pada parent yang sama.

## Catatan T026
- Pipeline deploy staging ditambahkan pada `.github/workflows/deploy-staging.yml`.
- Deploy job staging menjalankan update source berdasarkan SHA rilis, install dependency produksi, build asset, migrate, optimize cache, dan queue restart.
- Smoke test endpoint kritikal diotomasi melalui `scripts/smoke-staging.sh`.
- Dokumentasi operasional deployment staging ditambahkan di `docs/staging-deployment.md`.
- Checklist validasi pasca-deploy ditambahkan di `docs/staging-smoke-checklist.md`.

## Catatan T027
- Checklist go-live production ditambahkan di `docs/production-go-live-checklist.md`.
- Runbook operasional production + rollback ditambahkan di `docs/production-operations-runbook.md`.
- Dokumen handover admin operasional production ditambahkan di `docs/production-admin-handover.md`.
- Kriteria sign-off internal diformalkan melalui tabel approval pada checklist go-live.
- Template pencatatan keputusan final `GO/NO-GO` ditambahkan di `docs/production-signoff-record.md`.
- Draft PR notes release T027 ditambahkan di `docs/release-pr-notes-t027.md`.
