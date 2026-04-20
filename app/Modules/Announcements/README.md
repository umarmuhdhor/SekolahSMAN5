# Module Announcements

## Tujuan
Mengelola pengumuman resmi sekolah beserta aturan publish window.

## Status
Implementasi admin aktif pada T014:
- CRUD pengumuman di panel admin (`/admin/announcements`),
- lifecycle status `draft` / `published` / `archived`,
- validasi publish window (`publish_end_at >= publish_start_at`),
- slug unik server-side untuk URL publik,
- audit event pengumuman (`announcement.created`, `announcement.updated`, `announcement.deleted`, `announcement.published`, `announcement.unpublished`).

## Subdirektori
- Actions
- Policies
- DTOs
- Support
