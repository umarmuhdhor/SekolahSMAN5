# Gate & Policy Enforcement (T008)

Dokumen ini menjelaskan implementasi enforcement authorization server-side pada T008 dengan prinsip `deny-by-default`.

## Cakupan T008
- Gate foundation untuk akses panel admin dan dashboard.
- Policy foundation berbasis permission untuk entitas keamanan inti:
  - `UserPolicy`
  - `RolePolicy`
  - `PermissionPolicy`
- Wiring backend authorization pada area yang sudah tersedia (admin panel Filament).
- Feature test skenario allow/deny untuk gate dan policy.

## Komponen Utama
- Provider authorization:
  - `app/Providers/AuthServiceProvider.php`
- Ability katalog:
  - `app/Modules/RolesPermissions/Support/AuthorizationAbility.php`
- Policy helper:
  - `app/Policies/Concerns/AuthorizesWithPermissions.php`
- Policy:
  - `app/Policies/UserPolicy.php`
  - `app/Policies/RolePolicy.php`
  - `app/Policies/PermissionPolicy.php`
- Wiring panel/dashboard:
  - `app/Models/User.php` (`canAccessPanel` -> gate `panel.access`)
  - `app/Filament/Pages/Dashboard.php` (`canAccess` -> gate `panel.dashboard.view`)
  - `app/Providers/Filament/AdminPanelProvider.php`

## Aturan Enforcement
- `panel.access`:
  - User wajib punya role admin panel (`super_admin`/`guru`) **dan** permission `dashboard.view`.
- `panel.dashboard.view`:
  - User wajib lolos syarat role admin panel + permission `dashboard.view`.
- Policy method yang tidak didefinisikan otomatis ditolak (`deny-by-default`).

## Dampak Perilaku
- Role admin panel tanpa permission `dashboard.view` tidak bisa mengakses `/admin`.
- User non-admin panel tetap ditolak walaupun diberi permission `dashboard.view`.
- Pembatasan bukan hanya visibilitas UI, namun diproteksi backend melalui Gate/Policy.

## Catatan Lanjutan
- Fondasi ini disiapkan untuk modul berikutnya agar semua mutasi data memakai pola policy yang konsisten.
- Implementasi audit log authorization denied tetap berada pada lingkup task audit (T009+).
- Pada T010, `UserPolicy` dipakai langsung oleh modul user management dengan guard tambahan untuk mencegah penghapusan Super Admin terakhir.
