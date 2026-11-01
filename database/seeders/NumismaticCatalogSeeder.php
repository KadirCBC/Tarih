<?php

namespace Database\Seeders;

use App\Enums\CoinMetal;
use App\Enums\ConditionGrade;
use App\Enums\ImageType;
use App\Enums\RarityGrade;
use App\Models\Coin;
use App\Models\Mint;
use App\Models\Period;
use App\Models\Ruler;
use Illuminate\Database\Seeder;

class NumismaticCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dönemler
        $greek = Period::create([
            'name' => 'Klasik & Helenistik Yunan',
            'start_year' => -500,
            'end_year' => -31,
            'description' => 'Antik Yunan şehir devletleri ve Büyük İskender imparatorluğu dönemi darpları.',
        ]);

        $romanEmpire = Period::create([
            'name' => 'Roma İmparatorluğu (Principatus)',
            'start_year' => -27,
            'end_year' => 284,
            'description' => 'Augustus ile başlayan ve III. yüzyıl krizine kadar süren altın Roma nümizmatiği çağı.',
        ]);

        $byzantine = Period::create([
            'name' => 'Doğu Roma (Bizans İmparatorluğu)',
            'start_year' => 491,
            'end_year' => 1453,
            'description' => 'I. Anastasius sikke reformundan İstanbul fethine kadar uzanan Hristiyan nümizmatiği.',
        ]);

        // 2. Darphaneler
        $romeMint = Mint::create([
            'name' => 'Roma',
            'location' => 'İtalya',
            'latitude' => 41.890251,
            'longitude' => 12.492373,
        ]);

        $athensMint = Mint::create([
            'name' => 'Atina',
            'location' => 'Yunanistan',
            'latitude' => 37.971532,
            'longitude' => 23.725749,
        ]);

        $constantinopleMint = Mint::create([
            'name' => 'Konstantinopolis',
            'location' => 'İstanbul / Türkiye',
            'latitude' => 41.008238,
            'longitude' => 28.978359,
        ]);

        // 3. Hükümdarlar
        $augustus = Ruler::create([
            'period_id' => $romanEmpire->id,
            'name' => 'Augustus',
            'title' => 'İlk Roma İmparatoru (Augustus Caesar)',
            'reign_start' => -27,
            'reign_end' => 14,
            'biography' => 'Roma Cumhuriyeti’ni imparatorluğa dönüştüren, Pax Romana çağını başlatan ilk imparator.',
        ]);

        $alexander = Ruler::create([
            'period_id' => $greek->id,
            'name' => 'Büyük İskender (III. Aleksandros)',
            'title' => 'Makedonya Kralı & Asya Fatihi',
            'reign_start' => -336,
            'reign_end' => -323,
            'biography' => 'Dünya tarihinin en büyük fatihlerinden; Herakles ve Zeus tasvirli tetradrahmileri yüzyıllarca basılmıştır.',
        ]);

        $justinian = Ruler::create([
            'period_id' => $byzantine->id,
            'name' => 'I. Justinianus (Büyük Justinianos)',
            'title' => 'Doğu Roma İmparatoru',
            'reign_start' => 527,
            'reign_end' => 565,
            'biography' => 'Ayasofya’yı inşa ettiren, Roma Hukuku’nu (Corpus Juris Civilis) derleten büyük imparator.',
        ]);

        // 4. Örnek Sikkeler
        // 1. Sikke: Augustus Denarius
        $coin1 = Coin::create([
            'period_id' => $romanEmpire->id,
            'ruler_id' => $augustus->id,
            'mint_id' => $romeMint->id,
            'title' => 'Augustus Gümüş Denarius - Gaius ve Lucius Sezarlar',
            'catalog_reference' => 'RIC I 207; RSC 43',
            'denomination' => 'Denarius',
            'metal' => CoinMetal::SILVER,
            'weight_g' => 3.85,
            'diameter_mm' => 19.5,
            'obverse_desc' => 'Defne çelenkli Augustus büstü sağa bakar.',
            'obverse_legend' => 'CAESAR AVGVSTVS DIVI F PATER PATRIAE',
            'obverse_legend_trans' => 'Tanrısal [Sezar’ın] Oğlu, Vatanın Babası Sezar Augustus.',
            'reverse_desc' => 'Gaius ve Lucius Sezarlar togeli ve mızraklarla durmakta; ortada lituus ve simpulum.',
            'reverse_legend' => 'C L CAESARES AVGVSTI F COS DESIG PRINC IVVENT',
            'reverse_legend_trans' => 'Gençliğin Önderleri, Konsül Adayları, Augustus’un Oğulları Gaius ve Lucius.',
            'rarity_grade' => RarityGrade::C,
            'historical_notes' => 'Augustus’un erken yaşta ölen iki torunu ve varisleri anısına darbedilmiş ikonik sikke tipi.',
            'view_count' => 1420,
        ]);

        $coin1->valuations()->createMany([
            ['condition_grade' => ConditionGrade::FINE, 'min_value_usd' => 120.00, 'max_value_usd' => 200.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::VF, 'min_value_usd' => 300.00, 'max_value_usd' => 550.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::XF, 'min_value_usd' => 750.00, 'max_value_usd' => 1400.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::UNC, 'min_value_usd' => 2500.00, 'max_value_usd' => 4500.00, 'last_updated_at' => now()],
        ]);

        $coin1->images()->create([
            'image_type' => ImageType::BOTH,
            'image_path' => 'coins/augustus_denarius_c_l.webp',
            'is_primary' => true,
        ]);

        // 2. Sikke: İskender Tetradrahmi
        $coin2 = Coin::create([
            'period_id' => $greek->id,
            'ruler_id' => $alexander->id,
            'mint_id' => $athensMint->id,
            'title' => 'Büyük İskender Gümüş Tetradrahmi - Aslan Postlu Herakles & Tahtta Zeus',
            'catalog_reference' => 'Price 3599; Sear 6713',
            'denomination' => 'Tetradrahmi',
            'metal' => CoinMetal::SILVER,
            'weight_g' => 17.15,
            'diameter_mm' => 26.5,
            'obverse_desc' => 'Aslan postu başlığı giymiş genç Herakles başı sağa bakar.',
            'obverse_legend' => null,
            'obverse_legend_trans' => 'Yazısız ön yüz; tanrısal kuvvet ve kahramanlık sembolü.',
            'reverse_desc' => 'Tahtında oturan Zeus Aetophoros sola doğru; sol elinde asa, sağ elinde kartal tutar.',
            'reverse_legend' => 'ΑΛΕΞΑΝΔΡΟΥ',
            'reverse_legend_trans' => 'Aleksandrou (İskender’in)',
            'rarity_grade' => RarityGrade::R1,
            'historical_notes' => 'Antik dünyanın en çok işlem gören evrensel ticaret para birimi.',
            'view_count' => 3890,
        ]);

        $coin2->valuations()->createMany([
            ['condition_grade' => ConditionGrade::VF, 'min_value_usd' => 450.00, 'max_value_usd' => 800.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::XF, 'min_value_usd' => 1200.00, 'max_value_usd' => 2400.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::UNC, 'min_value_usd' => 3500.00, 'max_value_usd' => 7000.00, 'last_updated_at' => now()],
        ]);

        $coin2->images()->create([
            'image_type' => ImageType::BOTH,
            'image_path' => 'coins/alexander_tetradrachm_zeus.webp',
            'is_primary' => true,
        ]);
    }
}

