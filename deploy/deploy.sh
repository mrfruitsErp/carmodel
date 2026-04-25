#!/usr/bin/env bash
# =====================================================================
# CarModel ERP — Deploy script
# Uso: bash /var/www/carmodel/deploy/deploy.sh
# Idempotente: si può lanciare più volte senza problemi.
# =====================================================================
set -euo pipefail

APP_DIR="/var/www/carmodel"
BRANCH="${BRANCH:-main}"
PHP_BIN="/usr/bin/php8.2"

# Colori
GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; NC='\033[0m'
log()  { echo -e "${GREEN}▶${NC} $*"; }
warn() { echo -e "${YELLOW}⚠${NC} $*"; }
err()  { echo -e "${RED}✖${NC} $*"; exit 1; }

[ -d "$APP_DIR" ] || err "Cartella $APP_DIR non esiste"
cd "$APP_DIR"

# ─── 1. Modalità manutenzione ──────────────────────────────────────
if [ -f artisan ]; then
  log "Attivo modalità manutenzione..."
  $PHP_BIN artisan down --render="errors::503" --secret="deploy-$$" || true
fi
trap 'log "Disattivo manutenzione (cleanup)..."; $PHP_BIN artisan up || true' EXIT

# ─── 2. Git pull ───────────────────────────────────────────────────
log "Git pull (branch $BRANCH)..."
git fetch --all --prune
git reset --hard "origin/$BRANCH"
COMMIT=$(git rev-parse --short HEAD)
log "Commit attuale: $COMMIT"

# ─── 3. Composer ───────────────────────────────────────────────────
log "Composer install (no-dev, ottimizzato)..."
COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev --no-interaction --prefer-dist \
    --optimize-autoloader

# ─── 4. .env check ─────────────────────────────────────────────────
if [ ! -f .env ]; then
  warn "File .env mancante. Copia da deploy/.env.production.example:"
  warn "  cp deploy/.env.production.example .env && nano .env"
  err  "Interrompo deploy."
fi

# Genera APP_KEY se mancante
if ! grep -q '^APP_KEY=base64:' .env; then
  log "Genero APP_KEY..."
  $PHP_BIN artisan key:generate --force
fi

# ─── 5. Permessi ───────────────────────────────────────────────────
log "Setto permessi storage e bootstrap/cache..."
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ─── 6. Storage link ───────────────────────────────────────────────
if [ ! -L public/storage ]; then
  log "Creo symlink public/storage..."
  $PHP_BIN artisan storage:link
fi

# ─── 7. Migrazioni ─────────────────────────────────────────────────
log "Migrazioni database..."
$PHP_BIN artisan migrate --force --no-interaction

# ─── 8. Cache produzione ───────────────────────────────────────────
log "Pulizia cache..."
$PHP_BIN artisan optimize:clear
log "Caching config/route/view..."
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache || true

# ─── 9. Restart PHP-FPM (svuota opcache) ──────────────────────────
log "Reload PHP-FPM..."
systemctl reload php8.2-fpm || systemctl restart php8.2-fpm

# ─── 10. Esci da manutenzione ──────────────────────────────────────
log "Riattivo il sito..."
$PHP_BIN artisan up
trap - EXIT

echo
echo -e "${GREEN}═══════════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Deploy completato — commit $COMMIT${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════════════${NC}"
