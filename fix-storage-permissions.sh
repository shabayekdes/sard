#!/usr/bin/env bash
# Fix Laravel storage & cache permissions (run on live server)
# Usage: sudo bash fix-storage-permissions.sh [path]
# Example: sudo bash fix-storage-permissions.sh /var/www/sard

set -e
APP_PATH="${1:-/var/www/sard}"

if [[ ! -d "$APP_PATH" ]]; then
  echo "Error: App path $APP_PATH does not exist."
  exit 1
fi

# Common web server user (use www-data for Apache/Nginx, or your deploy user)
WEB_USER="${WEB_USER:-www-data}"

echo "Fixing permissions for $APP_PATH (user: $WEB_USER)"

# Create dirs if missing
mkdir -p "$APP_PATH/storage/logs"
mkdir -p "$APP_PATH/storage/framework/cache"
mkdir -p "$APP_PATH/storage/framework/sessions"
mkdir -p "$APP_PATH/storage/framework/views"
mkdir -p "$APP_PATH/bootstrap/cache"

# Ownership: web user and group
chown -R "$WEB_USER:$WEB_USER" "$APP_PATH/storage" "$APP_PATH/bootstrap/cache"

# Directories 775, files 664
chmod -R 775 "$APP_PATH/storage" "$APP_PATH/bootstrap/cache"
find "$APP_PATH/storage" "$APP_PATH/bootstrap/cache" -type f -exec chmod 664 {} \;

echo "Done. Run: php artisan config:clear && php artisan cache:clear"
