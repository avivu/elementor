#!/bin/bash
# Local daily runner for the release doc generator.
# Called by launchd (com.elementor.release-doc) once per day.
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

cd "$ROOT_DIR"

if [ ! -f "$SCRIPT_DIR/.env.release-doc" ]; then
    echo "Missing $SCRIPT_DIR/.env.release-doc — aborting." >&2
    exit 1
fi

set -a
source "$SCRIPT_DIR/.env.release-doc"
set +a

echo "=== $(date) ==="
python3 "$SCRIPT_DIR/run-all-versions.py" 2>&1
