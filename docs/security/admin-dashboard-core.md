# Admin Dashboard Core (T011)

Dokumen ini menjelaskan fondasi dashboard admin dan navigation guard berbasis permission.

## Cakupan T011
- Dashboard admin utama pada panel Filament.
- Widget ringkasan operasional minimum (users, roles, permissions).
- Quick links modul inti (users, news, announcements, galleries, media, settings, audit logs).
- Filter visibilitas shortcut/modul berdasarkan permission user.

## Komponen Utama
- Page dashboard:
  - `app/Filament/Pages/Dashboard.php`
- Widgets:
  - `app/Filament/Widgets/AdminOperationalOverviewWidget.php`
  - `app/Filament/Widgets/AdminQuickLinksWidget.php`
- Quick links action:
  - `app/Modules/Dashboard/Actions/GetDashboardQuickLinksAction.php`
- Widget view:
  - `resources/views/filament/widgets/admin-quick-links-widget.blade.php`

## Aturan Authorization
- Dashboard page tetap dijaga gate `panel.dashboard.view`.
- Quick links hanya tampil jika user memiliki permission modul terkait.
- Resource navigation mengikuti policy `viewAny` (contoh: `UserResource` hanya tampil untuk user berpermission `users.view`).

## Catatan Scope
- T011 tidak mengimplementasikan modul bisnis baru.
- Link modul yang belum diimplementasikan tetap ditampilkan sebagai `Segera Hadir` bila permission user memenuhi.
- Sejak T012, shortcut `Media` sudah aktif dan mengarah ke modul media library.
- Sejak T013, shortcut `News` sudah aktif dan mengarah ke modul news admin.
- Sejak T014, shortcut `Announcements` sudah aktif dan mengarah ke modul announcements admin.
- Sejak T015, shortcut `Galleries` sudah aktif dan mengarah ke modul galleries admin.
- Sejak T016, shortcut `School Profile` sudah aktif dan mengarah ke modul school profile admin.
- Sejak T017, shortcut `Theme Settings` sudah aktif dan mengarah ke modul theme settings admin.
- Sejak T018, shortcut `Navigation` sudah aktif dan mengarah ke modul navigation management admin.
- Sejak T019, shortcut `Audit Logs` sudah aktif dan mengarah ke modul audit log viewer admin.
