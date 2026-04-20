# 15 - Audit Log Plan

## Tujuan Dokumen
Mendefinisikan baseline auditability untuk seluruh aksi kritikal dalam CMS.

## Scope
- Menyimpan event audit terstruktur.
- Menyediakan tampilan admin untuk inspeksi audit log.
- Menetapkan daftar event wajib lintas modul.

## Struktur Data Audit
Field minimum:
- actor_id
- event_type
- module
- entity_type
- entity_id
- before_json
- after_json
- ip
- user_agent
- request_id
- created_at

## Event Wajib
- login
- logout
- failed_login
- create
- update
- delete
- publish
- unpublish
- role_change
- permission_change
- school_profile_update
- theme_update
- navigation_update
- media_upload
- media_replace
- media_delete

## Prinsip Implementasi
- Audit log bersifat append-only (immutable secara operasional).
- Redaksi data sensitif pada snapshot `before/after`.
- Simpan context request untuk forensic.

## Authorization
- Hanya Super Admin (atau role khusus auditor) dapat melihat audit logs.

## Dependensi
- `02-user-roles-and-permissions.md`
- `05-authentication-and-authorization-plan.md`
- semua modul mutasi data

## Acceptance Criteria
- Event wajib tercatat untuk semua modul inti.
- Data audit dapat difilter minimum per actor, modul, rentang waktu.
- Tidak ada data sensitif mentah yang bocor di payload audit.

## Out of Scope
- SIEM enterprise integration pada fase awal.

## Prompt Eksekusi Cepat
```text
Kerjakan audit log foundation sesuai 15-audit-log-plan.md.
Implementasikan event map wajib, payload before/after dengan redaksi data sensitif, akses view terbatas, dan integrasi lintas modul.
```
