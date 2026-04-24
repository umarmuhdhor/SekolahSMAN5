# CI Baseline (T005)

Workflow CI baseline tersedia di `.github/workflows/ci.yml`.

## Trigger
- `pull_request` ke `main`
- `push` ke `main`

## Job Wajib
1. `lint`
   - `composer validate --strict`
   - `vendor/bin/pint --test`
2. `test`
   - setup env testing
   - migrate sqlite
   - `php artisan test --testsuite=Feature`

## Required Status Checks (Branch Protection)
- `CI / lint`
- `CI / test`

## Catatan
- Pipeline baseline menggunakan SQLite untuk kecepatan feedback PR.
- Integration test PostgreSQL/Redis dapat ditambahkan pada task QA hardening berikutnya tanpa mengubah stack inti.
- Deploy staging automation tersedia di `.github/workflows/deploy-staging.yml` dengan smoke validation endpoint kritikal.
