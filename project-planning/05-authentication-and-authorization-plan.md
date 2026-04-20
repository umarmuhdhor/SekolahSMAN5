# 05 - Authentication and Authorization Plan

## Tujuan Dokumen
Menetapkan baseline keamanan akses sistem CMS Sekolah.

## Scope
- Session-based auth untuk panel admin.
- Gate/Policy enforcement di semua mutasi data.
- Integrasi role/permission via spatie/laravel-permission.
- Login throttling dan event audit auth.

## Desain Authentication
- Guard utama: `web` (session).
- Login endpoint/admin page melalui Filament auth.
- Logout wajib invalidasi session + regenerate token.
- Failed login dicatat untuk audit dan mitigasi brute force.

## Desain Authorization
- Permission granular per modul (`<module>.<action>`).
- Policy per entitas domain: news, announcements, galleries, media, settings.
- `deny-by-default` untuk aksi tanpa mapping jelas.
- UI admin hanya menampilkan action yang diizinkan, tetapi enforcement wajib di backend.

## Baseline Security Rules
- CSRF aktif di seluruh form mutasi.
- Validasi input server-side (Request Validation).
- Password hashing default Laravel.
- Rate limiting login endpoint.
- Session cookie: secure, httpOnly, sameSite.

## Event Audit Wajib
- login success
- login failed
- logout
- role change
- permission change
- authorization denied (opsional untuk forensic)

## Dependensi
- `00-project-overview.md`
- `02-user-roles-and-permissions.md`
- `15-audit-log-plan.md`

## Acceptance Criteria
- Login/logout admin berjalan aman.
- Unauthorized action ditolak oleh policy/gate.
- Role/permission assignment memengaruhi akses nyata.
- Event auth utama tercatat di audit log.

## Out of Scope
- JWT sebagai auth utama web.
- OAuth/SSO lanjutan pada fase inti.

## Prompt Eksekusi Cepat
```text
Kerjakan implementasi auth/authz sesuai 05-authentication-and-authorization-plan.md.
Pastikan session-based auth, policy/gate server-side, role-permission spatie, login throttling, dan audit auth event aktif.
Jangan menambah arsitektur baru di luar modular monolith Laravel 13.
```
