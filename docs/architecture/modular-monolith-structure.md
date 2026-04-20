# Modular Monolith Structure - Baseline (T002)

Dokumen ini menjelaskan struktur awal codebase setelah bootstrap Laravel 13 + Filament 5.

## Prinsip
- Satu aplikasi Laravel untuk admin panel dan frontend publik.
- Domain dipisah di `app/Modules`.
- UI/admin hanya surface; business logic diletakkan di actions/services modul.

## Struktur Direktori (Ringkas)
- `app/Modules/Auth`
- `app/Modules/Users`
- `app/Modules/RolesPermissions`
- `app/Modules/Dashboard`
- `app/Modules/News`
- `app/Modules/Announcements`
- `app/Modules/Galleries`
- `app/Modules/MediaLibrary`
- `app/Modules/SchoolProfile`
- `app/Modules/ThemeSettings`
- `app/Modules/NavigationMenus`
- `app/Modules/AuditLogs`
- `app/Modules/Shared`

## Relasi dengan Roadmap
- Struktur ini menjadi fondasi untuk T006+ (auth, authorization, audit).
- Modul konten/settings mengikuti boundary ini pada task implementasi masing-masing.

## Guardrails
- Jangan memindahkan logic domain ke Blade/Filament resource langsung.
- Jangan membuat modul baru di luar master plan tanpa change request.
