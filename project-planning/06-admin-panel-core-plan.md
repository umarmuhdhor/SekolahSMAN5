# 06 - Admin Panel Core Plan

## Tujuan Dokumen
Menyediakan fondasi panel admin Filament agar modul lain dapat ditambahkan konsisten.

## Scope Modul
- Dashboard admin dasar.
- Struktur navigasi admin berbasis permission.
- Widget ringkasan operasional minimum.
- Integrasi quick links ke modul inti.

## Halaman/Surface yang Wajib Ada
- Dashboard utama.
- Shortcut ke: News, Announcements, Galleries, Users, Media, Settings, Audit Logs.
- Panel notifikasi internal (opsional ringan).

## Prinsip UX Admin
- Sederhana untuk pengguna non-teknis.
- Aksi penting mudah ditemukan.
- Hindari form terlalu kompleks tanpa validasi jelas.

## Authorization
- Menu item hanya tampil jika permission terkait tersedia.
- Action button di resource harus patuh policy.

## Audit
- Catat akses dashboard (opsional).
- Wajib catat semua mutasi data yang berasal dari panel.

## Dependensi
- `05-authentication-and-authorization-plan.md`
- `14-media-management-plan.md`
- `15-audit-log-plan.md`

## Acceptance Criteria
- Admin dapat login dan melihat dashboard sesuai role.
- Menu modul tersaring otomatis oleh permission.
- Tidak ada menu/action sensitif yang bisa diakses tanpa hak.

## Out of Scope
- BI analytics kompleks.
- Custom UI yang menyimpang dari kebutuhan CMS inti.

## Prompt Eksekusi Cepat
```text
Kerjakan admin panel core mengikuti 06-admin-panel-core-plan.md.
Bangun dashboard dasar, navigation guard berbasis permission, dan struktur menu ke modul inti.
Jangan implementasi fitur modul bisnis detail di task ini.
```
