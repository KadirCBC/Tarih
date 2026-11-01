<?php

namespace App\Console\Commands;

use App\Services\SitemapGeneratorService;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Tüm nümizmatik içeriği ve programatik SEO sayfalarını sitemap.xml olarak üretir.';

    public function handle(SitemapGeneratorService $service): int
    {
        $this->info('Dinamik nümizmatik sitemap.xml üretiliyor...');
        $path = $service->generate();
        $this->info("Sitemap başarıyla oluşturuldu: {$path}");

        return self::SUCCESS;
    }
}

