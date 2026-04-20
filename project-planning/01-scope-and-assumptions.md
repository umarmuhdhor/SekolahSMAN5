# 01 - Scope and Assumptions

## Tujuan Dokumen
Menetapkan batas implementasi inti agar proyek fokus, realistis, dan terhindar dari scope creep.

## In Scope (Fase Inti)
- Manajemen user CMS.
- Manajemen role & permission.
- Dashboard admin.
- Manajemen berita.
- Manajemen pengumuman.
- Manajemen galeri.
- Manajemen media/file.
- Pengaturan profil sekolah.
- Pengaturan tema (logo + warna).
- Pengaturan menu navigasi.
- Website publik: homepage, berita, pengumuman, galeri.
- Audit log aktivitas admin.

## Out of Scope (Bukan Fase Inti)
- Nilai akademik.
- Absensi.
- Jadwal pelajaran.
- Pembayaran.
- E-learning/LMS.
- Ujian online.
- Microservices.
- Full SPA sebagai default.
- JWT untuk auth utama web CMS.

## Future Scope (Opsional)
- Integrasi sistem akademik.
- SSO sekolah.
- Workflow editorial multi-level lanjutan.
- Mobile app.
- Notifikasi multi-channel.

## Asumsi Proyek
- Tim implementasi kecil-menengah (3-7 orang).
- Developer memiliki pengalaman menengah Laravel.
- Admin panel akan dipakai pengguna non-teknis.
- Website publik lebih prioritas dibanding aplikasi mobile.
- Volume trafik awal menengah.
- Deployment bertahap local → staging → production.
- Repository GitHub tersedia sebelum task coding dimulai.

## Risiko Scope Creep
- Permintaan fitur akademik masuk sprint CMS.
- Penambahan modul tanpa dependency readiness.
- Perubahan stack tanpa persetujuan arsitektur.

## Mekanisme Kontrol Scope
1. Semua request baru diklasifikasi: `in-scope`, `future scope`, atau `reject`.
2. Jika berdampak arsitektur/scope inti, wajib buat change request.
3. Task di luar dependency roadmap tidak boleh dikerjakan.

## Referensi
- Rulebook inti: `00-project-overview.md`
- Task sequencing: `19-master-task-roadmap.md`
