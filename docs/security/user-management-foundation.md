# User Management Foundation (T010)

Dokumen ini menjelaskan fondasi modul manajemen user admin pada CMS Sekolah.

## Cakupan T010
- CRUD user admin (nama, email, status aktif).
- Assign role user dari panel admin.
- Enforcement authorization server-side via policy.
- Audit perubahan user:
  - `user.created`
  - `user.updated`
  - `user.status_changed`
  - `user.deleted`
- Constraint keamanan:
  - email unik,
  - password minimal 8 karakter,
  - super admin terakhir tidak boleh dihapus.

## Komponen Utama
- Filament resource:
  - `app/Filament/Resources/Users/UserResource.php`
  - `app/Filament/Resources/Users/Schemas/UserForm.php`
  - `app/Filament/Resources/Users/Tables/UsersTable.php`
  - `app/Filament/Resources/Users/Pages/CreateUser.php`
  - `app/Filament/Resources/Users/Pages/EditUser.php`
- Policy:
  - `app/Policies/UserPolicy.php`
- Audit observer:
  - `app/Modules/Users/Observers/UserAuditObserver.php`
- Data model:
  - kolom `users.is_active`

## Aturan Enforcement
- Akses modul users mengikuti permission:
  - `users.view`
  - `users.create`
  - `users.update`
  - `users.delete`
  - `users.assign_role`
- Role assignment hanya diproses bila actor lolos ability `assignRole`.
- User nonaktif (`is_active = false`) ditolak mengakses panel admin.

## Catatan
- Event role attach/detach tetap tercatat melalui listener role/permission pada T009.
- Viewer audit log tetap dikerjakan di T019.
