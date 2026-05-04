#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Laravel 12 production deploy
# Ishlatish: bash deploy.sh
# =============================================================================

set -euo pipefail

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; NC='\033[0m'
step() { echo -e "\n${GREEN}==>${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[x]${NC} $1"; exit 1; }

# --fix-perms: permissions buzilganda yoki birinchi deployda ishlatish
FIX_PERMS=false
for arg in "$@"; do [[ "$arg" == "--fix-perms" ]] && FIX_PERMS=true; done

DEPLOY_START=$(date +%s)

# =============================================================================
# 1. Muhit tekshirish
# =============================================================================
step "Muhit tekshirilmoqda..."

php -r "version_compare(PHP_VERSION, '8.2.0', '<') && exit(1);" \
    || fail "PHP 8.2+ kerak. Hozirgi: $(php -v | grep -m1 '' | cut -d' ' -f1-2)"

echo "  PHP:      $(php -v | grep -m1 '' | cut -d' ' -f1-2)"
echo "  Composer: $(composer --version 2>/dev/null | cut -d' ' -f1-3)"

[ -f ".env" ] || fail ".env fayl topilmadi!"

APP_ENV_VAL=$(grep "^APP_ENV=" .env | cut -d'=' -f2 || echo "")
APP_DEBUG_VAL=$(grep "^APP_DEBUG=" .env | cut -d'=' -f2 || echo "")
[ "$APP_ENV_VAL" = "production" ] || warn "APP_ENV=production emas! Hozirgi: $APP_ENV_VAL"
[ "$APP_DEBUG_VAL" = "false" ]    || warn "APP_DEBUG=false emas — xatolar saytda ko'rinishi mumkin!"

if command -v node &>/dev/null; then
    echo "  Node: $(node -v)  NPM: $(npm -v)"
    HAS_NODE=true
else
    warn "Node.js topilmadi — public/build allaqachon mavjud bo'lishi kerak"
    [ -d "public/build" ] || fail "public/build yo'q va Node ham yo'q — deploy to'xtatildi"
    HAS_NODE=false
fi

# =============================================================================
# 2. Maintenance mode
# =============================================================================
step "Maintenance mode yoqilmoqda..."
php artisan down --retry=60 2>/dev/null || warn "down ishlamadi, davom etilmoqda..."

# =============================================================================
# 3. Storage papkalari (mkdir -p — tez va idempotent, har safar xavfsiz)
# =============================================================================
step "Storage papkalari tekshirilmoqda..."
mkdir -p \
    storage/framework/{cache/data,sessions,views,testing} \
    storage/{logs,app/public,app/private/livewire-tmp,backups} \
    bootstrap/cache

# =============================================================================
# 4. Fayl ruxsatlari (faqat --fix-perms bilan yoki yangi papka paydo bo'lganda)
# =============================================================================
if [ "$FIX_PERMS" = true ]; then
    step "Ruxsatlar to'g'irlanmoqda (--fix-perms)..."
    find storage bootstrap/cache -type d -exec chmod 775 {} \;
    find storage bootstrap/cache -type f -exec chmod 664 {} \;
    WEB_USER="www-data"
    id "$WEB_USER" &>/dev/null || { WEB_USER=$(whoami); warn "www-data yo'q — $WEB_USER ishlatilmoqda"; }
    chown -R "$WEB_USER:$WEB_USER" storage bootstrap/cache 2>/dev/null || true
    echo "  Ruxsatlar yangilandi"
else
    echo "  O'tkazib yuborildi (--fix-perms flag yo'q)"
fi

# =============================================================================
# 5. Composer
# =============================================================================
step "Composer (--no-dev)..."
composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# =============================================================================
# 6. NPM build
# =============================================================================
if [ "$HAS_NODE" = true ]; then
    step "NPM build..."
    npm ci --prefer-offline
    npm run build
fi

# =============================================================================
# 7. Cache tozalash
# =============================================================================
step "Cache tozalanmoqda..."
php artisan optimize:clear

# =============================================================================
# 8. Database migrate
# =============================================================================
step "Database migrate..."

# Backup (mysqldump mavjud bo'lsa)
if command -v mysqldump &>/dev/null; then
    DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2 || echo "")
    DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2 || echo "")
    DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2 || echo "")
    if [ -n "$DB_DATABASE" ]; then
        BACKUP_FILE="storage/backups/db_$(date +%Y%m%d_%H%M%S).sql"
        mysqldump -u"$DB_USERNAME" ${DB_PASSWORD:+-p"$DB_PASSWORD"} \
            "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null \
            && echo "  Backup: $BACKUP_FILE" \
            || warn "Backup olib bo'lmadi — migrate davom etadi"
    fi
fi

php artisan migrate --force

# =============================================================================
# 9. Storage link
# =============================================================================
step "Storage link tekshirilmoqda..."
if [ ! -L "public/storage" ]; then
    php artisan storage:link
elif [ ! -e "public/storage" ]; then
    rm "public/storage"
    php artisan storage:link
    echo "  Broken link tuzatildi"
else
    echo "  Mavjud va ishlayapti"
fi

# =============================================================================
# 10. Production cache qurish
# =============================================================================
step "Production cache qurilmoqda..."
php artisan optimize          # config + route + view + event cache
php artisan filament:upgrade
php artisan icons:cache 2>/dev/null || true

# =============================================================================
# 11. Queue + Sitemap
# =============================================================================
step "Queue restart..."
php artisan queue:restart

step "Sitemap generatsiya..."
php artisan sitemap:generate 2>/dev/null || warn "sitemap:generate ishlamadi"

# =============================================================================
# 12. Maintenance mode o'chirish
# =============================================================================
step "Maintenance mode o'chirilmoqda..."
php artisan up

# =============================================================================
DEPLOY_END=$(date +%s)
ELAPSED=$((DEPLOY_END - DEPLOY_START))
echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  Deploy muvaffaqiyatli! (${ELAPSED}s)${NC}"
echo -e "${GREEN}============================================${NC}"
echo "  $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
