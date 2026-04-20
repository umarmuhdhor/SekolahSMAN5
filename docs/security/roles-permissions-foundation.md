# Roles & Permissions Foundation (T007)

Dokumen ini mendefinisikan fondasi role dan permission CMS Sekolah menggunakan `spatie/laravel-permission`.

## Cakupan T007
- Integrasi package Spatie permission.
- Seed role wajib: `super_admin`, `guru`, `siswa`, `orang_tua`.
- Seed permission baseline modular.
- Assign permission default ke role.
- Restriksi akses panel admin berdasarkan role foundation.

## Komponen
- Katalog role: `app/Modules/RolesPermissions/Support/RoleName.php`
- Katalog permission: `app/Modules/RolesPermissions/Support/PermissionName.php`
- Seeder baseline: `database/seeders/RolesPermissions/RolesAndPermissionsSeeder.php`

## Role Mapping Default
- `super_admin`: seluruh permission baseline.
- `guru`: permission konten terbatas (own scope) + dashboard/media.
- `siswa`: tanpa permission admin.
- `orang_tua`: tanpa permission admin.

Catatan T010:
- Guru default tidak memiliki `users.*`, sehingga tidak dapat mengakses modul manajemen user.

## Catatan Akses Panel
Untuk panel admin (`/admin`), user harus memiliki role pada daftar `adminPanelRoles()`.
Baseline saat ini: `super_admin`, `guru`.

## Catatan Audit Role/Permission
Sejak T009, event attach/detach role dan permission dicatat ke `audit_logs` melalui event Spatie (`RoleAttached`, `RoleDetached`, `PermissionAttached`, `PermissionDetached`).

## Seed Super Admin Awal
`DatabaseSeeder` membuat akun super admin default (bisa dioverride via env):
- `SEED_SUPER_ADMIN_NAME`
- `SEED_SUPER_ADMIN_EMAIL`
- `SEED_SUPER_ADMIN_PASSWORD`
