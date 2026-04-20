# 14 - Media Management Plan

## Tujuan Dokumen
Mendefinisikan pengelolaan media terpusat untuk seluruh modul CMS.

## Scope
- Upload file ke S3-compatible storage.
- Simpan metadata media di DB.
- Pilih media untuk modul konten/settings.
- Replace/delete media dengan guardrail.

## Storage Strategy
- Local/dev: MinIO.
- Staging/prod: AWS S3 atau Cloudflare R2.
- Simpan `disk`, `bucket`, `path`, `checksum`, `mime`, `size`.

## Validasi Wajib
- MIME whitelist.
- Batas ukuran file per tipe.
- Sanitasi nama file.
- Verifikasi checksum.

## Authorization
- `media.view`, `media.create`, `media.update`, `media.delete`

## Audit Events
- media.uploaded
- media.replaced
- media.deleted

## Edge Cases
- File orphan saat entitas referensi dihapus.
- File missing di object storage.
- Upload timeout file besar.

## Mitigasi
- Soft delete metadata media.
- Validasi referensi sebelum hard delete.
- Mekanisme retry upload/cleanup async jika dibutuhkan.

## Dependensi
- `04-database-plan.md`
- `05-authentication-and-authorization-plan.md`
- `15-audit-log-plan.md`

## Acceptance Criteria
- Upload media valid berhasil.
- Metadata media lengkap.
- Media dapat dipakai ulang lintas modul.
- Replace/delete aman dan tercatat audit.

## Out of Scope
- Digital asset management enterprise.

## Prompt Eksekusi Cepat
```text
Kerjakan media management sesuai 14-media-management-plan.md.
Fokus pada upload tervalidasi, metadata terstruktur, reuse media lintas modul, authorization, dan audit event.
```
