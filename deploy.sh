#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Laravel production deploy script
# Ishlatish: bash deploy.sh
# =============================================================================

set -e  # Biror command xato bo'lsa to'xta

# --- Ranglar ---
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

step() { echo -e "\n${GREEN}==>${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[x]${NC} $1"; exit 1; }

# =============================================================================
# 0. PHP va Node versiyalarini tekshirish
# =============================================================================
step "Muhit tekshirilmoqda..."

php -r "version_compare(PHP_VERSION, '8.2.0', '<') && exit(1);" \
    || fail "PHP 8.2+ kerak. Hozirgi: $(php -v | head -1)"

echo "  PHP:      $(php -v | head -1 | cut -d' ' -f1-2)"
echo "  Composer: $(composer --version 2>/dev/null | cut -d' ' -f1-3)"

if command -v node &>/dev/null; then
    echo "  Node:     $(node -v)"
    echo "  NPM:      $(npm -v)"
    HAS_NODE=true
else
    warn "Node.js topilmadi — npm build o'tkazib yuboriladi (dist papkasi allaqachon upload bo'lgan deb hisoblanadi)"
    HAS_NODE=false
fi

# =============================================================================
# 1. Maintenance mode yoqish
# =============================================================================
step "Maintenance mode yoqilmoqda..."
php artisan down --retry=60 2>/dev/null || warn "Maintenance mode ishlamadi, davom etilmoqda..."

# =============================================================================
# 2. Composer — faqat install (update EMAS!)
# =============================================================================
# MUHIM: Serverda hech qachon `composer update` ishlatilmaydi!
# `composer update` — local dev muhiti uchun (lock file yangilanadi)
# `composer install` — server uchun (lock file'dagi aniq versiyalar o'rnatiladi)
step "Composer paketlari o'rnatilmoqda (--no-dev)..."
composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# =============================================================================
# 3. NPM build (Node.js mavjud bo'lsa)
# =============================================================================
if [ "$HAS_NODE" = true ]; then
    step "NPM paketlari o'rnatilmoqda..."
    npm ci --prefer-offline

    step "Vite production build..."
    npm run build
else
    warn "NPM build o'tkazib yuborildi — public/build papkasi allaqachon mavjud bo'lishi kerak"
fi

# =============================================================================
# 4. .env fayl mavjudligini tekshirish
# =============================================================================
step ".env fayl tekshirilmoqda..."
[ -f ".env" ] || fail ".env fayl topilmadi! .env.example dan nusxa ko'chiring va to'ldiring"

APP_ENV_VAL=$(grep "^APP_ENV=" .env | cut -d'=' -f2)
APP_DEBUG_VAL=$(grep "^APP_DEBUG=" .env | cut -d'=' -f2)
[ "$APP_ENV_VAL" = "production" ] || warn ".env da APP_ENV=production emas! Hozirgi: $APP_ENV_VAL"
[ "$APP_DEBUG_VAL" = "false" ]    || warn ".env da APP_DEBUG=false emas! Hozirgi: $APP_DEBUG_VAL — xatolar saytda ko'rinishi mumkin!"

# =============================================================================
# 5. Ma'lumotlar bazasi migratsiyasi
# =============================================================================
step "Pending migratsiyalar tekshirilmoqda..."
PENDING=$(php artisan migrate:status --pending 2>/dev/null | grep -c "Pending" || true)

if [ "$PENDING" -gt 0 ] 2>/dev/null || php artisan migrate:status 2>/dev/null | grep -q "Pending"; then
    # MySQL bo'lsa avtomatik backup
    if command -v mysqldump &>/dev/null && [ -f ".env" ]; then
        DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d'=' -f2)
        DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)
        DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2)

        if [ -n "$DB_DATABASE" ]; then
            BACKUP_FILE="storage/backups/db_$(date +%Y%m%d_%H%M%S).sql"
            mkdir -p storage/backups
            step "DB backup olinmoqda: $BACKUP_FILE"
            mysqldump -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null \
                && echo "  Backup muvaffaqiyatli: $BACKUP_FILE" \
                || warn "Backup olib bo'lmadi — migration baribir davom etadi"
        fi
    fi

    step "Migratsiya ishga tushirilmoqda..."
    php artisan migrate --force
else
    echo "  Yangi migratsiya yo'q — o'tkazib yuborildi"
fi

# =============================================================================
# 6. Storage symbolic link
# =============================================================================
step "Storage link tekshirilmoqda..."
if [ ! -L "public/storage" ]; then
    php artisan storage:link
    echo "  Storage link yaratildi"
else
    echo "  Storage link allaqachon mavjud"
fi

# =============================================================================
# 7. Cache tozalash va qayta qurish
# =============================================================================
step "Cache tozalanmoqda..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

step "Cache qurilmoqda (production optimization)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# =============================================================================
# 8. Filament (admin panel)
# =============================================================================
step "Filament upgradelanmoqda..."
php artisan filament:upgrade

# Filament icon cache
php artisan icons:cache 2>/dev/null || warn "icons:cache ishlamadi (katta xato emas)"

# =============================================================================
# 9. Permissions (spatie)
# =============================================================================
step "Permission cache yangilanmoqda..."
php artisan permission:cache-reset 2>/dev/null || warn "permission:cache-reset ishlamadi"

# =============================================================================
# 10. Rasm cache tozalash (ixtiyoriy)
# =============================================================================
step "Rasm cache tozalanmoqda..."
php artisan images:clear-cache --force 2>/dev/null || warn "images:clear-cache ishlamadi"

# =============================================================================
# 11. Fayl ruxsatlarini to'g'rilash
# =============================================================================
step "Storage papkalari yaratilmoqda..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache

step "Fayl ruxsatlari (permissions) to'g'irlanmoqda..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null \
    || chown -R $(whoami):$(whoami) storage bootstrap/cache

# =============================================================================
# 12. Queue worker qayta ishga tushirish
# =============================================================================
step "Queue worker qayta ishga tushirilmoqda..."
php artisan queue:restart
echo "  Queue restart signali yuborildi"

# =============================================================================
# 13. Sitemap yangilash (ixtiyoriy)
# =============================================================================
step "Sitemap yangilanmoqda..."
php artisan sitemap:generate 2>/dev/null || warn "sitemap:generate ishlamadi"

# =============================================================================
# 14. Maintenance mode o'chirish
# =============================================================================
step "Maintenance mode o'chirilmoqda..."
php artisan up

# =============================================================================
# Tugadi
# =============================================================================
echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  Deploy muvaffaqiyatli yakunlandi!${NC}"
echo -e "${GREEN}============================================${NC}"
echo "  Vaqt: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
