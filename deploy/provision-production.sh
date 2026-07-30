#!/usr/bin/env bash

set -Eeuo pipefail

if [[ "${EUID}" -ne 0 ]]; then
    echo "Run this script with sudo."
    exit 1
fi

APP_ROOT="/var/www/deluxaesthetic"
ENV_FILE="${APP_ROOT}/shared/.env"
DB_NAME="deluxaesthetic"
DB_USER="deluxaesthetic"
DB_PASSWORD="$(openssl rand -hex 24)"

if [[ ! -f "$ENV_FILE" ]]; then
    echo "Missing ${ENV_FILE}."
    exit 1
fi

if [[ ! -f /tmp/deluxaesthetic-nginx.conf || ! -f /tmp/deluxaesthetic-supervisor.conf ]]; then
    echo "Upload the Nginx and Supervisor configuration files to /tmp first."
    exit 1
fi

mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL

DB_PASSWORD="$DB_PASSWORD" php -r '
    $path = "/var/www/deluxaesthetic/shared/.env";
    $updates = [
        "DB_CONNECTION" => "mysql",
        "DB_HOST" => "127.0.0.1",
        "DB_PORT" => "3306",
        "DB_DATABASE" => "deluxaesthetic",
        "DB_USERNAME" => "deluxaesthetic",
        "DB_PASSWORD" => getenv("DB_PASSWORD"),
        "PAYMENT_MOCK" => "false",
    ];
    $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
    $written = [];
    foreach ($lines as &$line) {
        if (!preg_match("/^([A-Z][A-Z0-9_]*)=/", $line, $match)) {
            continue;
        }
        $key = $match[1];
        if (array_key_exists($key, $updates)) {
            $line = $key."=".$updates[$key];
            $written[$key] = true;
        }
    }
    unset($line);
    foreach ($updates as $key => $value) {
        if (!isset($written[$key])) {
            $lines[] = $key."=".$value;
        }
    }
    file_put_contents($path, implode(PHP_EOL, $lines).PHP_EOL, LOCK_EX);
'

chown deploy:www-data "$ENV_FILE"
chmod 640 "$ENV_FILE"

install -m 644 /tmp/deluxaesthetic-nginx.conf /etc/nginx/sites-available/deluxaesthetic
ln -sfn /etc/nginx/sites-available/deluxaesthetic /etc/nginx/sites-enabled/deluxaesthetic
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

install -m 644 /tmp/deluxaesthetic-supervisor.conf /etc/supervisor/conf.d/deluxaesthetic-worker.conf

SCHEDULER_ENTRY='* * * * * cd /var/www/deluxaesthetic/current && /usr/bin/php artisan schedule:run >> /dev/null 2>&1'
(
    crontab -u deploy -l 2>/dev/null | grep -v 'artisan schedule:run' || true
    echo "$SCHEDULER_ENTRY"
) | crontab -u deploy -

echo "PROVISIONING_COMPLETE"
