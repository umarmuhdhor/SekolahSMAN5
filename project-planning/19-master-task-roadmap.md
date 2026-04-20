# 19 - Master Task Roadmap

## Tujuan Dokumen
Menjadi backlog eksekusi modular resmi dengan pola `1 sesi = 1 task aktif`.

## Aturan Eksekusi
- Kerjakan satu task card per sesi.
- Jangan melompati dependency.
- Setiap task harus menghasilkan deliverable yang bisa diverifikasi.
- Setiap task wajib mematuhi authorization, audit, testing, dan documentation standard.

## Definition of Ready (DoR)
Task boleh mulai jika:
- scope task jelas,
- prasyarat/dependency selesai,
- acceptance criteria ada,
- file plan referensi sudah ditentukan.

## Definition of Done (DoD)
Task selesai jika:
- deliverable tercapai,
- acceptance criteria terpenuhi,
- test relevan lulus,
- policy/audit relevan aktif,
- dokumentasi kode dan PR notes lengkap.

## Fase dan Urutan
1. Foundation
2. Security Core
3. Admin Core
4. Content Modules
5. Settings & Customization
6. Public Frontend
7. QA Hardening
8. Deployment & Handover

## Dependency Map Ringkas
- Foundation: T001-T005
- Security Core: T006-T009
- Admin Core: T010-T012
- Content: T013-T016
- Settings: T017-T019
- Public: T020-T023
- QA/Deploy: T024-T027

Task paralel yang aman:
- T003 dan T004 dapat paralel setelah T001/T002.
- T014, T015, T016 paralel setelah T012.
- T021, T022, T023 paralel setelah T020 + modul masing-masing.

---

## Task Cards

### T001 - Repository Baseline & Branch Rules
- Plan Referensi: `00`, `17`
- Prasyarat: -
- Deliverable: repository siap, branch protection `main`, PR template.
- Acceptance Criteria: `main` protected, required checks aktif, template tersedia.
- Prompt:
```text
Kerjakan T001 sesuai roadmap. Setup governance repository, branch protection, dan PR template tanpa implementasi fitur aplikasi.
```

### T002 - Laravel 13 Skeleton + Filament 5 Bootstrap
- Plan Referensi: `00`, `03`, `06`
- Prasyarat: T001
- Deliverable: skeleton app bootable.
- Acceptance Criteria: aplikasi jalan lokal, Filament aktif.
- Prompt:
```text
Kerjakan T002: inisialisasi Laravel 13 + Filament 5, struktur modular monolith dasar, tanpa modul bisnis detail.
```

### T003 - Docker Compose Local Stack
- Plan Referensi: `03`, `17`
- Prasyarat: T001
- Deliverable: compose untuk app/nginx/postgres/redis/minio.
- Acceptance Criteria: semua service sehat dan terkoneksi.
- Prompt:
```text
Kerjakan T003: setup Docker Compose local environment lengkap untuk CMS.
```

### T004 - Environment Configuration Baseline
- Plan Referensi: `03`, `17`
- Prasyarat: T002, T003
- Deliverable: `.env.example` dan mapping config lintas env.
- Acceptance Criteria: local/staging template konsisten, secret tidak hardcoded.
- Prompt:
```text
Kerjakan T004: konfigurasi baseline aplikasi untuk DB, Redis, session, queue, storage.
```

### T005 - CI Baseline GitHub Actions
- Plan Referensi: `17`
- Prasyarat: T001, T004
- Deliverable: workflow lint/check/test.
- Acceptance Criteria: PR ditahan jika checks gagal.
- Prompt:
```text
Kerjakan T005: setup CI GitHub Actions baseline untuk quality gate PR.
```

### T006 - Session Authentication Foundation
- Plan Referensi: `05`
- Prasyarat: T002, T004
- Deliverable: login/logout session admin + throttling.
- Acceptance Criteria: auth flow dan failed login handling berjalan.
- Prompt:
```text
Kerjakan T006: implementasi auth session-based admin sesuai plan.
```

### T007 - Role & Permission Foundation
- Plan Referensi: `02`, `05`
- Prasyarat: T006
- Deliverable: role default + permission baseline.
- Acceptance Criteria: akses route/resource sesuai matriks.
- Prompt:
```text
Kerjakan T007: integrasi spatie permission, role wajib, dan permission baseline.
```

### T008 - Gate/Policy Enforcement
- Plan Referensi: `05`
- Prasyarat: T007
- Deliverable: policy enforcement modul inti.
- Acceptance Criteria: unauthorized action ditolak server-side.
- Prompt:
```text
Kerjakan T008: terapkan gate/policy dengan deny-by-default pada aksi mutasi.
```

### T009 - Audit Log Foundation
- Plan Referensi: `15`
- Prasyarat: T006, T007
- Deliverable: tabel audit + service pencatatan event inti.
- Acceptance Criteria: event auth dan mutasi dasar tercatat.
- Prompt:
```text
Kerjakan T009: fondasi audit log terstruktur dan immutable.
```

### T010 - User Management Module
- Plan Referensi: `08`
- Prasyarat: T007, T008, T009
- Deliverable: CRUD user + assign role.
- Acceptance Criteria: validasi email unik, audit role/status change aktif.
- Prompt:
```text
Kerjakan T010: user management admin (CRUD, role assignment, status) sesuai plan.
```

### T011 - Admin Dashboard Core
- Plan Referensi: `06`
- Prasyarat: T006, T008
- Deliverable: dashboard + menu guard by permission.
- Acceptance Criteria: menu sensitif hanya terlihat untuk role berhak.
- Prompt:
```text
Kerjakan T011: bangun dashboard Filament dasar dan navigation guard.
```

### T012 - Media Library Foundation
- Plan Referensi: `14`
- Prasyarat: T004, T009
- Deliverable: upload media + metadata + reuse.
- Acceptance Criteria: validasi file aman dan audit media aktif.
- Prompt:
```text
Kerjakan T012: media library foundation dengan S3-compatible storage.
```

### T013 - News Module (Admin)
- Plan Referensi: `07`
- Prasyarat: T008, T011, T012
- Deliverable: CRUD berita + publish state.
- Acceptance Criteria: slug unik, publish/unpublish aman.
- Prompt:
```text
Kerjakan T013: modul berita admin lengkap dengan policy dan audit.
```

### T014 - Announcement Module (Admin)
- Plan Referensi: `10`
- Prasyarat: T008, T011
- Deliverable: CRUD pengumuman + publish window.
- Acceptance Criteria: konten tampil sesuai rentang waktu.
- Prompt:
```text
Kerjakan T014: modul pengumuman admin dengan validasi publish window.
```

### T015 - Gallery Module (Admin)
- Plan Referensi: `09`
- Prasyarat: T008, T011, T012
- Deliverable: CRUD album + item + sorting.
- Acceptance Criteria: urutan item stabil, relasi media valid.
- Prompt:
```text
Kerjakan T015: modul galeri admin lengkap dengan item management.
```

### T016 - School Profile Module (Admin)
- Plan Referensi: `11`
- Prasyarat: T008, T011
- Deliverable: single-record profile settings.
- Acceptance Criteria: validasi kontak berjalan, audit update tersimpan.
- Prompt:
```text
Kerjakan T016: settings profil sekolah dari admin.
```

### T017 - Theme Settings Module
- Plan Referensi: `12`
- Prasyarat: T008, T011, T012
- Deliverable: pengaturan logo + warna.
- Acceptance Criteria: validasi HEX dan fallback default aktif.
- Prompt:
```text
Kerjakan T017: theme settings dengan validasi dan fallback.
```

### T018 - Navigation Management Module
- Plan Referensi: `13`
- Prasyarat: T008, T011
- Deliverable: manajemen menu + item hierarkis.
- Acceptance Criteria: tidak ada circular parent dan urutan valid.
- Prompt:
```text
Kerjakan T018: navigation management admin sesuai plan.
```

### T019 - Audit Log Admin Viewer
- Plan Referensi: `15`
- Prasyarat: T009, T011
- Deliverable: halaman view/filter audit logs.
- Acceptance Criteria: filter actor/module/date berjalan.
- Prompt:
```text
Kerjakan T019: buat audit log viewer untuk Super Admin.
```

### T020 - Public Frontend Layout + Homepage
- Plan Referensi: `16`
- Prasyarat: T016, T017, T018
- Deliverable: layout publik dinamis dan homepage.
- Acceptance Criteria: theme/nav/profile tersambung dari admin.
- Prompt:
```text
Kerjakan T020: public layout dan homepage Blade SSR dinamis.
```

### T021 - Public News Pages
- Plan Referensi: `16`, `07`
- Prasyarat: T013, T020
- Deliverable: listing + detail berita.
- Acceptance Criteria: hanya konten published tampil.
- Prompt:
```text
Kerjakan T021: halaman berita publik SSR dengan slug dan filter published.
```

### T022 - Public Announcement Pages
- Plan Referensi: `16`, `10`
- Prasyarat: T014, T020
- Deliverable: listing + detail pengumuman.
- Acceptance Criteria: publish window diterapkan di query publik.
- Prompt:
```text
Kerjakan T022: halaman pengumuman publik dengan kontrol waktu tayang.
```

### T023 - Public Gallery Pages
- Plan Referensi: `16`, `09`
- Prasyarat: T015, T020
- Deliverable: listing + detail galeri.
- Acceptance Criteria: hanya album published, media fallback aman.
- Prompt:
```text
Kerjakan T023: halaman galeri publik SSR sesuai data published.
```

### T024 - QA Hardening Auth/Permission/Audit
- Plan Referensi: `17`
- Prasyarat: T013-T023
- Deliverable: test kritikal lintas modul.
- Acceptance Criteria: tidak ada celah unauthorized/publish leakage.
- Prompt:
```text
Kerjakan T024: QA hardening fokus auth, permission, publish visibility, dan audit completeness.
```

### T025 - QA Hardening Media and Settings
- Plan Referensi: `14`, `12`, `13`, `17`
- Prasyarat: T017, T018, T023
- Deliverable: test validasi media/theme/navigation.
- Acceptance Criteria: edge cases upload dan konfigurasi invalid tertutup.
- Prompt:
```text
Kerjakan T025: hardening media + settings dengan test edge cases.
```

### T026 - Deployment Prep (Staging)
- Plan Referensi: `17`
- Prasyarat: T005, T024, T025
- Deliverable: pipeline deploy staging + smoke checklist.
- Acceptance Criteria: staging deploy sukses dan tervalidasi.
- Prompt:
```text
Kerjakan T026: siapkan deployment staging dan jalankan smoke test release.
```

### T027 - Production Readiness & Handover
- Plan Referensi: `17`, `18`
- Prasyarat: T026
- Deliverable: runbook operasi, rollback, dan handover admin.
- Acceptance Criteria: checklist go-live selesai dan sign-off internal.
- Prompt:
```text
Kerjakan T027: final readiness production, runbook rollback, dan dokumen handover operasional.
```

---

## Aturan Agent untuk Sesi Berikutnya
- Wajib baca plan referensi task sebelum coding.
- Wajib sebut Task ID aktif di awal sesi.
- Wajib cek dependency selesai sebelum implementasi.
- Dilarang mengubah stack/arsitektur tanpa change request.
- Dilarang mengambil task kedua sebelum task aktif selesai.
- Wajib update dokumentasi sesuai `18-coding-documentation-standard.md`.

## Change Request Control
Setiap perubahan besar harus mencakup:
- alasan bisnis,
- dampak teknis,
- risiko,
- estimasi dampak timeline,
- rencana rollback.
