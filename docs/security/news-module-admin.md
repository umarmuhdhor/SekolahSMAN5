# News Module Admin (T013)

Dokumen ini menjelaskan implementasi modul berita admin pada CMS Sekolah.

## Cakupan T013
- CRUD berita pada panel admin (`/admin/news`).
- Lifecycle status berita: `draft`, `published`, `archived`.
- Slug unik server-side sebagai fondasi URL publik.
- Relasi `author` ke `users` dan `cover` opsional ke `media_assets`.
- Enforcement authorization backend via policy.
- Audit event berita:
  - `news.created`
  - `news.updated`
  - `news.deleted`
  - `news.published`
  - `news.unpublished`

## Komponen Utama
- Migration:
  - `database/migrations/2026_04_20_000004_create_news_table.php`
- Model:
  - `app/Modules/News/Models/News.php`
- Status support:
  - `app/Modules/News/Support/NewsStatus.php`
- Slug action:
  - `app/Modules/News/Actions/GenerateUniqueNewsSlugAction.php`
- Policy:
  - `app/Policies/NewsPolicy.php`
- Observer audit:
  - `app/Modules/News/Observers/NewsAuditObserver.php`
- Filament resource:
  - `app/Filament/Resources/News/*`

## Aturan Authorization
- `news.view` untuk akses daftar/detail berita admin.
- `news.create` untuk membuat berita.
- `news.update_own` / `news.update_any` untuk edit berita.
- `news.delete_own` / `news.delete_any` untuk hapus berita.
- `news.publish` wajib untuk aksi publish/unpublish.
- Publish/unpublish diverifikasi server-side (bukan hanya visibilitas tombol UI).

## Aturan Audit
- Create/hapus berita tercatat sebagai event dedicated.
- Update konten non-status tercatat sebagai `news.updated`.
- Perubahan status masuk/keluar `published` tercatat sebagai `news.published` / `news.unpublished`.

## Catatan Integrasi
- Dashboard quick link `News` kini berstatus ready dan mengarah ke resource news.
- Fondasi ini hanya admin module; surface publik berita tetap dikerjakan pada task public frontend.
