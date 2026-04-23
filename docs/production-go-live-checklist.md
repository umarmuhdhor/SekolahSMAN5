# Production Go-Live Checklist (T027)

Checklist ini dipakai untuk validasi readiness sebelum dan sesudah cutover production.

## Ruang Lingkup
- Aplikasi Laravel + Filament CMS Sekolah.
- Infrastruktur aplikasi, database, queue, storage object.
- Validasi operasional, keamanan, dan handover tim admin.

## Snapshot Verifikasi Saat Ini (Pre-Cutover, 2026-04-23)
- [x] Branch release `feature/t027-production-readiness-handover` dalam kondisi hijau (CI `lint` dan `test` lulus di PR).
- [x] Dokumen handover/release T027 tersedia dan sinkron.
- [ ] Deploy + smoke check staging terbaru untuk SHA target (menunggu konfirmasi infra/ops).
- [ ] Eksekusi cutover production H-0 (belum dijalankan).

## Pre Go-Live (H-7 s.d. H-1)
- [ ] Branch `main` dalam kondisi hijau (`CI / lint` dan `CI / test` lulus).
- [ ] Deploy terakhir ke staging sukses.
- [ ] Smoke check staging lulus untuk endpoint kritikal.
- [ ] Konfigurasi production tervalidasi (`APP_ENV=production`, `APP_DEBUG=false`, cache driver, queue driver, mail driver).
- [ ] Secret production tervalidasi (DB, Redis, storage, mail, app key).
- [ ] Backup database production terakhir tersedia dan tervalidasi restore test-nya.
- [ ] SOP rollback dipahami operator on-duty.
- [ ] Akun admin operasional (super admin) siap digunakan.
- [ ] Checklist handover admin selesai.

## Eksekusi Go-Live (H-0)
- [ ] Freeze perubahan non-kritis selama window rilis.
- [ ] Buat backup database tepat sebelum deploy.
- [ ] Deploy commit SHA yang sudah tervalidasi di staging.
- [ ] Jalankan migrasi database production.
- [ ] Rebuild cache aplikasi (`config`, `route`, `view`) dan restart queue worker.
- [ ] Jalankan smoke check production endpoint kritikal.
- [ ] Verifikasi login admin dan akses modul utama.
- [ ] Publikasikan informasi sukses rilis ke stakeholder internal.

## Post Go-Live (H+0 s.d. H+3)
- [ ] Monitoring error log, queue, dan respons endpoint berjalan normal.
- [ ] Tidak ada lonjakan error kritis pada audit/security log.
- [ ] Uji transaksi konten sederhana (buat draft, publish, unpublish) berhasil.
- [ ] Tidak ada regresi pada halaman publik utama.
- [ ] Incident mayor dalam 72 jam pertama: `0`.

## Sign-Off Internal
Isi tabel ini setelah validasi selesai.

| Peran | Nama | Tanggal | Status |
| --- | --- | --- | --- |
| Tech Lead |  |  | APPROVED / BLOCKED |
| QA Lead |  |  | APPROVED / BLOCKED |
| DevOps/Operator |  |  | APPROVED / BLOCKED |
| Product Owner/Perwakilan Sekolah |  |  | APPROVED / BLOCKED |

Kriteria selesai: seluruh item checklist tercentang dan seluruh sign-off berstatus `APPROVED`.
