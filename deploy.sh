#!/usr/bin/env bash
set -euo pipefail

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; NC='\033[0m'
step() { echo -e "\n${GREEN}==>${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
fail() { echo -e "${RED}[x]${NC} $1"; exit 1; }

DEPLOY_START=$(date +%s)

# =============================================================================
# 1. Muhit
# =============================================================================
step "Muhit tekshirilmoqda..."
php -r "version_compare(PHP_VERSION, '8.2.0', '<') && exit(1);" \
    || fail "PHP 8.2+ kerak"
[ -f ".env" ] || fail ".env fayl topilmadi!"
echo "  PHP: $(php -v | grep -m1 '' | cut -d' ' -f1-2)"

# =============================================================================
# 2. Maintenance mode
# =============================================================================
step "Maintenance mode..."
php artisan down --retry=60 2>/dev/null || warn "down ishlamadi, davom etilmoqda..."

# =============================================================================
# 3. Storage papkalari
# =============================================================================
mkdir -p \
    storage/framework/{cache/data,sessions,views} \
    storage/{logs,app/public,app/private/livewire-tmp} \
    bootstrap/cache

# =============================================================================
# 4. Composer
# =============================================================================
step "Composer..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# =============================================================================
# 5. NPM build
# =============================================================================
step "NPM build..."
npm ci --prefer-offline
npm run build

# =============================================================================
# 6. Cache tozalash
# =============================================================================
step "Cache tozalanmoqda..."
php artisan optimize:clear

# =============================================================================
# 6. Migrate
# =============================================================================
step "Migrate..."
php artisan migrate --force

# =============================================================================
# 7. Storage link
# =============================================================================
step "Storage link..."
if [ ! -L "public/storage" ] || [ ! -e "public/storage" ]; then
    rm -f "public/storage"
    php artisan storage:link
else
    echo "  Mavjud"
fi

# =============================================================================
# 8. Cache qurish
# =============================================================================
step "Cache qurilmoqda..."
php artisan optimize
php artisan filament:upgrade
php artisan icons:cache 2>/dev/null || true

# =============================================================================
# 9. Queue + Sitemap
# =============================================================================
step "Queue restart..."
php artisan queue:restart

php artisan sitemap:generate 2>/dev/null || warn "sitemap:generate ishlamadi"

# =============================================================================
# 10. Maintenance mode off
# =============================================================================
step "Sayt yoqilmoqda..."
php artisan up

ELAPSED=$(( $(date +%s) - DEPLOY_START ))
echo -e "\n${GREEN}Deploy muvaffaqiyatli! (${ELAPSED}s)${NC}\n"
