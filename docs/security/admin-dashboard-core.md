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
