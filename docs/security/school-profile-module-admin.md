# School Profile Module Admin (T016)

Dokumen ini menjelaskan implementasi modul pengaturan profil sekolah pada admin CMS.

## Cakupan T016
- Pengelolaan profil sekolah pada panel admin (`/admin/school-profile`).
- Pola single-record config (`school_profile`) untuk sumber data institusi.
- Validasi backend untuk data kontak utama.
- Enforcement authorization backend via policy.
- Audit event profil sekolah:
  - `school_profile_update`

## Komponen Utama
- Migration:
  - `database/migrations/2026_04_20_000007_create_school_profile_table.php`
- Model:
  - `app/Modules/SchoolProfile/Models/SchoolProfile.php`
- Policy:
  - `app/Policies/SchoolProfilePolicy.php`
- Observer audit:
  - `app/Modules/SchoolProfile/Observers/SchoolProfileAuditObserver.php`
- Filament resource:
  - `app/Filament/Resources/SchoolProfiles/*`

## Aturan Authorization
- `school_profile.view` untuk akses modul profil sekolah.
- `school_profile.update` untuk create pertama dan update profil.
- User tanpa permission ditolak oleh backend (403), bukan hanya disembunyikan di UI.

## Aturan Data
- Tabel `school_profile` menggunakan pola single-row dengan `singleton_key` unik.
- Email harus valid format email.
- Telepon harus valid pola numerik/karakter telepon.
- Pembuatan record kedua ditolak server-side.

## Aturan Audit
- Setiap update profil sekolah dicatat sebagai `school_profile_update`.
- Snapshot perubahan menyimpan perbedaan `before/after` field yang berubah.

## Catatan Integrasi
- Dashboard quick link `School Profile` kini berstatus ready dan mengarah ke resource profil.
- Data ini disiapkan untuk konsumsi frontend publik pada fase public frontend.
