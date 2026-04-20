# Module MediaLibrary

## Tujuan
Mengelola upload, metadata, dan pemakaian ulang aset media.

## Status
Fondasi implementasi aktif pada T012:
- upload media ke storage S3-compatible,
- metadata media tersimpan terstruktur di DB,
- replace/delete file dengan cleanup storage,
- audit event media (`media_upload`, `media_replace`, `media_delete`).

## Subdirektori
- Actions
- Policies
- DTOs
- Support
