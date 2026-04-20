# Gallery Module Admin (T015)

Dokumen ini menjelaskan implementasi modul gallery admin pada CMS Sekolah.

## Cakupan T015
- CRUD album gallery pada panel admin (`/admin/galleries`).
- Lifecycle status album: `draft`, `published`, `archived`.
- Slug unik server-side untuk URL publik.
- Manajemen item media per album (add/remove/reorder).
- Urutan item dijaga dengan `sort_order` unik di dalam album.
- Enforcement authorization backend via policy.
- Audit event gallery:
  - `gallery.created`
  - `gallery.updated`
  - `gallery.deleted`
  - `gallery.published`
  - `gallery.unpublished`
  - `gallery.item_added`
  - `gallery.item_removed`
  - `gallery.items_reordered`

## Komponen Utama
- Migrations:
  - `database/migrations/2026_04_20_000006_create_galleries_tables.php`
- Models:
  - `app/Modules/Galleries/Models/Gallery.php`
  - `app/Modules/Galleries/Models/GalleryItem.php`
- Status support:
  - `app/Modules/Galleries/Support/GalleryStatus.php`
- Actions:
  - `app/Modules/Galleries/Actions/GenerateUniqueGallerySlugAction.php`
  - `app/Modules/Galleries/Actions/NormalizeGalleryItemSortOrderAction.php`
- Policy:
  - `app/Policies/GalleryPolicy.php`
- Observers:
  - `app/Modules/Galleries/Observers/GalleryAuditObserver.php`
  - `app/Modules/Galleries/Observers/GalleryItemAuditObserver.php`
- Filament resource:
  - `app/Filament/Resources/Galleries/*`

## Aturan Authorization
- `galleries.view` untuk akses daftar/detail gallery admin.
- `galleries.create` untuk membuat album gallery.
- `galleries.update_own` / `galleries.update_any` untuk edit album.
- `galleries.delete_own` / `galleries.delete_any` untuk hapus album.
- `galleries.publish` wajib untuk aksi publish/unpublish.
- Publish/unpublish diverifikasi server-side (bukan sekadar tombol UI).

## Aturan Data & Sorting
- Setiap item gallery harus merujuk `media_assets` valid.
- Constraint database menjaga `sort_order` unik di dalam album (`gallery_id`, `sort_order`).
- Normalisasi urutan dilakukan untuk memastikan ordering stabil setelah add/remove/reorder.

## Catatan Integrasi
- Dashboard quick link `Galleries` kini berstatus ready dan mengarah ke resource galleries.
- Fondasi ini hanya admin module; halaman publik gallery dilakukan pada task public frontend berikutnya.
