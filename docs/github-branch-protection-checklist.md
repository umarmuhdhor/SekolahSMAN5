# GitHub Branch Protection Checklist (Main)

Dokumen ini dipakai untuk menuntaskan bagian T001 yang tidak bisa dikunci dari local file saja.

## Target Branch
- `main`

## Rule Wajib
- [ ] Require a pull request before merging
- [ ] Require approvals: minimal 1
- [ ] Dismiss stale pull request approvals when new commits are pushed
- [ ] Require status checks to pass before merging
- [ ] Require conversation resolution before merging
- [ ] Require linear history (opsional, direkomendasikan)
- [ ] Restrict who can push to matching branches
- [ ] Do not allow force pushes
- [ ] Do not allow deletions

## Required Status Checks
Isi setelah CI baseline (T005) aktif:
- [x] CI / lint
- [x] CI / test

## Catatan
- Branch protection harus diatur di GitHub repository settings.
- Setelah T005 selesai, update daftar status checks pada dokumen ini.

## Opsi Otomatisasi
Jika `gh` CLI sudah login dan repository sudah ada, jalankan:

```bash
./scripts/github/apply-branch-protection.sh <owner/repo>
```

Contoh:

```bash
./scripts/github/apply-branch-protection.sh sekolah-org/cms-sekolah
```
