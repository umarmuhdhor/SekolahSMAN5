# Theme Settings Module Admin (T017)

Dokumen ini menjelaskan implementasi modul pengaturan tema (branding logo + warna) pada admin CMS.

## Cakupan T017
- Pengelolaan theme settings pada panel admin (`/admin/theme-settings`).
- Konfigurasi logo dari media library (`logo_media_id`).
- Konfigurasi token warna (`primary_color`, `secondary_color`, `accent_color`).
- Validasi backend warna HEX 6 digit.
- Enforcement authorization backend via policy.
- Audit event perubahan tema:
  - `theme.updated`
  - `theme.logo_changed`
- Fallback default aman saat konfigurasi kosong/tidak valid.

## Komponen Utama
- Migration:
  - `database/migrations/2026_04_20_000008_create_theme_settings_table.php`
- Model:
  - `app/Modules/ThemeSettings/Models/ThemeSetting.php`
- Support & action fallback:
  - `app/Modules/ThemeSettings/Support/ThemeDefaults.php`
  - `app/Modules/ThemeSettings/Actions/ResolveActiveThemeSettingsAction.php`
- Policy:
  - `app/Policies/ThemeSettingPolicy.php`
- Observer audit:
  - `app/Modules/ThemeSettings/Observers/ThemeSettingAuditObserver.php`
- Filament resource:
  - `app/Filament/Resources/ThemeSettings/*`

## Aturan Authorization
- `theme.view` untuk akses daftar/detail theme settings.
- `theme.update` untuk create pertama dan update konfigurasi tema.
- User tanpa permission `theme.*` ditolak backend (403), bukan hanya disembunyikan di UI.

## Aturan Data
- Tabel `theme_settings` mengikuti pola single active config (`singleton_key` unik + `is_active=true`).
- `logo_media_id` opsional dan merujuk `media_assets.id` valid.
- Field warna menerima hanya format `#RRGGBB`.
- Input warna kosong diizinkan untuk memicu fallback palette default aman.

## Aturan Fallback
- Jika tidak ada konfigurasi aktif, sistem mengembalikan default palette + default logo asset.
- Jika warna di database kosong/tidak valid, warna otomatis fallback ke default aman.
- Jika logo tidak tersedia, sistem fallback ke asset logo default (`public/assets/theme/default-school-logo.svg`).

## Catatan Integrasi
- Dashboard quick link `Theme Settings` kini berstatus ready dan mengarah ke modul theme settings.
- Fondasi ini disiapkan untuk konsumsi frontend publik dinamis pada task public frontend berikutnya.
