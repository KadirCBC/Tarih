<?php

use Illuminate\Http\Request;

/**
 * --------------------------------------------------------------------------
 * cPanel / Shared Hosting İçin Ayrıştırılmış index.php
 * --------------------------------------------------------------------------
 * Bu dosya /home/kullanici/public_html/ dizini altında yer almalıdır.
 * Laravel çekirdek dosyaları ise /home/kullanici/laravel_app/ içine taşınır.
 */

define('LARAVEL_START', microtime(true));

// Laravel uygulamamızın ana dizini (public_html'in bir üstündeki laravel_app)
$appPath = __DIR__ . '/../laravel_app';

// 1. Bakım Modu Kontrolü
if (file_exists($maintenance = $appPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// 2. Composer Autoloader
require $appPath . '/vendor/autoload.php';

// 3. Laravel Uygulamasını Başlatma (Bootstrap)
$app = require_once $appPath . '/bootstrap/app.php';

// 4. Public Path Yapılandırması:
// Laravel'in public_path() yardımcı fonksiyonunun public_html'i işaret etmesini sağlar.
if (method_exists($app, 'usePublicPath')) {
    $app->usePublicPath(__DIR__);
}

// 5. İsteği Yakala ve Yanıtla (Laravel 11+ uyumlu)
$app->handleRequest(Request::capture());

