#!/usr/bin/env bash
set -euo pipefail

base_url="${1:-${STAGING_APP_URL:-}}"

if [[ -z "${base_url}" ]]; then
  echo "STAGING_APP_URL belum diberikan."
  exit 1
fi

base_url="${base_url%/}"

endpoints=(
  "/"
  "/berita"
  "/pengumuman"
  "/galeri"
  "/admin/login"
)

echo "Mulai smoke check staging di: ${base_url}"

overall_status=0

for endpoint in "${endpoints[@]}"; do
  url="${base_url}${endpoint}"
  tmp_file="$(mktemp)"

  http_code="$(curl --silent --show-error --location --max-time 20 --output "${tmp_file}" --write-out "%{http_code}" "${url}")"

  if [[ "${http_code}" =~ ^2[0-9][0-9]$ ]]; then
    echo "[PASS] ${url} -> HTTP ${http_code}"
  else
    echo "[FAIL] ${url} -> HTTP ${http_code}"
    echo "Cuplikan respons:" 
    sed -n '1,8p' "${tmp_file}"
    overall_status=1
  fi

  rm -f "${tmp_file}"
done

if [[ ${overall_status} -ne 0 ]]; then
  echo "Smoke check staging gagal."
  exit 1
fi

echo "Smoke check staging selesai dan seluruh endpoint lulus."
