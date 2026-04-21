# QA Hardening Media and Settings (T025)

Dokumen ini merangkum hardening QA pada fase T025 dengan fokus media library, theme settings, dan navigation settings.

## Cakupan T025
- Menambahkan regression test edge case upload media berbasis validasi backend.
- Menambahkan hardening test untuk fallback theme logo pada kondisi konfigurasi media invalid.
- Menambahkan hardening test untuk konfigurasi navigation invalid agar tidak bocor ke surface publik.
- Menambahkan verifikasi validasi sort order navigation pada level parent-child.

## Komponen Utama
- Test suite hardening:
  - `tests/Feature/QA/QAHardeningMediaSettingsTest.php`
- Penyesuaian behavior fallback theme:
  - `app/Modules/ThemeSettings/Actions/ResolveActiveThemeSettingsAction.php`

## Skenario Kritis yang Diverifikasi
- Media:
  - file melebihi batas ukuran MIME-specific ditolak server-side,
  - `original_name` dibersihkan dari path traversal dan dipotong aman,
  - checksum media tetap terbentuk.
- Theme:
  - jika logo media terkonfigurasi tetapi file logo tidak tersedia, resolver memakai default logo dan menandai `is_fallback=true`.
- Navigation:
  - link `javascript:` dan konfigurasi item invalid tidak lolos resolver publik,
  - label kosong dinormalisasi ke `Untitled`,
  - depth tree publik dibatasi (maksimal 3 level render),
  - duplikasi `sort_order` pada parent yang sama ditolak.

## Hasil Verifikasi
- Menjalankan test target T025: lulus.
- Menjalankan full test suite: lulus tanpa regresi.
