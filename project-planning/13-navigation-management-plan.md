# 13 - Navigation Management Plan

## Tujuan Dokumen
Mendefinisikan sistem manajemen menu navigasi publik dari panel admin.

## Scope
- CRUD menu (mis. header, footer).
- CRUD item menu (label, URL, target, urutan).
- Hierarki parent-child terbatas.
- Toggle visibilitas item.

## Data
- `navigation_menus`
- `navigation_items`

## Business Rules
- Urutan item unik per parent.
- Cegah circular reference parent-child.
- Validasi URL/route sebelum simpan.

## Authorization
- `navigation.view`
- `navigation.update`

## Audit Events
- navigation.menu_updated
- navigation.item_created
- navigation.item_updated
- navigation.item_deleted
- navigation.items_reordered

## Dependensi
- `05-authentication-and-authorization-plan.md`
- `15-audit-log-plan.md`
- `16-public-frontend-plan.md`

## Acceptance Criteria
- Admin berizin dapat mengatur menu publik.
- Struktur menu valid dan stabil.
- Update menu terlihat di frontend.
- Semua mutasi menu tercatat audit.

## Out of Scope
- Personalisasi menu per user.
- Mega menu kompleks.

## Prompt Eksekusi Cepat
```text
Kerjakan navigation management sesuai 13-navigation-management-plan.md.
Fokus pada CRUD menu/item, validasi hierarki, urutan, authorization, dan audit event.
Jangan menambah personalisasi menu per pengguna.
```
