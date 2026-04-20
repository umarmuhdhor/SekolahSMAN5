# Module RolesPermissions

## Tujuan
Mengelola role dan permission granular berbasis `spatie/laravel-permission` sebagai fondasi authorization CMS Sekolah.

## Status
Baseline T007 selesai: role wajib, permission katalog, dan seeder foundation sudah tersedia.

## Komponen Inti
- `Support/RoleName.php`: konstanta role wajib dan daftar role akses panel admin.
- `Support/PermissionName.php`: katalog permission modular dan mapping default role guru.
- `database/seeders/RolesPermissions/RolesAndPermissionsSeeder.php`: seed role + permission baseline.

## Aturan Implementasi
1. Nama permission wajib pola `<module>.<action>`.
2. Role `super_admin` mendapatkan seluruh permission baseline.
3. Role `guru` menggunakan subset permission default, fokus own-content.
4. Role `siswa` dan `orang_tua` tidak diberi permission admin pada fase inti.
5. Perubahan role/permission harus melalui task roadmap dan terdokumentasi.
