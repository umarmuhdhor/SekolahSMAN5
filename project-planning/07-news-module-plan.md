# 07 - News Module Plan

## Tujuan Dokumen
Mendefinisikan modul manajemen berita sebagai salah satu fitur inti CMS.

## Scope
- CRUD berita di admin.
- Status lifecycle: draft/published/archived.
- Slug unik untuk URL publik.
- Integrasi author dan media utama.

## Data yang Digunakan
- Tabel: `news`
- Relasi: `users` (author), `media_assets` (thumbnail/cover opsional)

## Admin Surface
- News List
- Create/Edit News
- Publish/Unpublish Action
- Filter status/tanggal/author

## Business Rules
- Judul wajib, konten wajib, slug unik.
- Hanya status `published` yang boleh tampil di publik.
- Perubahan konten published harus tetap diaudit.

## Authorization
- `news.view`, `news.create`, `news.update`, `news.publish`, `news.delete`
- Guru default `update-own`, Super Admin `update-any`.

## Audit Events
- news.created
- news.updated
- news.deleted
- news.published
- news.unpublished

## Dependensi
- `05-authentication-and-authorization-plan.md`
- `14-media-management-plan.md`
- `15-audit-log-plan.md`
- `16-public-frontend-plan.md`

## Acceptance Criteria
- CRUD berita berjalan sesuai role.
- Slug unik konsisten.
- Hanya berita published muncul di halaman publik.
- Semua mutasi penting tercatat audit.

## Out of Scope
- Workflow approval multi-level.
- Integrasi notifikasi massal.

## Prompt Eksekusi Cepat
```text
Kerjakan modul berita sesuai 07-news-module-plan.md.
Fokus pada CRUD admin, slug unik, status publish, authorization policy, dan audit event.
Jangan menambahkan workflow approval kompleks di fase ini.
```
