# Navigation Management Module Admin (T018)

Dokumen ini menjelaskan implementasi modul manajemen menu navigasi publik pada panel admin CMS.

## Cakupan T018
- CRUD navigation menu (`/admin/navigation-menus`).
- CRUD navigation item (`/admin/navigation-items`).
- Hierarki parent-child item dengan validasi anti-circular.
- Validasi urutan item unik pada parent yang sama.
- Validasi backend link item (URL internal/eksternal atau route name valid).
- Toggle visibilitas item (`is_visible`).
- Enforcement authorization backend via policy.
- Audit event navigation:
  - `navigation.menu_updated`
  - `navigation.item_created`
  - `navigation.item_updated`
  - `navigation.item_deleted`
  - `navigation.items_reordered`

## Komponen Utama
- Migration:
  - `database/migrations/2026_04_20_000009_create_navigation_menus_tables.php`
- Models:
  - `app/Modules/NavigationMenus/Models/NavigationMenu.php`
  - `app/Modules/NavigationMenus/Models/NavigationItem.php`
- Actions:
  - `AssertValidNavigationItemHierarchyAction`
  - `AssertUniqueNavigationSortOrderAction`
  - `ValidateNavigationLinkAction`
  - `NormalizeNavigationItemSortOrderAction`
- Policies:
  - `app/Policies/NavigationMenuPolicy.php`
  - `app/Policies/NavigationItemPolicy.php`
- Observers:
  - `NavigationMenuAuditObserver`
  - `NavigationItemAuditObserver`
- Filament resources:
  - `app/Filament/Resources/NavigationMenus/*`
  - `app/Filament/Resources/NavigationItems/*`

## Aturan Authorization
- `navigation.view` untuk akses daftar/detail menu dan item.
- `navigation.update` untuk create/update/delete menu dan item.
- User tanpa permission `navigation.*` ditolak backend (403), bukan hanya dibatasi UI.

## Aturan Data
- `sort_order` item harus unik dalam kombinasi menu + parent yang sama.
- Circular reference parent-child ditolak server-side.
- Parent item wajib berasal dari menu yang sama.
- Link type `url` wajib URL http/https valid atau path internal (`/...`).
- Link type `route` wajib route name yang tersedia di Laravel route registry.

## Catatan Integrasi
- Dashboard quick link `Navigation` kini berstatus ready dan mengarah ke module navigation menus.
- Fondasi ini disiapkan untuk konsumsi layout frontend publik dinamis pada task public frontend.
