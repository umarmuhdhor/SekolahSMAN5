# 11 - School Profile Settings Plan

## Tujuan Dokumen
Mendefinisikan pengelolaan profil sekolah sebagai sumber data institusi publik.

## Scope
- Pengaturan profil sekolah melalui admin.
- Single source config: nama sekolah, alamat, kontak, deskripsi, sosial media (opsional).
- Data dipakai di homepage dan halaman profil publik.

## Data
- Tabel: `school_profile` (single-row config pattern)

## Business Rules
- Hanya role berizin yang dapat update.
- Field kontak wajib valid (email/telepon).
- Perubahan profil harus tercatat audit.

## Authorization
- `school_profile.view`
- `school_profile.update`

## Audit Events
- school_profile.updated

## Dependensi
- `05-authentication-and-authorization-plan.md`
- `15-audit-log-plan.md`
- `16-public-frontend-plan.md`

## Acceptance Criteria
- Admin berizin dapat update profil sekolah.
- Validasi field profil berjalan.
- Perubahan tercermin di frontend publik.
- Audit update tersimpan lengkap.

## Out of Scope
- Multi-branch profile (multi-tenant).

## Prompt Eksekusi Cepat
```text
Kerjakan settings profil sekolah sesuai 11-school-profile-settings-plan.md.
Fokus pada single-record profile, validasi data kontak, authorization, audit, dan konsumsi data oleh frontend.
```
