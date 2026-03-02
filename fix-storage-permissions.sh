#!/usr/bin/env bash
# Fix storage and cache permissions for Laravel (run on live/server)
# Usage: sudo bash fix-storage-permissions.sh [web-user]
# Example: sudo bash fix-storage-permissions.sh www-data

set -e
WEB_USER="${1:-www-data}"
APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Setting ownership to $WEB_USER for storage and bootstrap/cache..."
chown -R "$WEB_USER:$WEB_USER" "$APP_ROOT/storage" "$APP_ROOT/bootstrap/cache"

echo "Setting directory permissions (775)..."
find "$APP_ROOT/storage" "$APP_ROOT/bootstrap/cache" -type d -exec chmod 775 {} \;

echo "Setting file permissions (664)..."
find "$APP_ROOT/storage" "$APP_ROOT/bootstrap/cache" -type f -exec chmod 664 {} \;

echo "Done. You can now run: php artisan migrate:fresh --seed (as $WEB_USER or a user in group $WEB_USER)"
echo "Or run: sudo -u $WEB_USER php artisan migrate:fresh --seed"
