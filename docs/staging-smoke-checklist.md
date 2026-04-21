# Staging Smoke Checklist (T026)

Checklist ini dipakai untuk validasi cepat setelah deployment ke staging.

## Kriteria Lulus
- Semua endpoint kritikal mengembalikan HTTP `2xx`.
- Login admin dapat diakses.
- Surface publik utama dapat dibuka tanpa error.

## Endpoint Wajib Dicek
- `/`
- `/berita`
- `/pengumuman`
- `/galeri`
- `/admin/login`

## Validasi Otomatis (Direkomendasikan)
Gunakan script:

```bash
bash scripts/smoke-staging.sh https://staging.cms-sekolah.example
```

Script akan menandai `PASS/FAIL` per endpoint dan keluar dengan exit code non-zero jika ada kegagalan.

## Validasi Manual Tambahan
1. Buka dashboard admin setelah login dan pastikan halaman utama termuat.
2. Cek halaman list publik (`berita/pengumuman/galeri`) menampilkan layout normal.
3. Cek log aplikasi staging untuk error baru sesaat setelah deploy.

## Template Catatan Eksekusi
- Waktu deploy:
- Commit SHA:
- Operator:
- Hasil smoke otomatis:
- Temuan manual:
- Status akhir: `PASS` / `FAIL`
