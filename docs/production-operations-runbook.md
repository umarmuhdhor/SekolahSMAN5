# Production Operations & Rollback Runbook (T027)

Dokumen ini menjadi panduan operator saat deploy, validasi, incident handling, dan rollback production.

## Tujuan
- Menjaga proses rilis production konsisten dan dapat diulang.
- Menyediakan prosedur rollback cepat saat terjadi kegagalan.
- Menetapkan validasi minimum sebelum sistem dinyatakan stabil.

## Prasyarat Sebelum Deploy
- Commit SHA target sudah lulus CI dan sudah tervalidasi di staging.
- Akses SSH production tersedia untuk operator on-duty.
- Backup database terbaru tersedia.
- Window deploy dan PIC incident sudah ditetapkan.

## Prosedur Deploy Production (High Level)
1. Freeze perubahan non-kritis selama window deploy.
2. Buat backup database production sebelum update kode.
3. Checkout commit SHA rilis pada server production.
4. Install dependency production (`composer install --no-dev`).
5. Build asset frontend (`npm ci && npm run build`).
6. Jalankan migrasi (`php artisan migrate --force`).
7. Refresh cache (`php artisan optimize:clear`, `config:cache`, `route:cache`, `view:cache`).
8. Restart queue worker (`php artisan queue:restart`).
9. Jalankan smoke check endpoint kritikal.
10. Dokumentasikan hasil deploy dan status akhir.

## Smoke Check Minimal Production
Endpoint minimum yang wajib `2xx`:
- `/`
- `/berita`
- `/pengumuman`
- `/galeri`
- `/admin/login`

Contoh eksekusi:

```bash
bash scripts/smoke-staging.sh https://cms-sekolah.example
```

Catatan: script bersifat generic dan dapat dipakai untuk production selama base URL production diberikan.

## Monitoring Pasca Deploy
- Pantau log aplikasi (`storage/logs/laravel.log`) untuk error baru.
- Pantau queue backlog dan job gagal.
- Pantau endpoint publik utama selama minimal 30 menit pertama.
- Validasi login admin dan akses modul konten utama.

## Klasifikasi Incident (Ringkas)
- `SEV-1`: layanan publik/admin tidak dapat diakses total.
- `SEV-2`: fitur kritikal gagal sebagian (publish konten, login admin, queue utama).
- `SEV-3`: gangguan minor tanpa dampak operasional besar.

Respons awal:
- `SEV-1`: rollback segera setelah analisis cepat (maksimal 15 menit).
- `SEV-2`: mitigasi cepat, rollback jika tidak pulih dalam SLA internal.
- `SEV-3`: perbaikan terjadwal, tanpa rollback kecuali eskalasi.

## Rollback Strategy
Rollback harus mempertimbangkan dua lapisan: aplikasi dan database.

### A. Rollback Aplikasi (Kode)
1. Identifikasi release SHA stabil sebelumnya.
2. Checkout SHA stabil pada server production.
3. Jalankan ulang install/build/cache/restart queue seperti prosedur deploy.
4. Jalankan smoke check ulang.

### B. Rollback Database
- Jangan mengandalkan `migrate:rollback` untuk migrasi yang berpotensi destruktif.
- Gunakan restore dari backup tervalidasi sebagai jalur rollback utama.
- Setelah restore, sinkronkan kode aplikasi ke SHA yang kompatibel dengan snapshot DB.

## Validasi Wajib Setelah Rollback
- Endpoint kritikal kembali `2xx`.
- Login admin berhasil.
- Alur konten dasar (buat draft/publish) berjalan.
- Tidak ada error kritikal baru di log aplikasi.

## Template Catatan Eksekusi
- Waktu kejadian:
- Environment:
- Release SHA terdampak:
- Gejala utama:
- Keputusan: `rollback` / `hotfix`
- SHA tujuan rollback:
- Backup DB yang dipakai (jika restore):
- Hasil smoke check:
- Status akhir: `RECOVERED` / `ONGOING`

## Referensi Terkait
- `docs/staging-deployment.md`
- `docs/staging-smoke-checklist.md`
- `docs/production-go-live-checklist.md`
