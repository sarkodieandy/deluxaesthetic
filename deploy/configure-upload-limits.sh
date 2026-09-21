#!/usr/bin/env bash

set -Eeuo pipefail

if [[ "${EUID}" -ne 0 ]]; then
    echo "Run this script with sudo."
    exit 1
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
NGINX_SITE="/etc/nginx/sites-available/deluxaesthetic"
PHP_FPM_INI_DIR="/etc/php/8.3/fpm/conf.d"
PHP_UPLOAD_INI="${PHP_FPM_INI_DIR}/99-deluxaesthetic-uploads.ini"

if [[ ! -f "$NGINX_SITE" ]]; then
    echo "Missing Nginx site: ${NGINX_SITE}"
    exit 1
fi

if [[ ! -f "${SCRIPT_DIR}/php-upload.ini" ]]; then
    echo "Missing ${SCRIPT_DIR}/php-upload.ini"
    exit 1
fi

if ! grep -qE 'client_max_body_size[[:space:]]+[^;]+;' "$NGINX_SITE"; then
    echo "The Nginx site does not contain client_max_body_size; refusing an unsafe automatic edit."
    exit 1
fi

sed -i -E 's/client_max_body_size[[:space:]]+[^;]+;/client_max_body_size 256M;/' "$NGINX_SITE"
install -d -m 755 "$PHP_FPM_INI_DIR"
install -m 644 "${SCRIPT_DIR}/php-upload.ini" "$PHP_UPLOAD_INI"

nginx -t
systemctl reload nginx
systemctl restart php8.3-fpm

echo "UPLOAD_LIMITS_CONFIGURED"
grep -E 'client_max_body_size' "$NGINX_SITE"
php-fpm8.3 -i 2>/dev/null | grep -E '^(upload_max_filesize|post_max_size|memory_limit)' | head -n 6
