# Module Users

## Tujuan
Mengelola lifecycle user CMS, status akun, dan profil akun admin.

## Status
Fondasi implementasi aktif pada T010:
- CRUD user melalui Filament resource,
- assign role berbasis permission `users.assign_role`,
- aktivasi/non-aktivasi akun (`is_active`),
- audit perubahan user (create/update/status/deleted) melalui observer.

## Subdirektori
- Actions
- Policies
- DTOs
- Support
