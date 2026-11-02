@echo off
setlocal EnableDelayedExpansion
title Antik Numizmatik Ansiklopedisi - localhost:5000
color 0E

echo ===============================================================================
echo            ANTIK NUMIZMATIK VE SIKKE MUZE KATALOGU
echo ===============================================================================
echo.

REM 1. Dizin Ayari
cd /d "%~dp0"

REM 2. PHP Yolu Belirleme
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

echo [OK] PHP Bulundu: %PHP_CMD%

REM 3. .env Kontrolu
if not exist ".env" (
    echo [!] .env dosyasi bulunamadi, .env.example kopyalaniyor...
    copy ".env.example" ".env" >nul
)

REM 4. Klasor ve Veritabani Kontrolleri
if not exist "bootstrap\cache" mkdir "bootstrap\cache"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\logs" mkdir "storage\logs"

if not exist "database\database.sqlite" (
    echo [!] Yerel SQLite veritabani olusturuluyor...
    type nul > "database\database.sqlite"
    echo [!] Tablolar ve ornek veriler yukleniyor...
    "%PHP_CMD%" artisan migrate --force >nul 2>&1
    "%PHP_CMD%" artisan db:seed --force >nul 2>&1
)

REM 5. Onbellek Temizleme
echo [OK] Sablon onbellegi temizleniyor...
"%PHP_CMD%" artisan view:clear >nul 2>&1

REM 6. Tarayiciyi Ac
echo [OK] Web tarayicisi aciliyor: http://localhost:5000
start "" "http://localhost:5000"

echo.
echo ===============================================================================
echo    Web Sitesi Aktif: http://localhost:5000
echo    Kapatmak icin bu pencereyi kapatabilir veya Ctrl + C yapabilirsiniz.
echo ===============================================================================
echo.

REM 7. Sunucuyu Baslat
"%PHP_CMD%" artisan serve --host=127.0.0.1 --port=5000

if errorlevel 1 (
    echo.
    echo [HATA] Sunucu baslatilamadi veya beklenmedik sekilde kapandi.
    pause
)

