#!/usr/bin/env bash
#
# Deploys the current main branch on the server.
#
# Run as root (or via sudo) from the application root:
#
#   sudo bash scripts/deploy.sh
#
# It lives in the repository rather than being typed out each time for one
# reason: a step left out of an ad-hoc command is invisible. Two deploys
# went out without the php-fpm reload at the end, and because OPcache
# holds the compiled Blade views in shared memory, the site kept serving
# the previous templates while every file on disk was correct — which is a
# very confusing way to lose an afternoon. The reload is what clears that
# shared memory, so it is the last line here and it is not optional.

set -euo pipefail

cd "$(dirname "$0")/.."

echo "==> Pulling"
git pull --ff-only origin main

echo "==> Dependencies"
# --no-dev and the optimised autoloader: this is a production host.
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "==> Database"
php artisan migrate --force

echo "==> Image variants"
# Idempotent: only writes a variant that is missing or older than its
# source, so this costs nothing on a deploy that changed no images.
php scripts/make-image-variants.php

echo "==> Front-end build"
# Not --omit=dev: Vite and Tailwind are devDependencies and the build
# needs them. public/build is gitignored, so the bundle is produced here
# rather than committed.
npm ci --silent || npm install --silent
npm run build

echo "==> Caches"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Ownership and modes"
# Seeding or caching under sudo leaves root-owned files that php-fpm
# cannot traverse; this is what made the stored PDFs unreachable once.
chown -R hxpeed:www-data storage bootstrap/cache public/build public/images
find storage -type d -exec chmod 2775 {} +
find storage -type f -exec chmod 664 {} +

echo "==> Reloading php-fpm (clears the OPcache)"
systemctl reload php8.5-fpm

echo "==> Done"
