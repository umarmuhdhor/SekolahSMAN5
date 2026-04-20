# Module Galleries

## Tujuan
Mengelola album galeri sekolah, item media, dan urutan tampil.

## Status
Implementasi admin aktif pada T015:
- CRUD album galeri di panel admin (`/admin/galleries`),
- lifecycle status `draft` / `published` / `archived`,
- manajemen item media per album (add/remove/reorder),
- urutan item stabil dengan `sort_order` unik per album,
- audit event gallery (`gallery.created`, `gallery.updated`, `gallery.deleted`, `gallery.published`, `gallery.unpublished`, `gallery.item_added`, `gallery.item_removed`, `gallery.items_reordered`).

## Subdirektori
- Actions
- Policies
- DTOs
- Support
