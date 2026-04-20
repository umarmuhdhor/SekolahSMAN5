# Project Planning Manifest - CMS Sekolah Dinamis

Dokumen ini adalah **indeks utama** untuk seluruh rencana implementasi proyek CMS Sekolah Dinamis.
Semua sesi implementasi berikutnya wajib mengacu ke file-file pada folder ini.

## Cara Pakai (Mode Eksekusi Bertahap)
1. Buka file plan yang sesuai task aktif.
2. Cek `Prasyarat & Dependensi` pada file plan.
3. Kerjakan **1 task aktif saja** berdasarkan referensi ke `19-master-task-roadmap.md`.
4. Pastikan acceptance criteria + DoD task terpenuhi.
5. Lanjut ke task berikutnya sesuai dependency map.

## Aturan Wajib
- Arsitektur dan stack pada dokumen ini bersifat **terkunci**.
- Perubahan besar wajib melalui **Change Request / Architecture Decision Change**.
- Dilarang mengerjakan task yang dependensinya belum selesai.
- Setiap task wajib mempertimbangkan: authorization, audit log, testing, dan dokumentasi kode.
- Git workflow default: `main` deployable, `1 task = 1 branch = 1 PR`.

## Struktur File
- [00-project-overview.md](./00-project-overview.md)
- [01-scope-and-assumptions.md](./01-scope-and-assumptions.md)
- [02-user-roles-and-permissions.md](./02-user-roles-and-permissions.md)
- [03-system-architecture.md](./03-system-architecture.md)
- [04-database-plan.md](./04-database-plan.md)
- [05-authentication-and-authorization-plan.md](./05-authentication-and-authorization-plan.md)
- [06-admin-panel-core-plan.md](./06-admin-panel-core-plan.md)
- [07-news-module-plan.md](./07-news-module-plan.md)
- [08-user-management-plan.md](./08-user-management-plan.md)
- [09-gallery-module-plan.md](./09-gallery-module-plan.md)
- [10-announcement-module-plan.md](./10-announcement-module-plan.md)
- [11-school-profile-settings-plan.md](./11-school-profile-settings-plan.md)
- [12-theme-customization-plan.md](./12-theme-customization-plan.md)
- [13-navigation-management-plan.md](./13-navigation-management-plan.md)
- [14-media-management-plan.md](./14-media-management-plan.md)
- [15-audit-log-plan.md](./15-audit-log-plan.md)
- [16-public-frontend-plan.md](./16-public-frontend-plan.md)
- [17-testing-security-and-deployment-plan.md](./17-testing-security-and-deployment-plan.md)
- [18-coding-documentation-standard.md](./18-coding-documentation-standard.md)
- [19-master-task-roadmap.md](./19-master-task-roadmap.md)

## Urutan Baca yang Disarankan
1. 00 → 01 → 02 → 03 → 04 (fondasi keputusan)
2. 05 → 06 → 14 → 15 (security + admin core)
3. 07 → 10 → 09 → 11 → 12 → 13 (modul inti)
4. 16 (frontend publik)
5. 17 → 18 → 19 (QA, standar coding, roadmap eksekusi)

## Catatan
- Folder ini adalah **single source of truth perencanaan**.
- Seluruh task implementasi pada sesi berikutnya wajib menautkan file plan yang relevan.
