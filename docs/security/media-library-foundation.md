# Media Library Foundation (T012)

Dokumen ini menjelaskan fondasi media library terpusat untuk CMS Sekolah.

## Cakupan T012
- Upload file media ke storage S3-compatible.
- Penyimpanan metadata media pada tabel `media_assets`.
- Replace dan delete file media dengan cleanup object storage.
- Enforcement authorization server-side untuk aksi media.
- Audit event media aktif:
  - `media_upload`
  - `media_replace`
  - `media_delete`

## Komponen Utama
- Model:
  - `app/Modules/MediaLibrary/Models/MediaAsset.php`
- Action:
  - `app/Modules/MediaLibrary/Actions/PrepareMediaAssetPayloadAction.php`
- Observer:
  - `app/Modules/MediaLibrary/Observers/MediaAssetAuditObserver.php`
- Policy:
  - `app/Policies/MediaAssetPolicy.php`
- Filament resource:
  - `app/Filament/Resources/MediaAssets/*`
- Konfigurasi:
  - `config/media_library.php`

## Validasi Keamanan
- MIME whitelist (`allowed_mime_types`).
- Batas ukuran file per pola MIME (`max_size_kb`).
- Sanitasi nama file saat upload.
- Verifikasi checksum SHA-256 sebelum metadata disimpan.

## Metadata Inti
- `disk`, `bucket`, `path`
- `file_name`, `original_name`, `extension`
- `mime_type`, `size_bytes`, `checksum`
- `visibility`, `uploaded_by`, `alt_text`, `caption`

## Catatan Reuse
- Media disimpan sebagai entitas terpisah agar dapat dipakai ulang lintas modul.
- Integrasi pemakaian media oleh modul konten dilakukan bertahap pada task modul masing-masing.
