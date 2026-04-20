# Announcement Module Admin (T014)

Dokumen ini menjelaskan implementasi modul pengumuman admin pada CMS Sekolah.

## Cakupan T014
- CRUD pengumuman pada panel admin (`/admin/announcements`).
- Lifecycle status pengumuman: `draft`, `published`, `archived`.
- Slug unik server-side sebagai fondasi URL publik.
- Publish window menggunakan `publish_start_at` dan `publish_end_at`.
- Validasi backend: `publish_end_at` wajib sama atau setelah `publish_start_at`.
- Enforcement authorization backend via policy.
- Audit event pengumuman:
  - `announcement.created`
  - `announcement.updated`
  - `announcement.deleted`
  - `announcement.published`
  - `announcement.unpublished`

## Komponen Utama
- Migration:
  - `database/migrations/2026_04_20_000005_create_announcements_table.php`
- Model:
  - `app/Modules/Announcements/Models/Announcement.php`
- Status support:
  - `app/Modules/Announcements/Support/AnnouncementStatus.php`
- Slug action:
  - `app/Modules/Announcements/Actions/GenerateUniqueAnnouncementSlugAction.php`
- Policy:
  - `app/Policies/AnnouncementPolicy.php`
- Observer audit:
  - `app/Modules/Announcements/Observers/AnnouncementAuditObserver.php`
- Filament resource:
  - `app/Filament/Resources/Announcements/*`

## Aturan Authorization
- `announcements.view` untuk akses daftar/detail pengumuman admin.
- `announcements.create` untuk membuat pengumuman.
- `announcements.update_own` / `announcements.update_any` untuk edit pengumuman.
- `announcements.delete_own` / `announcements.delete_any` untuk hapus pengumuman.
- `announcements.publish` wajib untuk aksi publish/unpublish.
- Publish/unpublish diverifikasi server-side (bukan sekadar visibilitas tombol UI).

## Aturan Visibilitas Publik
Pengumuman dianggap layak tampil publik bila:
- `status = published`,
- `published_at` terisi,
- jika `publish_start_at` diisi maka `publish_start_at <= now()`,
- jika `publish_end_at` diisi maka `publish_end_at >= now()`.

Aturan ini difondasikan di scope model `visibleOnPublic()` untuk dipakai modul public frontend pada task berikutnya.

## Catatan Integrasi
- Dashboard quick link `Announcements` kini berstatus ready dan mengarah ke resource announcements.
- Modul ini tidak mengerjakan segmentasi pengumuman per kelas/angkatan (out-of-scope T014).
