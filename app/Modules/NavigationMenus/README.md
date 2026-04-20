# Module NavigationMenus

## Tujuan
Mengelola struktur menu navigasi publik secara dinamis.

## Status
Implementasi admin aktif pada T018:
- manajemen menu (`/admin/navigation-menus`) dan item (`/admin/navigation-items`),
- validasi backend link URL/route, hierarki parent-child, dan sort order unik per parent,
- audit mutasi navigation (`navigation.menu_updated`, `navigation.item_created`, `navigation.item_updated`, `navigation.item_deleted`, `navigation.items_reordered`).

## Subdirektori
- Actions
- Policies
- DTOs
- Support
