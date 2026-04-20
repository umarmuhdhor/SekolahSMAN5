# 17 - Testing, Security, and Deployment Plan

## Tujuan Dokumen
Menyatukan strategi QA, hardening keamanan, dan persiapan deployment agar rilis stabil.

## Strategi Testing
### Prioritas Tinggi
- Auth success/failure/logout.
- Authorization denied/allowed per role.
- Publish visibility (published vs unpublished).
- Publish window pengumuman.
- Audit event completeness.
- Validasi upload media.

### Jenis Test
- Feature test untuk alur admin.
- Integration test lintas modul kritikal.
- Smoke test admin dan frontend publik.

## Security Baseline
- CSRF protection.
- Input validation ketat.
- Session hardening.
- Rate limit endpoint sensitif.
- Secure file upload pipeline.

## CI/CD Baseline
- GitHub Actions untuk lint/check/test.
- Status check wajib sebelum merge.
- `main` harus selalu deployable.

## Deployment Strategy
- Local -> Staging -> Production.
- Release bertahap per phase.
- Gunakan runbook rollback.

## Rollback Thinking
- Backup DB tervalidasi.
- Rollback aplikasi dan migrasi aman.
- Validasi pasca-rollback di staging.

## Acceptance Criteria
- Test kritikal berjalan di CI.
- Staging deploy berhasil + smoke pass.
- Prosedur rollback terdokumentasi dan diuji.

## Dependensi
- Semua plan modul inti (07-16).
- Workflow delivery di roadmap.

## Prompt Eksekusi Cepat
```text
Kerjakan QA hardening dan deployment prep sesuai 17-testing-security-and-deployment-plan.md.
Prioritaskan test kritikal auth/permission/publish/audit/media, setup CI checks, staging validation, dan rollback runbook.
```
