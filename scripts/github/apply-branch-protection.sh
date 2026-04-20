#!/usr/bin/env bash
set -euo pipefail

# Apply branch protection rules for `main` using GitHub CLI.
# Usage:
#   ./scripts/github/apply-branch-protection.sh <owner/repo>
# Example:
#   ./scripts/github/apply-branch-protection.sh sekolah-org/cms-sekolah

if ! command -v gh >/dev/null 2>&1; then
  echo "Error: gh CLI not found. Install GitHub CLI first." >&2
  exit 1
fi

if [ "$#" -ne 1 ]; then
  echo "Usage: $0 <owner/repo>" >&2
  exit 1
fi

REPO="$1"

gh api \
  --method PUT \
  -H "Accept: application/vnd.github+json" \
  "/repos/${REPO}/branches/main/protection" \
  --input - <<'JSON'
{
  "required_status_checks": {
    "strict": true,
    "contexts": [
      "CI / lint",
      "CI / test"
    ]
  },
  "enforce_admins": true,
  "required_pull_request_reviews": {
    "dismiss_stale_reviews": true,
    "require_code_owner_reviews": false,
    "required_approving_review_count": 1
  },
  "restrictions": null,
  "required_linear_history": true,
  "allow_force_pushes": false,
  "allow_deletions": false,
  "block_creations": false,
  "required_conversation_resolution": true,
  "lock_branch": false,
  "allow_fork_syncing": true
}
JSON

echo "Branch protection applied for ${REPO}:main"
echo "Required checks configured: CI / lint, CI / test"
