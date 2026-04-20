# 16 - Public Frontend Plan

## Tujuan Dokumen
Mendefinisikan implementasi website publik berbasis Blade SSR yang mengonsumsi data CMS.

## Scope
- Layout publik utama (header/footer).
- Homepage dinamis.
- Halaman berita (listing + detail).
- Halaman pengumuman (listing + detail).
- Halaman galeri (listing + detail).

## Teknologi
- Laravel Blade SSR.
- Alpine.js untuk interaksi ringan.
- Livewire hanya jika stateful interaction diperlukan.

## Data Source
- School profile, theme settings, navigation menus.
- News/announcements/galleries status `published` saja.

## Business Rules Publik
- Data unpublished tidak boleh tampil.
- Publish window pengumuman wajib dihormati.
- Fallback data jika konfigurasi kosong.

## Performa Dasar
- Caching query/listing yang relevan.
- Pagination untuk listing konten.
- Optimasi gambar dasar (lazy loading ringan).

## Security Frontend
- Escape output untuk mitigasi XSS.
- Validasi route slug.
- Batasi data exposure pada query publik.

## Dependensi
- `07-news-module-plan.md`
- `10-announcement-module-plan.md`
- `09-gallery-module-plan.md`
- `11-school-profile-settings-plan.md`
- `12-theme-customization-plan.md`
- `13-navigation-management-plan.md`

## Acceptance Criteria
- Semua halaman publik inti render stabil.
- Branding/menu/profile tampil dari konfigurasi admin.
- Hanya konten valid yang tampil publik.
- Smoke test frontend lulus.

## Out of Scope
- Full SPA / headless-first.
- Personalisasi konten berbasis akun pada fase inti.

## Prompt Eksekusi Cepat
```text
Kerjakan frontend publik sesuai 16-public-frontend-plan.md.
Bangun halaman Blade SSR untuk home, berita, pengumuman, dan galeri dengan konsumsi data published + fallback konfigurasi.
Jangan membangun full SPA.
```
