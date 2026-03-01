#!/bin/bash
# Fix storage permissions so the web server (www-data) can write to tenant directories.
# Run from project root: sudo ./fix-storage-permissions.sh

set -e
STORAGE_DIR="${1:-/var/www/sard/storage}"
WEB_USER="${2:-www-data}"

echo "Fixing permissions in $STORAGE_DIR for user:group $WEB_USER"
# Ensure storage and framework dirs are writable by web server
chown -R "$WEB_USER:$WEB_USER" "$STORAGE_DIR"
chmod -R 0775 "$STORAGE_DIR"
# Restore executable bit on directories (chmod 0775 keeps it)
find "$STORAGE_DIR" -type d -exec chmod 0775 {} \;
find "$STORAGE_DIR" -type f -exec chmod 0664 {} \;
echo "Done. Tenant uploads should work now."
