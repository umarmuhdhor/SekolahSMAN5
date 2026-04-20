# 03 - System Architecture

## Tujuan Dokumen
Menjelaskan blueprint arsitektur sistem CMS Sekolah berbasis Modular Monolith.

## Arsitektur Inti
- **Modular Monolith** dengan satu aplikasi Laravel.
- Admin panel dan frontend publik berada dalam codebase yang sama.
- Batas domain dipisahkan per modul (service, policy, request, event).

## Komponen Utama
- Nginx (reverse proxy)
- Laravel 13 app
- Filament 5 (admin)
- Blade SSR + Alpine/Livewire (public)
- PostgreSQL (data)
- Redis (cache + queue)
- S3-compatible storage (media)

## Boundary Modul (Core)
- Foundation: Auth, Users, RolesPermissions, AuditLogs, MediaLibrary
- Content: News, Announcements, Galleries
- Settings: SchoolProfile, ThemeSettings, NavigationMenus
- Public Presentation: Homepage, list/detail konten

## Alur Kritis
### 1) Auth Flow
1. User admin login via session.
2. Middleware auth memverifikasi session.
3. Permission check via Policy/Gate.

### 2) Content Publish Flow
1. Admin membuat konten status draft.
2. Role berizin publish mengubah status ke published.
3. Cache invalidated.
4. Frontend publik hanya query konten published.

### 3) Media Flow
1. Upload file tervalidasi.
2. File disimpan ke object storage.
3. Metadata disimpan di DB.
4. Perubahan media dicatat di audit log.

### 4) Audit Flow
1. Event aksi penting dipublish.
2. Audit service menyimpan actor, action, before/after, context.

## Environment Separation
- **Local**: Docker Compose + MinIO + DB lokal
- **Staging**: konfigurasi mendekati production untuk UAT
- **Production**: layanan final + backup + monitoring

## Non-Goals Arsitektur
- Tidak membangun microservices.
- Tidak menerapkan headless-first sebagai default.
- Tidak menggunakan JWT untuk auth web utama.

## Referensi
- Detail DB: `04-database-plan.md`
- Security baseline: `17-testing-security-and-deployment-plan.md`
