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

class NumismaticDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ------------------------------------------------------------------
        // 1. DÖNEMLER (PERIODS)
        // ------------------------------------------------------------------
        $greek = Period::firstOrCreate(
            ['slug' => 'antik-grek'],
            [
                'name' => 'Antik Grek',
                'start_year' => -700,
                'end_year' => -31,
                'description' => 'Arkaik, Klasik ve Helenistik çağları kapsayan, elektron, altın ve gümüş darplarıyla dünya nümizmatiğinin doğuş dönemi.',
            ]
        );

        $roman = Period::firstOrCreate(
            ['slug' => 'roma-imparatorlugu'],
            [
                'name' => 'Roma İmparatorluğu',
                'start_year' => -27,
                'end_year' => 476,
                'description' => 'Augustus ile başlayıp Batı Roma’nın çöküşüne kadar devam eden; Aureus, Denarius ve Sestertius tipi sikke sisteminin hâkim olduğu dönem.',
            ]
        );

        $byzantine = Period::firstOrCreate(
            ['slug' => 'bizans-imparatorlugu'],
            [
                'name' => 'Bizans İmparatorluğu',
                'start_year' => 491,
                'end_year' => 1453,
                'description' => 'I. Anastasius’un M.S. 498 sikke reformundan İstanbul’un fethine kadar devam eden Solidus ve Follis ağırlıklı Doğu Roma nümizmatiği.',
            ]
        );

        $ottoman = Period::firstOrCreate(
            ['slug' => 'osmanli-imparatorlugu'],
            [
                'name' => 'Osmanlı İmparatorluğu',
                'start_year' => 1299,
                'end_year' => 1922,
                'description' => 'Osman Gazi’nin ilk gümüş akçesinden başlayıp Fatih’in ilk altın Sultani sikkesine ve Tanzimat dönemi mecidiye sistemine uzanan İslami nümizmatik çağı.',
            ]
        );

        // ------------------------------------------------------------------
        // 2. DARPHANELER (MINTS)
        // ------------------------------------------------------------------
        $mints = [
            'konstantinopolis' => Mint::firstOrCreate(
                ['slug' => 'konstantinopolis'],
                [
                    'name' => 'Konstantinopolis',
                    'location' => 'İstanbul / Türkiye',
                    'latitude' => 41.008238,
                    'longitude' => 28.978359,
                ]
            ),
            'roma' => Mint::firstOrCreate(
                ['slug' => 'roma'],
                [
                    'name' => 'Roma',
                    'location' => 'Lazio / İtalya',
                    'latitude' => 41.890251,
                    'longitude' => 12.492373,
                ]
            ),
            'efes' => Mint::firstOrCreate(
                ['slug' => 'efes'],
                [
                    'name' => 'Efes (Ephesos)',
                    'location' => 'İzmir / Türkiye',
                    'latitude' => 37.941113,
                    'longitude' => 27.341944,
                ]
            ),
            'siscia' => Mint::firstOrCreate(
                ['slug' => 'siscia'],
                [
                    'name' => 'Siscia',
                    'location' => 'Sisak / Hırvatistan',
                    'latitude' => 45.485121,
                    'longitude' => 16.376110,
                ]
            ),
            'bursa' => Mint::firstOrCreate(
                ['slug' => 'bursa'],
                [
                    'name' => 'Bursa',
                    'location' => 'Bursa / Türkiye',
                    'latitude' => 40.188528,
                    'longitude' => 29.061012,
                ]
            ),
        ];

        // ------------------------------------------------------------------
        // 3. HÜKÜMDARLAR (RULERS)
        // ------------------------------------------------------------------
        $trajan = Ruler::firstOrCreate(
            ['slug' => 'trajan'],
            [
                'period_id' => $roman->id,
                'name' => 'Trajan (Marcus Ulpius Traianus)',
                'title' => 'Optimus Princeps (En İyi İmparator)',
                'reign_start' => 98,
                'reign_end' => 117,
                'biography' => 'Roma İmparatorluğu’nu tarihindeki en geniş sınırlara ulaştıran; Daçya ve Part zaferleriyle anılan Beş İyi İmparator’dan ikincisi.',
            ]
        );

        $hadrian = Ruler::firstOrCreate(
            ['slug' => 'hadrian'],
            [
                'period_id' => $roman->id,
                'name' => 'Hadrian (Publius Aelius Hadrianus)',
                'title' => 'İmparator & Barış Kurucu',
                'reign_start' => 117,
                'reign_end' => 138,
                'biography' => 'Sınırları tahkim eden, Hadrian Duvarı’nı inşa ettiren, Helen kültürüne hayran mimar ve entelektüel imparator.',
            ]
        );

        $constantine = Ruler::firstOrCreate(
            ['slug' => 'konstantin'],
            [
                'period_id' => $byzantine->id,
                'name' => 'I. Konstantin (Büyük Konstantin)',
                'title' => 'Augustus & Konstantinopolis’in Kurucusu',
                'reign_start' => 306,
                'reign_end' => 337,
                'biography' => 'Hristiyanlığı serbest bırakan (Milano Fermanı), başkenti Byzantion’a taşıyıp Konstantinopolis adını veren ve Solidus altın standardını getiren imparator.',
            ]
        );

        $mehmed = Ruler::firstOrCreate(
            ['slug' => 'fatih-sultan-mehmed'],
            [
                'period_id' => $ottoman->id,
                'name' => 'Fatih Sultan Mehmed (II. Mehmed)',
                'title' => 'Ebû\'l-Feth, Kayser-i Rûm, Padişah',
                'reign_start' => 1451,
                'reign_end' => 1481,
                'biography' => 'İstanbul’u fethederek Doğu Roma İmparatorluğu’na son veren, Osmanlı’yı cihan devletine dönüştüren ve ilk Osmanlı altın sikkesini bastıran büyük sultan.',
            ]
        );

        // İskender (Antik Grek için)
        $alexander = Ruler::firstOrCreate(
            ['slug' => 'buyuk-iskender'],
            [
                'period_id' => $greek->id,
                'name' => 'Büyük İskender (III. Aleksandros)',
                'title' => 'Makedonya Kralı & Asya Fatihi',
                'reign_start' => -336,
                'reign_end' => -323,
                'biography' => 'Aristo’nun öğrencisi, Pers İmparatorluğu’nu yıkarak Hindistan’a kadar uzanan Helenistik dönemi başlatan fatih.',
            ]
        );

        // ------------------------------------------------------------------
        // 4. DETAYLI ÖRNEK SİKKE KAYITLARI
        // ------------------------------------------------------------------

        // ==================================================================
        // ÖRNEK 1: Trajan Gümüş Denarius (Arab Adquis)
        // ==================================================================
        $coinTrajan = Coin::updateOrCreate(
            ['slug' => 'trajan-gumus-denarius-arab-adquis'],
            [
                'period_id' => $roman->id,
                'ruler_id' => $trajan->id,
                'mint_id' => $mints['roma']->id,
                'title' => 'Trajan Gümüş Denarius - Arab Adquis (Arabistan Fethi)',
                'catalog_reference' => 'RIC II 142; RSC 26; BMC 499',
                'denomination' => 'Denarius',
                'metal' => CoinMetal::SILVER,
                'weight_g' => 3.25,
                'diameter_mm' => 19.0,
                'obverse_desc' => 'Defne çelenkli, zırhlı ve omzunda paludamentum bulunan Trajan büstü sağa bakar.',
                'obverse_legend' => 'IMP CAES NERVA TRAIAN AVG GERM',
                'obverse_legend_trans' => 'Imperator Caesar Nerva Traianus Augustus Germanicus (Başkomutan Sezar Nerva Traianus Augustus, Cermenlerin Fatihi).',
                'reverse_desc' => 'Ayakta duran kadın figürü (Arabia personifikasyonu) sola bakar; sağ elinde defne dalı, sol elinde kamış tutar; sol alt yanında tek hörgüçlü deve yavrusu durmaktadır.',
                'reverse_legend' => 'ARAB ADQ COS V P P S P Q R OPTIMO PRINC',
                'reverse_legend_trans' => 'Arabia Adquisita, Consul V, Pater Patriae, Senatus Populusque Romanus Optimo Principi (Arabistan Eyaleti Roma’ya Katıldı. 5. Kez Konsül, Vatanın Babası, Senato ve Roma Halkından En Kusursuz Öndere).',
                'rarity_grade' => RarityGrade::R1,
                'historical_notes' => 'Trajan’ın M.S. 106 yılında Nebati (Nabataean) Krallığı’nı fethederek Roma’nın "Arabia Petraea" eyaletini kurması şerefine darbedilmiştir. Sikke üzerindeki deve tasviri, Roma nümizmatiğinde Doğu eyaletlerinin sembolik zafer tasvirlerinin en seçkin örneklerindendir.',
                'view_count' => 2840,
            ]
        );

        $coinTrajan->valuations()->delete();
        $coinTrajan->valuations()->createMany([
            ['condition_grade' => ConditionGrade::POOR, 'min_value_usd' => 60.00, 'max_value_usd' => 95.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::FINE, 'min_value_usd' => 140.00, 'max_value_usd' => 220.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::VF, 'min_value_usd' => 350.00, 'max_value_usd' => 550.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::XF, 'min_value_usd' => 800.00, 'max_value_usd' => 1400.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::UNC, 'min_value_usd' => 2500.00, 'max_value_usd' => 4200.00, 'last_updated_at' => now()],
        ]);

        $coinTrajan->images()->delete();
        $coinTrajan->images()->create([
            'image_type' => ImageType::BOTH,
            'image_path' => 'coins/trajan_denarius_arab_adquis.webp',
            'is_primary' => true,
        ]);

        // ==================================================================
        // ÖRNEK 2: Büyük İskender Gümüş Tetradrahmi (Efes Darbı)
        // ==================================================================
        $coinAlexander = Coin::updateOrCreate(
            ['slug' => 'buyuk-iskender-gumus-tetradrahmi-efes'],
            [
                'period_id' => $greek->id,
                'ruler_id' => $alexander->id,
                'mint_id' => $mints['efes']->id,
                'title' => 'Büyük İskender Gümüş Tetradrahmi - Efes Darbı (Herakles & Zeus)',
                'catalog_reference' => 'Price 1759; Müller 522',
                'denomination' => 'Tetradrahmi',
                'metal' => CoinMetal::SILVER,
                'weight_g' => 17.20,
                'diameter_mm' => 27.5,
                'obverse_desc' => 'Nemea aslanının postunu başlık olarak giymiş genç Herakles başı sağa bakar.',
                'obverse_legend' => null,
                'obverse_legend_trans' => 'Yazısız ön yüz (Anepigraphic); Herakles soyundan gelen Argead hanedanının tanrısal gücünü sembolize eder.',
                'reverse_desc' => 'Sırtlıksız tahtında oturan Zeus Aetophoros sola doğru; sol elinde uzun asa tutar, uzattığı sağ elinde kartal tünemiştir. Sol alanda Efes kent sembolü arı (bee) ve darphane monogramı.',
                'reverse_legend' => 'ΒΑΣΙΛΕΩΣ ΑΛΕΞΑΝΔΡΟΥ',
                'reverse_legend_trans' => 'Basileos Aleksandrou (Kral İskender’in mülkü / adına).',
                'rarity_grade' => RarityGrade::R2,
                'historical_notes' => 'M.Ö. 334 yılında Efes’in Pers esaretinden kurtarılmasının ardından Asya ticaret hacmini desteklemek amacıyla basılmış kraliyet gümüşüdür. Arka yüzdeki arı motifi Efes Artemis Tapınağı’nın kadim kutsal amblemidir.',
                'view_count' => 5120,
            ]
        );

        $coinAlexander->valuations()->delete();
        $coinAlexander->valuations()->createMany([
            ['condition_grade' => ConditionGrade::POOR, 'min_value_usd' => 150.00, 'max_value_usd' => 250.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::FINE, 'min_value_usd' => 380.00, 'max_value_usd' => 600.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::VF, 'min_value_usd' => 850.00, 'max_value_usd' => 1500.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::XF, 'min_value_usd' => 2200.00, 'max_value_usd' => 3800.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::UNC, 'min_value_usd' => 6000.00, 'max_value_usd' => 10500.00, 'last_updated_at' => now()],
        ]);

        $coinAlexander->images()->delete();
        $coinAlexander->images()->create([
            'image_type' => ImageType::BOTH,
            'image_path' => 'coins/alexander_tetradrachm_ephesos.webp',
            'is_primary' => true,
        ]);

        // ==================================================================
        // ÖRNEK 3: Fatih Sultan Mehmed İlk Altın Sikkesi (Sultani)
        // ==================================================================
        $coinMehmed = Coin::updateOrCreate(
            ['slug' => 'fatih-sultan-mehmed-ilk-altin-sultani-882'],
            [
                'period_id' => $ottoman->id,
                'ruler_id' => $mehmed->id,
                'mint_id' => $mints['konstantinopolis']->id,
                'title' => 'Fatih Sultan Mehmed İlk Altın Sikkesi (Sultani / Hasene-i Şerife - H. 882)',
                'catalog_reference' => 'Damalı 8-AK-G1; Pere 82; Artuk 1445',
                'denomination' => 'Sultani (Hasene-i Şerife)',
                'metal' => CoinMetal::GOLD,
                'weight_g' => 3.51,
                'diameter_mm' => 20.0,
                'obverse_desc' => 'Dört satır nesih/sülüs hatla istiflenmiş kitabe; hükümdarın hükümranlık kudretini ve altın sikke bastırma imtiyazını beyan eder.',
                'obverse_legend' => 'ضارب النضر صاحب العز والنصر في البر والبحر',
                'obverse_legend_trans' => 'Dâribü’n-nadr sâhibü’l-izzi ve’n-nasr fi’l-berri ve’l-bahr (Altını darbeden, karada ve denizde izzet ve zafer sahibi).',
                'reverse_desc' => 'Dört satır sülüs hatla hükümdarın soy silsilesi, dua ibaresi, darp yeri ve Hicri 882 tarihi.',
                'reverse_legend' => 'سلطان محمد بن مراد خان عز نصره خلد ملكه ضرب قسطنطينية ٨٨٢',
                'reverse_legend_trans' => 'Sultân Muhammed bin Murâd Hân azze nasruhu hulide mülkuhu duribe Kostantıniyye sene 882 (Murad Han oğlu Sultan Mehmed, zaferi aziz olsun, mülkü daim olsun, Kostantiniyye’de basıldı, sene 882 [M. 1477/1478]).',
                'rarity_grade' => RarityGrade::R4,
                'historical_notes' => 'Osmanlı İmparatorluğu tarihinde basılan İLK ALTIN SİKKE tipidir. İstanbul’un fethinden 24 yıl sonra (H. 882 / M. 1477-78) Venedik dükasına (Filori) rakip olarak 23.5 ayar saf altından Kostantiniyye darphanesinde darbedilmiştir. Türk-İslam nümizmatiğinin en nadide ve kıymetli şaheserlerinden biridir.',
                'view_count' => 9740,
            ]
        );

        $coinMehmed->valuations()->delete();
        $coinMehmed->valuations()->createMany([
            ['condition_grade' => ConditionGrade::POOR, 'min_value_usd' => 850.00, 'max_value_usd' => 1500.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::FINE, 'min_value_usd' => 2500.00, 'max_value_usd' => 4500.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::VF, 'min_value_usd' => 7000.00, 'max_value_usd' => 12000.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::XF, 'min_value_usd' => 16000.00, 'max_value_usd' => 26000.00, 'last_updated_at' => now()],
            ['condition_grade' => ConditionGrade::UNC, 'min_value_usd' => 38000.00, 'max_value_usd' => 65000.00, 'last_updated_at' => now()],
        ]);

        $coinMehmed->images()->delete();
        $coinMehmed->images()->create([
            'image_type' => ImageType::BOTH,
            'image_path' => 'coins/fatih_mehmed_sultani_882.webp',
            'is_primary' => true,
        ]);
    }
}

