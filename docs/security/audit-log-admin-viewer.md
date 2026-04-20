# Audit Log Admin Viewer (T019)

Dokumen ini menjelaskan implementasi halaman viewer/filter audit logs untuk admin berizin.

## Cakupan T019
- Halaman audit log viewer pada admin panel (`/admin/audit-logs`).
- Menampilkan daftar audit log secara read-only (tanpa mutasi).
- Filter audit log berdasarkan:
  - actor
  - module
  - rentang tanggal (`created_at`)
- Enforcement authorization backend untuk akses audit logs.

## Komponen Utama
- Filament resource:
  - `app/Filament/Resources/AuditLogs/AuditLogResource.php`
  - `app/Filament/Resources/AuditLogs/Pages/ListAuditLogs.php`
  - `app/Filament/Resources/AuditLogs/Tables/AuditLogsTable.php`
- Model relation pendukung filter actor:
  - `app/Modules/AuditLogs/Models/AuditLog.php` (`actor()`)
- Support module labels:
  - `app/Modules/AuditLogs/Support/AuditModule.php`

## Aturan Authorization
- Akses viewer mengikuti policy `AuditLogPolicy`.
- Permission wajib: `audit.view`.
- User tanpa permission ditolak backend (403).

## Aturan Data Viewer
- Data audit tetap immutable (append-only), viewer tidak menyediakan aksi create/update/delete.
- Filter actor memakai relasi user (`actor_id -> users.id`).
- Filter module menggunakan daftar modul audit terstandar.
- Filter tanggal memakai rentang `created_at` untuk forensic tracing.

## Catatan Integrasi
- Dashboard quick link `Audit Logs` kini berstatus ready dan mengarah ke halaman audit viewer.
- Fondasi ini melengkapi T009 agar jejak audit tidak hanya tercatat, tetapi juga dapat diinspeksi langsung oleh admin berizin.
