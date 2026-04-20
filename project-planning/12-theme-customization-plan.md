# 12 - Theme Customization Plan

## Tujuan Dokumen
Mendefinisikan kustomisasi branding (logo + warna) dari admin panel tanpa coding.

## Scope
- Atur logo situs (dari media library).
- Atur token warna utama (primary, secondary, accent).
- Fallback default jika konfigurasi kosong/invalid.

## Data
- Tabel: `theme_settings`
- Relasi: `logo_media_id -> media_assets.id`

## Business Rules
- Format warna harus valid (HEX).
- Konfigurasi aktif tunggal.
- Perubahan theme harus memicu pembaruan tampilan publik.

## Authorization
- `theme.view`
- `theme.update`

## Audit Events
- theme.updated
- theme.logo_changed

## Risiko dan Mitigasi
- Risiko kontras buruk: sediakan default palette aman.
- Risiko logo tidak tersedia: fallback ke aset logo default.

## Dependensi
- `14-media-management-plan.md`
- `05-authentication-and-authorization-plan.md`
- `16-public-frontend-plan.md`

## Acceptance Criteria
- Admin berizin dapat ganti logo dan warna.
- Validasi input warna bekerja.
- Frontend menggunakan tema aktif dengan fallback aman.
- Event perubahan theme tercatat audit.

## Out of Scope
- Theme builder bebas tanpa batas.

## Prompt Eksekusi Cepat
```text
Kerjakan theme customization sesuai 12-theme-customization-plan.md.
Fokus pada logo + warna, validasi HEX, fallback default, authorization, dan audit event.
Jangan membangun visual builder kompleks.
```
