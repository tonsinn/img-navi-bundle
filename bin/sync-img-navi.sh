#!/usr/bin/env bash
# Synchronisiert ~/img-navi in die lokale DDEV-Instanz (~/contao57) und
# aktualisiert dort Autoloader, Cache und Assets.
#
# Aufruf: bin/sync-img-navi.sh [--migrate]
#   --migrate  zusätzlich contao:migrate ausführen (nach DCA-Änderungen nötig)
set -euo pipefail

SRC="$(cd "$(dirname "$0")/.." && pwd)/"
PROJECT="${HOME}/contao57"
DEST="${PROJECT}/img-navi-bundle/"
VENDOR="${PROJECT}/vendor/tonsinn/img-navi-bundle"

[ -d "${PROJECT}" ] || { echo "DDEV-Projekt ${PROJECT} nicht gefunden."; exit 1; }

rsync -a --delete \
  --exclude='.git' \
  --exclude='.claude' \
  --exclude='vendor' \
  --exclude='testinstall.env' \
  --exclude='composer.lock' \
  "${SRC}" "${DEST}"

# Falls Composer das Paket kopiert statt verlinkt hat, vendor/ ebenfalls aktualisieren
if [ -d "${VENDOR}" ] && [ ! -L "${VENDOR}" ]; then
  rsync -a --delete --exclude='.git' "${DEST}" "${VENDOR}/"
fi

cd "${PROJECT}"
ddev exec composer dump-autoload

if [ "${1:-}" = "--migrate" ]; then
  ddev exec php vendor/bin/contao-console contao:migrate --no-interaction
fi

ddev exec php vendor/bin/contao-console cache:clear
ddev exec php vendor/bin/contao-console contao:symlinks
ddev exec php vendor/bin/contao-console assets:install --symlink

echo "Bundle synchronisiert, Cache geleert und Assets verlinkt."
