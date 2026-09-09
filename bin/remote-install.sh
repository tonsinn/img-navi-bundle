#!/usr/bin/env bash
# Installiert bzw. aktualisiert das Bundle auf dem Remote-Testserver aus dem
# lokalen Arbeitsstand. Das Bundle wird als Composer-Path-Repository unter
# $REMOTE_PATH/bundles/img-navi-bundle eingebunden.
#
# ACHTUNG: Das Path-Repository ist canonical und hat Vorrang vor Packagist.
# Solange es eingetragen ist, laesst sich die Instanz NICHT auf eine
# veroeffentlichte Version (z. B. ^1.0) umstellen - Composer bricht mit
# "has higher repository priority" ab. Zum Wechsel auf die Release-Version:
#   composer config --unset repositories.imgnavi
#   composer require tonsinn/img-navi-bundle:^1.0
# Dieses Skript macht die Umstellung wieder rueckgaengig.
#
# Zugangsdaten: testinstall.env (siehe testinstall.env.example)
set -euo pipefail

HERE="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="${HERE}/testinstall.env"

[ -f "${ENV_FILE}" ] || { echo "testinstall.env fehlt (Vorlage: testinstall.env.example)"; exit 1; }
set -a; . "${ENV_FILE}"; set +a

PORT="${REMOTE_PORT:-22}"
TARGET="${REMOTE_USER}@${REMOTE_HOST}"
BUNDLE_DIR="${REMOTE_PATH}/bundles/img-navi-bundle"
PHPBIN="${REMOTE_PHP:-keyhelp-php85}"
COMPOSER="${REMOTE_COMPOSER:-composer}"

if [ -n "${REMOTE_KEY:-}" ]; then
  RSH="ssh -i ${REMOTE_KEY} -p ${PORT} -o StrictHostKeyChecking=accept-new"
else
  export SSHPASS="${REMOTE_PASSWORD}"
  RSH="sshpass -e ssh -p ${PORT} -o StrictHostKeyChecking=accept-new"
fi

echo "==> PHP-Version auf dem Server prüfen"
${RSH} "${TARGET}" "export PHPBIN='${PHPBIN}'; bash -s" <<'REMOTE'
if ! command -v "$PHPBIN" >/dev/null 2>&1; then
  if [ -x /opt/keyhelp/php/8.5/bin/php ]; then
    echo "HINWEIS: '$PHPBIN' nicht im PATH, /opt/keyhelp/php/8.5/bin/php verwenden."
  else
    echo "FEHLER: Keine PHP-8.5-Binary gefunden ('$PHPBIN')." >&2
    exit 1
  fi
else
  "$PHPBIN" -v | head -1
fi
REMOTE

echo "==> Dateien übertragen nach ${BUNDLE_DIR}"
${RSH} "${TARGET}" "mkdir -p '${BUNDLE_DIR}'"
rsync -az --delete -e "${RSH}" \
  --exclude='.git' \
  --exclude='.claude' \
  --exclude='vendor' \
  --exclude='testinstall.env' \
  --exclude='composer.lock' \
  "${HERE}/" "${TARGET}:${BUNDLE_DIR}/"

echo "==> Composer und Contao-Migration auf dem Server"
${RSH} "${TARGET}" "export P='${REMOTE_PATH}' PHPBIN='${PHPBIN}' CMP='${COMPOSER}'; bash -s" <<'REMOTE'
set -e
cd "$P"

COMPOSER_BIN="$(command -v "$CMP" || echo "$CMP")"

"$PHPBIN" "$COMPOSER_BIN" config repositories.imgnavi \
  '{"type":"path","url":"bundles/img-navi-bundle","options":{"symlink":true,"versions":{"tonsinn/img-navi-bundle":"dev-main"}}}'
"$PHPBIN" "$COMPOSER_BIN" require tonsinn/img-navi-bundle:@dev --no-interaction --no-progress

# Cache VOR der Migration leeren: contao:migrate liest die DCA-Definitionen aus
# dem Cache. Mit einem alten Cache kennt es neue Felder noch nicht und legt die
# zugehoerigen Spalten nicht an.
"$PHPBIN" vendor/bin/contao-console cache:clear

# Twig-Syntax pruefen, bevor migriert wird: ein Fehler im Template legt sonst
# das Frontend lahm und faellt erst beim Aufruf auf.
"$PHPBIN" vendor/bin/contao-console lint:twig bundles/img-navi-bundle/contao/templates

"$PHPBIN" vendor/bin/contao-console contao:migrate --no-interaction
"$PHPBIN" vendor/bin/contao-console assets:install
REMOTE

echo "Fertig. Test-Installation: ${REMOTE_URL:-<REMOTE_URL nicht gesetzt>}"
