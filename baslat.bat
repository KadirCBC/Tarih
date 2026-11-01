@echo off
chcp 65001 >nul
title Antik Nümizmatik Ansiklopedisi - Localhost:5000
color 0E

echo ===============================================================================
echo            🏛️  ANTİK NÜMİZMATİK VE SİKKE MÜZE KATALOĞU  🏛️
echo ===============================================================================
echo.

:: 1. Dizin Ayarı
cd /d "%~dp0"

:: 2. PHP Yolu Belirleme (Öncelik XAMPP, ardından Sistem PATH)
set "PHP_CMD="
if exist "C:\xampp\php\php.exe" (
    set "PHP_CMD=C:\xampp\php\php.exe"
) else (
    where php >nul 2>&1
    if not errorlevel 1 (
        set "PHP_CMD=php"
    )
)

if "%PHP_CMD%"=="" (
    echo [HATA] PHP bulunamadi!
    echo Lutfen C:\xampp\php\php.exe kurulumunu veya sistem PATH ortam degiskenini kontrol edin.
    echo.
    pause
    exit /b 1
)

echo [✓] PHP Bulundu: %PHP_CMD%

:: 3. .env Kontrolü
if not exist ".env" (
    echo [!] .env dosyasi bulunamadi, .env.example'dan olusturuluyor...
    copy .env.example .env >nul
)

:: 4. Veritabanı ve Klasör Kontrolleri
if not exist "database\database.sqlite" (
    echo [!] Yerel SQLite veritabani olusturuluyor...
    type nul > "database\database.sqlite"
    echo [!] Tablolar ve nümizmatik ornek veriler yukleniyor...
    "%PHP_CMD%" artisan migrate --force >nul 2>&1
    "%PHP_CMD%" artisan db:seed --force >nul 2>&1
)

if not exist "bootstrap\cache" mkdir "bootstrap\cache"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\logs" mkdir "storage\logs"

:: 5. Tarayıcıyı Aç
echo [✓] Web tarayicisi aciliyor: http://localhost:5000
timeout /t 2 /nobreak >nul
start http://localhost:5000

echo.
echo ===============================================================================
echo   🚀 Web Sitesi Aktif: http://localhost:5000
echo   Kapatmak icin bu pencereyi kapatabilir veya Ctrl + C tuslarina basabilirsiniz.
echo ===============================================================================
echo.

:: 6. Web Sunucusunu Başlat (localhost:5000)
"%PHP_CMD%" artisan serve --host=127.0.0.1 --port=5000

pause
