# 10 - Announcement Module Plan

## Tujuan Dokumen
Mendefinisikan modul pengumuman resmi sekolah dengan kontrol waktu tayang.

## Scope
- CRUD pengumuman.
- Status publish/unpublish.
- Publish window (`publish_start_at`, `publish_end_at`).
- Slug unik untuk detail publik.

## Data
- Tabel: `announcements`
- Relasi: `users` (author)

## Business Rules
- Pengumuman tampil publik jika:
  - status published, dan
  - waktu saat ini berada dalam publish window (jika window diatur).
- Validasi `publish_end_at >= publish_start_at`.

## Authorization
- `announcements.view`, `announcements.create`, `announcements.update`, `announcements.publish`, `announcements.delete`

## Audit Events
- announcement.created
- announcement.updated
- announcement.deleted
- announcement.published / announcement.unpublished

## Dependensi
- `05-authentication-and-authorization-plan.md`
- `15-audit-log-plan.md`
- `16-public-frontend-plan.md`

## Acceptance Criteria
- CRUD pengumuman berjalan.
- Publish window tervalidasi.
- Konten kadaluarsa tidak tampil publik.
- Event mutasi/publish tercatat audit.

## Out of Scope
- Segmentasi pengumuman granular per kelas/angkatan.

## Prompt Eksekusi Cepat
```text
Kerjakan modul pengumuman sesuai 10-announcement-module-plan.md.
Fokus pada CRUD, publish window, policy akses, audit event, dan visibilitas publik berdasarkan waktu.
Jangan menambah segmentasi kompleks di fase inti.
```
