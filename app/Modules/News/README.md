# Module News

## Tujuan
Mengelola konten berita, status publish, dan relasi author/media.

## Status
Implementasi admin aktif pada T013:
- CRUD berita di panel admin (`/admin/news`),
- lifecycle status `draft` / `published` / `archived`,
- slug unik konsisten untuk URL publik,
- relasi author (`users`) dan cover opsional (`media_assets`),
- audit event berita (`news.created`, `news.updated`, `news.deleted`, `news.published`, `news.unpublished`).

## Subdirektori
- Actions
- Policies
- DTOs
- Support
