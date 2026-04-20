# 18 - Coding Documentation Standard

## Tujuan Dokumen
Menetapkan standar dokumentasi kode wajib agar implementasi lintas sesi tetap terbaca, aman, dan mudah dirawat.

## Prinsip Umum
- Dokumentasi harus kontekstual untuk proyek CMS Sekolah.
- Hindari docstring generik tanpa nilai teknis.
- Komentar inline hanya untuk keputusan non-obvious.

## Objek yang Wajib Didokumentasikan
- function, method, class, component
- service, controller, action
- model, request validation, policy
- job, event, listener
- migration, helper/utility
- endpoint penting
- custom Filament resource/page/widget

## Isi Minimum Docstring
Setiap docstring minimal memuat:
1. tujuan/responsibility
2. konteks penggunaan
3. parameter penting
4. output/return
5. validasi penting
6. error/failure mode
7. side effects
8. dependency penting
9. asumsi

## Standar Naming
- Nama harus deskriptif, konsisten, dan domain-oriented.
- Hindari singkatan tidak umum.
- Gunakan penamaan permission `<module>.<action>`.

## Standar Struktur Fungsi
- Hindari fungsi terlalu panjang tanpa pemisahan concern.
- Untuk logika kompleks, pecah ke service/action dengan dokumentasi terpisah.

## Kewajiban di PR
Setiap PR wajib menyebut:
- area kode yang ditambahkan/diubah,
- area dokumentasi yang ditambahkan/diubah,
- dampak ke authorization/audit/testing,
- trade-off teknis jika ada.

## Checklist Dokumentasi per Task
- [ ] Docstring objek utama ditambahkan.
- [ ] Komentar keputusan non-obvious tersedia.
- [ ] README/plan terkait diperbarui jika ada perubahan perilaku.
- [ ] PR description memuat ringkasan perubahan dan risiko.

## Referensi
- Rulebook: `00-project-overview.md`
- Roadmap: `19-master-task-roadmap.md`
