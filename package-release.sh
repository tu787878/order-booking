#!/bin/sh
# Create a customer-ready WordPress plugin ZIP without development files or credentials.
set -eu

project_dir=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
slug=order-booking
version=$(sed -n 's/^ \* Version: //p' "$project_dir/booking-order.php" | head -n 1)

if [ -z "$version" ]; then
    echo "Could not determine the plugin version." >&2
    exit 1
fi

build_dir="$project_dir/.build/$slug"
dist_dir="$project_dir/dist"
archive="$dist_dir/$slug-$version.zip"

rm -rf "$build_dir"
mkdir -p "$build_dir" "$dist_dir"

rsync -a \
    --exclude '.git/' \
    --exclude '.DS_Store' \
    --exclude '.agents/' \
    --exclude '.codex/' \
    --exclude '.build/' \
    --exclude 'dist/' \
    --exclude 'tests/' \
    --exclude 'DEVSENSE/' \
    --exclude 'AGENTS.md' \
    --exclude '.gitignore' \
    --exclude 'composer.json' \
    --exclude 'composer.lock' \
    --exclude 'package-release.sh' \
    "$project_dir/" "$build_dir/"

rm -f "$archive"
(cd "$project_dir/.build" && zip -qr "$archive" "$slug")
rm -rf "$build_dir"

echo "Created $archive"
