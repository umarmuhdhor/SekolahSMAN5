# 04 - Database Plan

## Tujuan Dokumen
Mendefinisikan rancangan entitas, relasi, constraint, indexing, dan lifecycle data.

## Engine dan Prinsip
- DBMS: **PostgreSQL**
- Gunakan migrasi terstruktur per modul.
- Konsistensi data diutamakan melalui FK + constraint.
- Query publik kritikal didukung indexing yang jelas.

## Entitas Inti

### users
- Fungsi: akun pengguna CMS.
- Field penting: `name`, `email`, `password`, `status`, `last_login_at`.
- Constraint: unique `email`.
- Relasi: ke roles (spatie), ke audit_logs (actor).

### roles / permissions (spatie)
- Fungsi: kontrol akses granular.
- Relasi: many-to-many role-permission, user-role.

### news
- Fungsi: konten berita.
- Field: `title`, `slug`, `summary`, `content`, `status`, `published_at`, `author_id`.
- Constraint: unique `slug`.
- Index: `(status, published_at)`.

### announcements
- Fungsi: pengumuman dengan rentang waktu tayang.
- Field: `title`, `slug`, `content`, `status`, `publish_start_at`, `publish_end_at`, `author_id`.
- Constraint: validasi `publish_end_at >= publish_start_at`.

### galleries
- Fungsi: album galeri.
- Field: `title`, `slug`, `description`, `status`, `published_at`, `author_id`.

### gallery_items
- Fungsi: item media dalam album.
- Field: `gallery_id`, `media_asset_id`, `caption`, `sort_order`.
- Constraint: unique `(gallery_id, sort_order)`.

### media_assets
- Fungsi: metadata file media.
- Field: `disk`, `bucket`, `path`, `mime_type`, `size`, `checksum`, `metadata_json`, `uploaded_by`.
- Index: `mime_type`, `created_at`.

### school_profile
- Fungsi: konfigurasi profil sekolah.
- Pola: single-row config table.

### theme_settings
- Fungsi: pengaturan logo dan warna.
- Field: `logo_media_id`, `primary_color`, `secondary_color`, `accent_color`, `is_active`.

### navigation_menus / navigation_items
- Fungsi: menu publik dinamis.
- Constraint: depth hierarchy dibatasi, urutan unik per parent.

### audit_logs
- Fungsi: catatan aktivitas sensitif.
- Field: `actor_id`, `event_type`, `module`, `entity_type`, `entity_id`, `before_json`, `after_json`, `ip`, `user_agent`, `request_id`, `created_at`.
- Index: `(module, created_at)`, `(actor_id, created_at)`.

## Strategi Data
- Soft delete untuk entitas konten dan media.
- Slug unik per tabel konten, auto-suffix bila bentrok.
- Metadata media simpan sebagai kolom struktural + JSONB.
- Snapshot audit simpan `before/after` dengan redaksi field sensitif.

## Data Lifecycle
- Draft → Published → Archived (konten).
- Media: uploaded → active → replaced/deleted.
- Audit logs immutable.

## Referensi
- Modul berita: `07-news-module-plan.md`
- Modul media: `14-media-management-plan.md`
- Modul audit: `15-audit-log-plan.md`
