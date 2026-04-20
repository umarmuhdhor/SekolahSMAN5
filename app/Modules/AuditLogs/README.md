# Module AuditLogs

## Tujuan
Mencatat dan menampilkan jejak audit aktivitas penting.

## Status
Fondasi implementasi aktif pada T009:
- tabel audit terstruktur `audit_logs`,
- action pencatatan audit dengan redaksi data sensitif,
- immutable model (append-only),
- listener event auth serta mutasi role/permission.

## Subdirektori
- Actions
- Policies
- DTOs
- Support
