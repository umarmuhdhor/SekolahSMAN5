# Production Admin Handover (T027)

Dokumen handover ini dipakai untuk transisi operasional dari tim implementasi ke admin/operator sekolah.

## Tujuan
- Memastikan admin memahami alur kerja harian pada CMS production.
- Menetapkan batas kewenangan dan kontrol keamanan operasional.
- Menyediakan checklist serah-terima yang dapat diaudit.

## Peran Operasional Minimal
- `Super Admin`: kelola user/role, konfigurasi sistem, audit log.
- `Guru/Admin Konten`: kelola konten (berita, pengumuman, galeri) sesuai permission.
- `Operator Teknis`: monitoring layanan, deploy, backup/restore, rollback.

## SOP Operasional Harian
1. Verifikasi halaman publik utama dapat diakses.
2. Verifikasi login admin dan dashboard termuat normal.
3. Cek notifikasi/error kritikal pada log operasional.
4. Pastikan queue penting tidak menumpuk atau gagal terus-menerus.

## SOP Publikasi Konten
1. Buat konten sebagai `draft`.
2. Review konten (judul, slug, media, jadwal tayang).
3. Publish oleh user berpermission.
4. Verifikasi hasil publish di halaman publik.
5. Jika ada kesalahan, lakukan `unpublish` lalu perbaiki.

## SOP Manajemen Akses
1. Akun admin wajib unik per individu (tidak berbagi akun).
2. Penambahan role mengikuti prinsip least privilege.
3. User keluar tim harus dinonaktifkan pada hari yang sama.
4. Perubahan akses kritikal wajib diverifikasi oleh Super Admin.

## SOP Audit & Keamanan
- Audit log diperiksa berkala untuk aktivitas sensitif:
  - login gagal berulang,
  - perubahan role/permission,
  - publish/unpublish konten.
- Jangan menyimpan kredensial di dokumen publik.
- Gunakan channel komunikasi internal resmi untuk incident.

## Checklist Serah-Terima
- [ ] Admin memahami akses ke `/admin/login`.
- [ ] Admin memahami alur CRUD konten utama.
- [ ] Admin memahami alur publish/unpublish aman.
- [ ] Admin memahami penggunaan media library.
- [ ] Admin memahami lokasi audit log dan cara filter.
- [ ] Operator memahami SOP deploy, smoke check, dan rollback.
- [ ] Kontak PIC incident sudah disepakati.

## Daftar Kontak Operasional (Isi Internal)
| Fungsi | Nama | Kontak |
| --- | --- | --- |
| Super Admin Utama |  |  |
| Super Admin Cadangan |  |  |
| Operator Teknis On-Call |  |  |
| PIC Manajemen Sekolah |  |  |

## Referensi Modul
- `docs/security/user-management-foundation.md`
- `docs/security/news-module-admin.md`
- `docs/security/announcement-module-admin.md`
- `docs/security/gallery-module-admin.md`
- `docs/security/audit-log-admin-viewer.md`
- `docs/production-operations-runbook.md`
