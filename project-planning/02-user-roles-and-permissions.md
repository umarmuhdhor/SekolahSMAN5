# 02 - User Roles and Permissions

## Tujuan Dokumen
Mendefinisikan model role dan permission untuk kontrol akses admin CMS secara konsisten.

## Role Wajib
- **Super Admin**: kontrol penuh modul dan konfigurasi sistem.
- **Guru**: pengelola konten sesuai permission yang diberikan.
- **Siswa**: konsumen konten publik (read-only, tanpa akses admin).
- **Orang Tua**: konsumen konten publik (read-only, tanpa akses admin).

## Aturan Umum Authorization
- Semua mutasi data harus lolos Gate/Policy server-side.
- Visibilitas menu admin berbasis permission, bukan role hardcoded.
- `deny by default` untuk aksi yang tidak didefinisikan.
- Perubahan role/permission harus tercatat audit log.

## Permission Naming Convention
Gunakan pola: `<modul>.<aksi>`
Contoh:
- `users.view`, `users.create`, `users.update`, `users.delete`
- `news.view`, `news.create`, `news.update`, `news.publish`, `news.delete`
- `theme.update`, `navigation.update`, `audit.view`

## Permission Matrix Ringkas
| Modul/Aksi | Super Admin | Guru | Siswa | Orang Tua |
|---|---|---|---|---|
| Dashboard | view | view | - | - |
| Users | CRUD | view (opsional) | - | - |
| Roles & Permissions | CRUD | - | - | - |
| News | CRUD + publish | create/update-own (+ publish opsional) | view publik | view publik |
| Announcements | CRUD + publish | create/update-own (+ publish opsional) | view publik | view publik |
| Galleries | CRUD + publish | create/update-own (+ publish opsional) | view publik | view publik |
| Media | CRUD | CRUD terbatas | - | - |
| School Profile | CRUD | view/update terbatas (opsional) | view publik | view publik |
| Theme | manage | - | - | - |
| Navigation | manage | - | - | - |
| Audit Logs | view | - | - | - |

## Batasan Data
- Guru default hanya boleh update konten milik sendiri (`own`) kecuali diberi `any`.
- Siswa dan Orang Tua tidak memiliki akses panel admin.

## Event Audit Wajib Terkait Role/Permission
- role assigned/removed
- permission assigned/removed
- privilege escalation/de-escalation

## Referensi
- Auth plan: `05-authentication-and-authorization-plan.md`
- Audit plan: `15-audit-log-plan.md`
