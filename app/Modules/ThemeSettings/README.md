# Module ThemeSettings

## Tujuan
Mengelola branding visual (logo dan token warna).

## Status
Implementasi admin aktif pada T017:
- pengaturan theme di panel admin (`/admin/theme-settings`),
- validasi backend warna HEX untuk token `primary/secondary/accent`,
- fallback default logo + palette aman saat konfigurasi kosong/invalid,
- audit perubahan tema (`theme.updated`, `theme.logo_changed`).

## Subdirektori
- Actions
- Policies
- DTOs
- Support
