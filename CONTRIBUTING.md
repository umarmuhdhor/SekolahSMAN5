# Contributing Guide - CMS Sekolah Dinamis

Dokumen ini menetapkan aturan kontribusi resmi untuk repository CMS Sekolah Dinamis.

## Workflow Wajib
- Model workflow: **GitHub Flow**
- Branch utama: `main` (harus selalu deployable)
- Pola branch task: `feature/<task-id>-<slug>`
- Pola eksekusi: `1 task = 1 branch = 1 PR`

Contoh:
- `feature/t001-repo-governance-baseline`
- `feature/t013-news-module-admin`

## Urutan Kerja per Task
1. Pastikan task dependency sudah selesai (lihat `project-planning/19-master-task-roadmap.md`).
2. Buat branch dari `main`.
3. Implementasikan hanya scope task aktif.
4. Update dokumentasi jika ada perubahan perilaku.
5. Buat PR dengan template wajib.
6. Merge hanya setelah review dan semua checks hijau.

## Aturan Scope
- Dilarang menambah fitur di luar scope task aktif.
- Dilarang mengubah stack/arsitektur tanpa change request.
- Dilarang mengerjakan task dengan dependency belum selesai.

## Standar Wajib di Setiap PR
- Authorization (gate/policy) dipertimbangkan.
- Audit event dipertimbangkan pada mutasi data.
- Test relevan ditambahkan/diperbarui.
- Dokumentasi kode diperbarui sesuai standar.

## Referensi Master Plan
- Manifest: `project-planning/README.md`
- Roadmap task: `project-planning/19-master-task-roadmap.md`
- Standar dokumentasi kode: `project-planning/18-coding-documentation-standard.md`
