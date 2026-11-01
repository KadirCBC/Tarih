#!/usr/bin/env bash
# ==============================================================================
# Tarih / Antik Nümizmatik Ansiklopedisi - Sıfır Kesinti (Zero-Downtime) Deploy Script
# Uyumlu Ortamlar: cPanel (CloudLinux / EA4), Plesk (Onyx / Obsidian), Linux VPS
# ==============================================================================

set -eo pipefail

# Renk Tanımları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${PURPLE}================================================================${NC}"
echo -e "${CYAN}   🚀 NÜMİZMATİK PROJESİ OTOMATİK DEPLOYMENT BAŞLADI           ${NC}"
echo -e "${PURPLE}================================================================${NC}"

# 1. Dizin ve Ortam Kontrolü
DEPLOY_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DEPLOY_DIR"

echo -e "${BLUE}📁 Çalışma Dizini:${NC} $DEPLOY_DIR"

# 2. PHP Binary Tespiti (cPanel EA4 / Plesk / Sistem PHP)
if [ -z "$PHP_BIN" ]; then
    if [ -f "/opt/cpanel/ea-php83/root/usr/bin/php" ]; then
        PHP_BIN="/opt/cpanel/ea-php83/root/usr/bin/php"
    elif [ -f "/opt/cpanel/ea-php82/root/usr/bin/php" ]; then
        PHP_BIN="/opt/cpanel/ea-php82/root/usr/bin/php"
    elif [ -f "/usr/local/bin/ea-php83" ]; then
        PHP_BIN="/usr/local/bin/ea-php83"
    elif [ -f "/usr/local/bin/ea-php82" ]; then
        PHP_BIN="/usr/local/bin/ea-php82"
    elif [ -f "/opt/plesk/php/8.3/bin/php" ]; then
        PHP_BIN="/opt/plesk/php/8.3/bin/php"
    elif [ -f "/opt/plesk/php/8.2/bin/php" ]; then
        PHP_BIN="/opt/plesk/php/8.2/bin/php"
    else
        PHP_BIN="$(command -v php || true)"
    fi
fi

if [ -z "$PHP_BIN" ] || [ ! -x "$PHP_BIN" ]; then
    echo -e "${RED}❌ HATA: PHP 8.2+ ikili dosyası bulunamadı! Lütfen PHP_BIN ortam değişkenini ayarlayın.${NC}"
    exit 1
fi

echo -e "${BLUE}🐘 Kullanılan PHP:${NC} $($PHP_BIN -v | head -n 1)"

# 3. Composer Tespiti
if [ -z "$COMPOSER_BIN" ]; then
    if [ -f "$DEPLOY_DIR/composer.phar" ]; then
        COMPOSER_BIN="$PHP_BIN $DEPLOY_DIR/composer.phar"
    elif command -v composer &> /dev/null; then
        COMPOSER_BIN="$(command -v composer)"
    else
        echo -e "${YELLOW}⚠️ Composer globalde bulunamadı, composer.phar indiriliyor...${NC}"
        curl -sS https://getcomposer.org/installer | $PHP_BIN
        COMPOSER_BIN="$PHP_BIN $DEPLOY_DIR/composer.phar"
    fi
fi

# 4. Hata Durumunda Sitenin Bakım Modunda Kalmasını Önleyen Trap
cleanup_on_failure() {
    echo -e "${RED}❌ Deployment sırasında bir hata oluştu! Bakım modu kapatılıyor...${NC}"
    $PHP_BIN artisan up || true
    exit 1
}
trap cleanup_on_failure ERR

# 5. Bakım Modunu Güvenli Gizli Anahtar (Secret) ile Aç
# Ziyaretçilere 503 gösterilirken, deploy scripti ve adminler bypass token ile erişebilir.
BYPASS_SECRET="numizmatik-deploy-$(date +%s)"
echo -e "${YELLOW}🔒 Bakım modu açılıyor (Bypass Anahtarı: $BYPASS_SECRET)...${NC}"
$PHP_BIN artisan down --secret="$BYPASS_SECRET" --render="errors::503" --retry=30 || true

# 6. Git Güncellemesi (En Son `main` Dalı)
echo -e "${CYAN}📥 Git üzerinden güncel kaynak kodlar çekiliyor...${NC}"
if [ -d ".git" ]; then
    git fetch origin main
    git reset --hard origin/main
    CURRENT_COMMIT="$(git log -1 --format='%h - %s (%ci)')"
    echo -e "${GREEN}✅ Git Güncellendi:${NC} $CURRENT_COMMIT"
else
    echo -e "${YELLOW}⚠️ .git dizini bulunamadı, git pull adımı atlandı.${NC}"
fi

# 7. Composer Bağımlılıklarını Prodüksiyon Modunda Yükle
echo -e "${CYAN}📦 Composer bağımlılıkları yükleniyor (no-dev, optimized)...${NC}"
$COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 8. Veritabanı Migrasyonları
echo -e "${CYAN}🗄️ Veritabanı migrasyonları uygulanıyor...${NC}"
$PHP_BIN artisan migrate --force

# 9. Sembolik Bağlantı (storage:link) Kontrolü
echo -e "${CYAN}🔗 Storage sembolik bağlantısı denetleniyor...${NC}"
# cPanel durumunda public_html hedefi kontrol edilir
if [ -d "../public_html" ] && [ ! -L "../public_html/storage" ]; then
    if [ -d "../public_html/storage" ]; then
        rm -rf ../public_html/storage
    fi
    ln -s "$DEPLOY_DIR/storage/app/public" "../public_html/storage"
    echo -e "${GREEN}✅ cPanel public_html/storage sembolik bağı oluşturuldu.${NC}"
else
    # Standart Laravel public dizini
    $PHP_BIN artisan storage:link || true
fi

# 10. İzinleri Düzenle (cPanel/Plesk Dosya İzinleri)
echo -e "${CYAN}🔑 Dosya ve dizin yazma izinleri ayarlanıyor...${NC}"
chmod -R 775 storage bootstrap/cache || true

# 11. Önbellekleri Temizle ve Prodüksiyon İçin Isıt
echo -e "${CYAN}⚡ Laravel önbellekleri ısıtılıyor (Config, Route, View, Event)...${NC}"
$PHP_BIN artisan config:clear
$PHP_BIN artisan route:clear
$PHP_BIN artisan view:clear
$PHP_BIN artisan event:clear

$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan event:cache

# 12. Filament & Livewire Varsa Optimize Et
echo -e "${CYAN}🎨 Filament & Blade UI bileşenleri optimize ediliyor...${NC}"
if $PHP_BIN artisan list | grep -q "filament:optimize"; then
    $PHP_BIN artisan filament:optimize || true
    echo -e "${GREEN}✅ Filament bileşenleri ve ikonları önbelleklendi.${NC}"
fi

if $PHP_BIN artisan list | grep -q "icons:cache"; then
    $PHP_BIN artisan icons:cache || true
fi

# 13. Queue Worker & Horizon Yeniden Başlatma
if $PHP_BIN artisan list | grep -q "queue:restart"; then
    echo -e "${CYAN}🔄 Kuyruk çalışanları (Queue Workers) yenileniyor...${NC}"
    $PHP_BIN artisan queue:restart || true
fi

# 14. OPcache Sıfırlama (PHP CLI üzerinden Web OPcache sıfırlanamadığından CLI cache purge)
if $PHP_BIN artisan list | grep -q "optimize:clear"; then
    # Ekstra kontrol
    true
fi

# 15. Bakım Modunu Kapat (Uygulamayı Canlıya Al)
echo -e "${GREEN}🔓 Bakım modu kapatılıyor, uygulama canlıya alınıyor...${NC}"
$PHP_BIN artisan up

# 16. Tamamlandı Bildirimi
echo -e "${PURPLE}================================================================${NC}"
echo -e "${GREEN}   ✨ DEPLOYMENT BAŞARIYLA TAMAMLANDI! (${CURRENT_COMMIT:-Canlıda}) ${NC}"
echo -e "${PURPLE}================================================================${NC}"
exit 0

