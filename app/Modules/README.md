# App Modules - Modular Monolith Boundary

Direktori ini adalah batas domain utama untuk CMS Sekolah Dinamis.

## Tujuan
- Memisahkan logic per domain agar maintainable.
- Menjaga agar implementasi bertahap tetap konsisten lintas task/sesi.
- Mencegah coupling berlebih antar modul.

## Modul Inti
- Auth
- Users
- RolesPermissions
- Dashboard
- News
- Announcements
- Galleries
- MediaLibrary
- SchoolProfile
- ThemeSettings
- NavigationMenus
- AuditLogs

## Konvensi Direktori per Modul
- `Actions/`: use case/action classes untuk business flow.
- `Policies/`: policy authorization level modul.
- `DTOs/`: object transfer data untuk boundary service.
- `Support/`: helper internal modul (non-global).

## Aturan Boundary
1. Hindari akses langsung ke detail internal modul lain.
2. Integrasi antar modul melalui action/service yang eksplisit.
3. Authorization selalu lewat Gate/Policy di layer server.
4. Mutasi data penting harus memicu audit event.

## Catatan
Pada fase T002, struktur ini baru baseline. Implementasi isi modul dilakukan per task berikutnya sesuai roadmap.
