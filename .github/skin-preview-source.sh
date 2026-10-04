#!/bin/bash
# Points tests/skin-preview/.wikven.yaml at the $SIDE pack that skin-preview.yml serves.
set -euo pipefail

file=tests/skin-preview/.wikven.yaml
git checkout -- "$file"
URL="http://host.docker.internal:8000/$SIDE.tar.gz" \
SHA=$(sha256sum < "$RUNNER_TEMP/serve/$SIDE.tar.gz" | cut -d' ' -f1) \
  yq -i '.config.WikvenRepositories.Femiwiki = {"tarball": strenv(URL), "sha256": strenv(SHA)}' "$file"
cat "$file"
