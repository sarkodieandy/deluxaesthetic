#!/usr/bin/env bash

set -Eeuo pipefail

RELEASE_ID="${1:-}"
ARCHIVE_PATH="${2:-}"
PRODUCTION_URL="${3:-}"
APP_ROOT="/var/www/deluxaesthetic"
RELEASES_DIR="${APP_ROOT}/releases"
SHARED_DIR="${APP_ROOT}/shared"
CURRENT_LINK="${APP_ROOT}/current"

if [[ ! "$RELEASE_ID" =~ ^[0-9]+-[a-f0-9]{40}$ ]]; then
    echo "Invalid release identifier."
    exit 1
fi

if [[ ! -f "$ARCHIVE_PATH" ]]; then
    echo "Release archive not found: $ARCHIVE_PATH"
    exit 1
fi

if [[ ! -f "${SHARED_DIR}/.env" ]]; then
    echo "Missing ${SHARED_DIR}/.env. Create the production environment file first."
    exit 1
fi

RELEASE_DIR="${RELEASES_DIR}/${RELEASE_ID}"
PREVIOUS_RELEASE=""

if [[ -L "$CURRENT_LINK" ]]; then
    PREVIOUS_RELEASE="$(readlink -f "$CURRENT_LINK")"
fi

cleanup_uploads() {
    rm -f "$ARCHIVE_PATH" "/tmp/deluxaesthetic-release-${RELEASE_ID}.sh"
}
trap cleanup_uploads EXIT

mkdir -p \
    "$RELEASE_DIR" \
    "${SHARED_DIR}/storage/app/public" \
    "${SHARED_DIR}/storage/framework/cache/data" \
    "${SHARED_DIR}/storage/framework/sessions" \
    "${SHARED_DIR}/storage/framework/views" \
    "${SHARED_DIR}/storage/logs"

tar -xzf "$ARCHIVE_PATH" -C "$RELEASE_DIR"

rm -rf "${RELEASE_DIR}/storage"
ln -s "${SHARED_DIR}/storage" "${RELEASE_DIR}/storage"
ln -s "${SHARED_DIR}/.env" "${RELEASE_DIR}/.env"

mkdir -p "${RELEASE_DIR}/bootstrap/cache"
chgrp -R www-data "$RELEASE_DIR"
chmod -R g+rX,o-rwx "$RELEASE_DIR"
find "$RELEASE_DIR" -type d -exec chmod g+s {} +
chmod -R ug+rwX "${RELEASE_DIR}/bootstrap/cache"

cd "$RELEASE_DIR"

php artisan config:clear
php artisan event:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force
php artisan storage:link
php artisan optimize

ln -s "$RELEASE_DIR" "${CURRENT_LINK}.new"
mv -Tf "${CURRENT_LINK}.new" "$CURRENT_LINK"

php "${CURRENT_LINK}/artisan" queue:restart

if [[ -n "$PRODUCTION_URL" ]]; then
    if ! curl --fail --silent --show-error --location \
        --retry 5 --retry-delay 2 --max-time 20 \
        "${PRODUCTION_URL%/}/up" >/dev/null; then
        if [[ -n "$PREVIOUS_RELEASE" && -d "$PREVIOUS_RELEASE" ]]; then
            ln -s "$PREVIOUS_RELEASE" "${CURRENT_LINK}.rollback"
            mv -Tf "${CURRENT_LINK}.rollback" "$CURRENT_LINK"
            php "${CURRENT_LINK}/artisan" queue:restart || true
        fi

        echo "Health check failed; the previous release was restored."
        exit 1
    fi
fi

mapfile -t OLD_RELEASES < <(
    find "$RELEASES_DIR" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' \
        | sort -rn \
        | tail -n +6 \
        | cut -d' ' -f2-
)

for OLD_RELEASE in "${OLD_RELEASES[@]}"; do
    if [[ "$OLD_RELEASE" == "${RELEASES_DIR}/"* && "$OLD_RELEASE" != "$(readlink -f "$CURRENT_LINK")" ]]; then
        rm -rf -- "$OLD_RELEASE"
    fi
done

echo "Release ${RELEASE_ID} is live."
