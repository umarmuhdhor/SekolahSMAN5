# Audit Log Foundation (T009)

Dokumen ini menjelaskan fondasi audit log terstruktur dan immutable pada CMS Sekolah.

## Cakupan T009
- Menambahkan tabel `audit_logs` untuk jejak audit terstruktur.
- Menambahkan action pencatatan audit (`RecordAuditLogAction`).
- Menambahkan redaksi payload sensitif (`password`, `token`, `secret`, dan kunci terkait).
- Menyambungkan event auth utama ke audit table:
  - `login`
  - `logout`
  - `failed_login`
- Menyambungkan mutasi dasar role/permission dari Spatie event:
  - `role_change`
  - `permission_change`
- Menyambungkan mutasi user management:
  - `user.created`
  - `user.updated`
  - `user.status_changed`
  - `user.deleted`
- Menyambungkan event media library:
  - `media_upload`
  - `media_replace`
  - `media_delete`

## Struktur Data Audit
Field minimum yang disimpan:
- `actor_id`
- `event_type`
- `module`
- `entity_type`
- `entity_id`
- `before_json`
- `after_json`
- `ip`
- `user_agent`
- `request_id`
- `created_at`

Tambahan fondasi:
- `metadata` untuk context tambahan.

## Append-only (Immutable)
- Model `AuditLog` mencegah operasi `update` dan `delete` di level aplikasi.
- Operasi audit log bersifat insert-only secara operasional.

## Event Wiring
- Auth event listener:
  - `App\Listeners\Auth\RecordLoginEvent`
  - `App\Listeners\Auth\RecordLogoutEvent`
  - `App\Listeners\Auth\RecordFailedLoginEvent`
- Roles/permissions event listener:
  - `App\Listeners\RolesPermissions\RecordRoleAttachedEvent`
  - `App\Listeners\RolesPermissions\RecordRoleDetachedEvent`
  - `App\Listeners\RolesPermissions\RecordPermissionAttachedEvent`
  - `App\Listeners\RolesPermissions\RecordPermissionDetachedEvent`

## Konfigurasi
- `config/permission.php`:
  - `events_enabled` diaktifkan (`PERMISSION_EVENTS_ENABLED=true`) agar event mutasi role/permission dapat diaudit.

## Catatan Scope
- T009 berfokus pada fondasi penyimpanan dan perekaman event inti.
- UI viewer/filter audit logs dikerjakan pada T019 sesuai roadmap.
