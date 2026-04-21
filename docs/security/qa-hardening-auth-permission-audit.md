# QA Hardening Auth/Permission/Audit (T024)

Dokumen ini merangkum hardening QA lintas modul pada fase T024 dengan fokus auth, permission, publish visibility, dan audit completeness.

## Cakupan T024
- Menambahkan regression test lintas modul untuk rute admin kritikal agar guest selalu diarahkan ke login admin.
- Menambahkan matriks permission lintas modul konten untuk memastikan role non-admin tidak bisa mengakses route manajemen konten.
- Menambahkan test anti publish leakage lintas surface publik (`news`, `announcements`, `galleries`).
- Menambahkan verifikasi audit lifecycle event (`created`, `published`, `unpublished`, `deleted`) untuk modul konten utama.

## Komponen Utama
- Test suite hardening:
  - `tests/Feature/QA/QAHardeningAuthPermissionAuditTest.php`

## Skenario Kritis yang Diverifikasi
- `guest` tidak bisa mengakses route admin kritikal dan selalu diarahkan ke `/admin/login`.
- Role `siswa` ditolak (`403`) untuk route list/edit konten pada modul `news`, `announcements`, dan `galleries`.
- Halaman publik tidak membocorkan konten non-visible:
  - `news`: hanya `published` + `published_at` terisi.
  - `announcements`: harus lolos `visibleOnPublic()` termasuk publish window.
  - `galleries`: hanya `published` + `published_at` terisi.
- Audit event konten lengkap pada lifecycle utama:
  - `news.created`, `news.published`, `news.unpublished`, `news.deleted`
  - `announcement.created`, `announcement.published`, `announcement.unpublished`, `announcement.deleted`
  - `gallery.created`, `gallery.published`, `gallery.unpublished`, `gallery.deleted`

## Hasil Verifikasi
- Menjalankan test target T024: lulus.
- Menjalankan full test suite: lulus tanpa regresi.
