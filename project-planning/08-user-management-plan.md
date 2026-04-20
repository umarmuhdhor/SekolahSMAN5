# 08 - User Management Plan

## Tujuan Dokumen
Mendefinisikan pengelolaan user CMS dan lifecycle akun.

## Scope
- CRUD user admin (nama, email, status).
- Reset password aman.
- Assign role ke user.
- Aktivasi/non-aktivasi akun.

## Data
- Tabel inti: `users`
- Relasi: `model_has_roles` (spatie), `audit_logs`.

## Lifecycle User
1. User dibuat oleh Super Admin.
2. Role diberikan sesuai kebutuhan.
3. Akun dapat diaktifkan/nonaktifkan.
4. Perubahan role/status diaudit.

## Authorization
- `users.view`, `users.create`, `users.update`, `users.delete`
- `roles.assign` (khusus Super Admin)

## Validasi
- Email unik.
- Password policy minimum.
- Tidak boleh menghapus Super Admin terakhir.

## Audit Events
- user.created
- user.updated
- user.status_changed
- user.role_assigned / user.role_removed
- user.deleted

## Dependensi
- `02-user-roles-and-permissions.md`
- `05-authentication-and-authorization-plan.md`
- `15-audit-log-plan.md`

## Acceptance Criteria
- Super Admin dapat mengelola user dan role dengan aman.
- Constraint email unik dan policy password aktif.
- Perubahan role/status tercatat audit log.

## Out of Scope
- Self-registration publik.
- Manajemen user skala enterprise (tenant multi organisasi).

## Prompt Eksekusi Cepat
```text
Kerjakan modul user management sesuai 08-user-management-plan.md.
Fokus pada CRUD user admin, assign role, status akun, policy akses, dan audit event.
Jangan implementasi self-registration publik.
```
