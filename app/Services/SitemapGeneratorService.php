<?php

namespace App\Services;

use App\Models\Coin;
use App\Models\CoinStory;
use App\Models\Mint;
use App\Models\Period;
use App\Models\Ruler;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapGeneratorService
{
    /**
     * Tüm nümizmatik içeriği ve programatik SEO sayfalarını sitemap.xml'e yazar.
     */
    public function generate(): string
    {
        $sitemap = Sitemap::create();

        // 1. Ana Sayfa ve Statik Listeler
        $sitemap->add(Url::create(route('home'))->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create(route('coins.index'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create(route('periods.index'))->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        $sitemap->add(Url::create(route('rulers.index'))->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        $sitemap->add(Url::create(route('stories.index'))->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        // 2. Sikke Detay Sayfaları (En yüksek öncelikli organik sayfalar)
        Coin::select(['id', 'slug', 'updated_at'])->chunk(500, function ($coins) use ($sitemap) {
            foreach ($coins as $coin) {
                $sitemap->add(
                    Url::create(route('coins.show', $coin->slug))
                        ->setLastModificationDate($coin->updated_at ?? now())
                        ->setPriority(0.9)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
            }
        });

        // 3. Haftanın Sikkesi & Editoryal Tarihsel Hikayeler (Google Discover)
        CoinStory::published()->select(['id', 'slug', 'published_at', 'updated_at'])->chunk(100, function ($stories) use ($sitemap) {
            foreach ($stories as $story) {
                $sitemap->add(
                    Url::create(route('stories.show', $story->slug))
                        ->setLastModificationDate($story->updated_at ?? $story->published_at ?? now())
                        ->setPriority(0.9)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
            }
        });

        // 4. Dönemler
        foreach (Period::all() as $period) {
            $sitemap->add(
                Url::create(route('periods.show', $period->slug))
                    ->setLastModificationDate($period->updated_at ?? now())
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        }

        // 5. Hükümdarlar
        foreach (Ruler::all() as $ruler) {
            $sitemap->add(
                Url::create(route('rulers.show', $ruler->slug))
                    ->setLastModificationDate($ruler->updated_at ?? now())
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            );
        }

        // 6. Programatik SEO Kombinasyon Sayfaları
        $popularMetals = ['altin', 'gumus', 'bronz'];
        foreach (Period::all() as $p) {
            foreach ($popularMetals as $m) {
                $pSeoUrl = url("/{$p->slug}-{$m}-sikkeleri");
                $sitemap->add(Url::create($pSeoUrl)->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }
        }

        foreach (Ruler::limit(30)->get() as $r) {
            foreach (['altin', 'gumus'] as $m) {
                $rSeoUrl = url("/{$r->slug}-{$m}-paralari");
                $sitemap->add(Url::create($rSeoUrl)->setPriority(0.7)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
            }
        }

        $filePath = public_path('sitemap.xml');
        $sitemap->writeToFile($filePath);

        return $filePath;
    }
}

