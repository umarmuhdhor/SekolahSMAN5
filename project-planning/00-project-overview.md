# 00 - Project Overview

## Tujuan Dokumen
Menetapkan konteks proyek, visi implementasi, keputusan teknis final, dan aturan eksekusi lintas sesi agent/developer.

## Ringkasan Proyek
CMS Sekolah Dinamis adalah platform untuk mengelola website sekolah melalui panel admin tanpa coding.
Fokus utama:
- manajemen konten publik sekolah,
- manajemen user CMS,
- manajemen konfigurasi tampilan,
- kontrol hak akses,
- jejak audit aktivitas admin.

## Problem Statement
- Perubahan konten masih bergantung pada developer.
- Branding (logo/warna/menu) tidak terpusat.
- Perubahan data penting belum memiliki audit trail kuat.
- Kontrol akses admin belum granular dan konsisten.

## Hasil yang Diharapkan
- Semua konten website utama bisa dikelola dari panel admin.
- Sistem aman, auditable, dan modular.
- Tim dapat delivery bertahap dengan pola `1 sesi = 1 task`.

## Keputusan Teknis Final (Terkunci)
- Arsitektur: **Modular Monolith**
- Backend: **Laravel 13**
- Admin panel: **Filament 5**
- Frontend publik: **Laravel Blade (SSR)**
- Interaktivitas ringan: **Alpine.js**
- Interaktivitas perlu state: **Livewire**
- Database: **PostgreSQL**
- Auth web: **Session-based authentication**
- Authorization: **Laravel Gates/Policies + spatie/laravel-permission**
- Cache/Queue: **Redis**
- Storage file/media: **S3-compatible**
  - local/dev: **MinIO**
  - staging/prod: **AWS S3** atau **Cloudflare R2**
- Reverse proxy/web server: **Nginx**
- Local env: **Docker Compose**
- CI/CD: **GitHub Actions**
- Source control: **GitHub Repository**

## Prinsip Arsitektur
1. Satu codebase Laravel untuk admin + website publik.
2. Domain dipisah per modul, bukan per layanan terpisah.
3. Security dan auditability lebih penting dari fleksibilitas berlebih.
4. Semua perubahan sensitif wajib melalui authorization + audit log.
5. Perubahan besar arsitektur hanya lewat change request.

## Success Indicators
- >90% update konten publik dilakukan via admin panel.
- Seluruh aksi kritikal tercatat audit log.
- `main` branch selalu deployable.
- Task dapat dikerjakan modular tanpa konflik dependency.

## Referensi Plan Lanjutan
- Scope detail: `01-scope-and-assumptions.md`
- Arsitektur detail: `03-system-architecture.md`
- Roadmap task: `19-master-task-roadmap.md`
