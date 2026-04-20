# 09 - Gallery Module Plan

## Tujuan Dokumen
Mendefinisikan modul galeri sekolah untuk menampilkan dokumentasi kegiatan.

## Scope
- CRUD album galeri.
- Manajemen item media per album.
- Urutan item (`sort_order`).
- Publish/unpublish album.

## Data
- `galleries`
- `gallery_items`
- relasi ke `media_assets`

## Admin Surface
- Gallery List
- Gallery Create/Edit
- Item manager (add/remove/reorder)

## Business Rules
- Album hanya tampil di publik jika published.
- Item harus merujuk media valid.
- Urutan item unik di dalam album.

## Authorization
- `galleries.view`, `galleries.create`, `galleries.update`, `galleries.publish`, `galleries.delete`

## Audit Events
- gallery.created
- gallery.updated
- gallery.deleted
- gallery.published / gallery.unpublished
- gallery.item_added / gallery.item_removed / gallery.items_reordered

## Dependensi
- `14-media-management-plan.md`
- `05-authentication-and-authorization-plan.md`
- `16-public-frontend-plan.md`

## Acceptance Criteria
- Admin bisa kelola album + item dengan aman.
- Urutan item konsisten setelah update.
- Hanya album published tampil di publik.
- Event utama tercatat pada audit log.

## Out of Scope
- Editor gambar dalam aplikasi.
- Fitur gallery analytics kompleks.

## Prompt Eksekusi Cepat
```text
Kerjakan modul galeri sesuai 09-gallery-module-plan.md.
Fokus pada CRUD album, relasi item media, sorting, publish status, permission, dan audit log.
Jangan membangun fitur edit gambar lanjutan.
```
