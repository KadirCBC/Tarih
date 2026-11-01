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
        // 0. TEST KULLANICILARI (FREEMIUM & PRO)
        // ------------------------------------------------------------------
        \App\Models\User::firstOrCreate(
            ['email' => 'free@numizmatik.local'],
            [
                'name' => 'Ücretsiz Koleksiyoner',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'subscription_tier' => 'free',
            ]
        );

        $proUser = \App\Models\User::firstOrCreate(
            ['email' => 'pro@numizmatik.local'],
            [
                'name' => 'Nümizmat Pro Koleksiyoner',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'subscription_tier' => 'pro',
                'subscription_expires_at' => now()->addYear(),
                'subscription_provider' => 'apple',
                'subscription_id' => 'TX-PRO-ANNUAL-TEST',
            ]
        );

        $expertUser = \App\Models\User::firstOrCreate(
            ['email' => 'expert@numizmatik.local'],
            [
                'name' => 'Dr. Mehmet Nümizmat',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'subscription_tier' => 'pro',
                'is_expert' => true,
                'expert_title' => 'Doğrulanmış Nümizmat & Baş Editör',
            ]
        );

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
                    'ancient_name' => 'Constantinopolis / Byzantion',
                    'modern_name' => 'İstanbul, Türkiye',
                    'active_period' => 'M.S. 330 - 1453',
                    'historical_significance' => 'Doğu Roma (Bizans) İmparatorluğu\'nun baş darphanesi. CONOB damgalı altın solidus ve bronz follislerin ana üretim merkezi.',
                    'location' => 'İstanbul / Türkiye',
                    'latitude' => 41.008238,
                    'longitude' => 28.978359,
                ]
            ),
            'roma' => Mint::firstOrCreate(
                ['slug' => 'roma'],
                [
                    'name' => 'Roma',
                    'ancient_name' => 'Roma (Moneta Augusti)',
                    'modern_name' => 'Roma, İtalya',
                    'active_period' => 'M.Ö. 289 - M.S. 476',
                    'historical_significance' => 'Roma Cumhuriyeti ve Erken İmparatorluk darphanesi. Denarius, Sestertius ve Aureus basımının kalbi.',
                    'location' => 'Lazio / İtalya',
                    'latitude' => 41.890251,
                    'longitude' => 12.492373,
                ]
            ),
            'efes' => Mint::firstOrCreate(
                ['slug' => 'efes'],
                [
                    'name' => 'Efes (Ephesos)',
                    'ancient_name' => 'Ephesos / Arsinoeia',
                    'modern_name' => 'Selçuk / İzmir, Türkiye',
                    'active_period' => 'M.Ö. 6. yy - M.S. 3. yy',
                    'historical_significance' => 'İyonya ve Küçük Asya Roma eyaletinin en büyük darphanesi. Cistophoric tetradrahmi ve Artemis arı tasvirli sikkeleriyle ünlü.',
                    'location' => 'İzmir / Türkiye',
                    'latitude' => 37.941113,
                    'longitude' => 27.341944,
                ]
            ),
            'siscia' => Mint::firstOrCreate(
                ['slug' => 'siscia'],
                [
                    'name' => 'Siscia',
                    'ancient_name' => 'Siscia',
                    'modern_name' => 'Sisak / Hırvatistan',
                    'active_period' => 'M.S. 260 - 387',
                    'historical_significance' => 'Pannonia eyaletinde askeri lejyonların ödemelerini karşılamak üzere kurulan ana sınır darphanesi.',
                    'location' => 'Sisak / Hırvatistan',
                    'latitude' => 45.485121,
                    'longitude' => 16.376110,
                ]
            ),
            'antiochia' => Mint::firstOrCreate(
                ['slug' => 'antiochia'],
                [
                    'name' => 'Antiochia',
                    'ancient_name' => 'Antiochia ad Orontem',
                    'modern_name' => 'Antakya / Hatay, Türkiye',
                    'active_period' => 'M.Ö. 300 - M.S. 638',
                    'historical_significance' => 'Selevkos Krallığı ve Roma Yakındoğu eyaletlerinin baş darphanesi. Roma gümüş tetradrahmileri ve bronz senato sikkeleri basılmıştır.',
                    'location' => 'Hatay / Türkiye',
                    'latitude' => 36.202100,
                    'longitude' => 36.160600,
                ]
            ),
            'alexandria' => Mint::firstOrCreate(
                ['slug' => 'alexandria'],
                [
                    'name' => 'İskenderiye (Alexandria)',
                    'ancient_name' => 'Alexandria ad Aegyptum',
                    'modern_name' => 'İskenderiye, Mısır',
                    'active_period' => 'M.Ö. 331 - M.S. 642',
                    'historical_significance' => 'Ptolemaios Krallığı ve Roma Mısır vilayetinin merkez darphanesi. Mısır için özel kapalı para sistemi (tetradrahmi) basmıştır.',
                    'location' => 'İskenderiye / Mısır',
                    'latitude' => 31.200092,
                    'longitude' => 29.918739,
                ]
            ),
            'bursa' => Mint::firstOrCreate(
                ['slug' => 'bursa'],
                [
                    'name' => 'Bursa',
                    'ancient_name' => 'Prusa ad Olympum',
                    'modern_name' => 'Bursa / Türkiye',
                    'active_period' => 'M.Ö. 2. yy - M.S. 1450',
                    'historical_significance' => 'Bithynia Roma eyalet sikkeleri ve Erken Osmanlı akçelerinin ilk basıldığı darphanelerdendir.',
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
                'title_translations' => [
                    'tr' => 'Trajan Gümüş Denarius - Arab Adquis (Arabistan Fethi)',
                    'en' => 'Trajan Silver Denarius - Arab Adquis (Arabia Annexation)',
                    'de' => 'Trajan Silberdenar - Arab Adquis (Eroberung Arabiens)',
                ],
                'catalog_reference' => 'RIC II 142; RSC 26; BMC 499',
                'denomination' => 'Denarius',
                'metal' => CoinMetal::SILVER,
                'weight_g' => 3.25,
                'diameter_mm' => 19.0,
                'obverse_desc' => 'Defne çelenkli, zırhlı ve omzunda paludamentum bulunan Trajan büstü sağa bakar.',
                'obverse_desc_translations' => [
                    'tr' => 'Defne çelenkli, zırhlı ve omzunda paludamentum bulunan Trajan büstü sağa bakar.',
                    'en' => 'Laureate head of Trajan right, drapery on far shoulder.',
                    'de' => 'Büste des Trajan mit Lorbeerkranz und Paludamentum nach rechts.',
                ],
                'obverse_legend' => 'IMP CAES NERVA TRAIAN AVG GERM',
                'obverse_legend_trans' => 'Imperator Caesar Nerva Traianus Augustus Germanicus (Başkomutan Sezar Nerva Traianus Augustus, Cermenlerin Fatihi).',
                'obverse_legend_trans_translations' => [
                    'tr' => 'Imperator Caesar Nerva Traianus Augustus Germanicus (Başkomutan Sezar Nerva Traianus Augustus, Cermenlerin Fatihi).',
                    'en' => 'Imperator Caesar Nerva Traianus Augustus Germanicus (Supreme Commander Caesar Nerva Trajan Augustus, Conqueror of the Germans).',
                    'de' => 'Imperator Caesar Nerva Traianus Augustus Germanicus (Oberbefehlshaber Caesar Nerva Trajan Augustus, Bezwinger der Germanen).',
                ],
                'reverse_desc' => 'Ayakta duran kadın figürü (Arabia personifikasyonu) sola bakar; sağ elinde defne dalı, sol elinde kamış tutar; sol alt yanında tek hörgüçlü deve yavrusu durmaktadır.',
                'reverse_desc_translations' => [
                    'tr' => 'Ayakta duran kadın figürü (Arabia personifikasyonu) sola bakar; sağ elinde defne dalı, sol elinde kamış tutar; sol alt yanında tek hörgüçlü deve yavrusu durmaktadır.',
                    'en' => 'Arabia standing left, holding branch in right hand and bundle of canes in left; at her feet to left, a camel walking left.',
                    'de' => 'Arabia steht nach links, Zweig in der Rechten und Bündel von Röhricht in der Linken haltend; zu ihren Füßen links ein Dromedar.',
                ],
                'reverse_legend' => 'ARAB ADQ COS V P P S P Q R OPTIMO PRINC',
                'reverse_legend_trans' => 'Arabia Adquisita, Consul V, Pater Patriae, Senatus Populusque Romanus Optimo Principi (Arabistan Eyaleti Roma’ya Katıldı. 5. Kez Konsül, Vatanın Babası, Senato ve Roma Halkından En Kusursuz Öndere).',
                'reverse_legend_trans_translations' => [
                    'tr' => 'Arabia Adquisita, Consul V, Pater Patriae, Senatus Populusque Romanus Optimo Principi (Arabistan Eyaleti Roma’ya Katıldı. 5. Kez Konsül, Vatanın Babası, Senato ve Roma Halkından En Kusursuz Öndere).',
                    'en' => 'Arabia Adquisita, Consul V, Pater Patriae, Senatus Populusque Romanus Optimo Principi (Arabia Annexed, Consul for the 5th time, Father of the Fatherland, to the Best Prince from Senate and Roman People).',
                    'de' => 'Arabia Adquisita, Consul V, Pater Patriae, Senatus Populusque Romanus Optimo Principi (Arabien erworben, Konsul zum 5. Mal, Vater des Vaterlandes, dem besten Fürsten von Senat und Volk von Rom).',
                ],
                'rarity_grade' => RarityGrade::R1,
                'historical_notes' => 'Trajan’ın M.S. 106 yılında Nebati (Nabataean) Krallığı’nı fethederek Roma’nın "Arabia Petraea" eyaletini kurması şerefine darbedilmiştir. Sikke üzerindeki deve tasviri, Roma nümizmatiğinde Doğu eyaletlerinin sembolik zafer tasvirlerinin en seçkin örneklerindendir.',
                'historical_notes_translations' => [
                    'tr' => 'Trajan’ın M.S. 106 yılında Nebati (Nabataean) Krallığı’nı fethederek Roma’nın "Arabia Petraea" eyaletini kurması şerefine darbedilmiştir. Sikke üzerindeki deve tasviri, Roma nümizmatiğinde Doğu eyaletlerinin sembolik zafer tasvirlerinin en seçkin örneklerindendir.',
                    'en' => 'Struck to celebrate the annexation of the Nabataean Kingdom in AD 106 and the creation of the Roman province of Arabia Petraea. The camel depiction is one of the classic provincial personifications in Roman imperial numismatics.',
                    'de' => 'Geprägt anlässlich der Annexion des Nabatäerreichs 106 n. Chr. und der Gründung der römischen Provinz Arabia Petraea. Das Dromedar gehört zu den klassischen provinziellen Personifikationen der römischen Numismatik.',
                ],
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

        // Çapraz Referanslar (Concordance) & Varyantlar
        $coinTrajan->references()->delete();
        $coinTrajan->references()->createMany([
            ['catalog_system' => 'RIC', 'reference_number' => 'II 142', 'notes' => 'Roman Imperial Coinage Vol II, Trajan No 142'],
            ['catalog_system' => 'RSC', 'reference_number' => '26', 'notes' => 'Roman Silver Coins No 26'],
            ['catalog_system' => 'BMC', 'reference_number' => '499', 'notes' => 'British Museum Coins of the Roman Empire No 499'],
            ['catalog_system' => 'Cohen', 'reference_number' => '26', 'notes' => 'Description historique des monnaies No 26'],
            ['catalog_system' => 'Sear', 'reference_number' => '3118', 'notes' => 'Roman Coins and Their Values II 3118'],
        ]);

        $coinTrajan->variants()->delete();
        $coinTrajan->variants()->createMany([
            [
                'variant_code' => 'RIC 142a',
                'obverse_diff' => 'Ön yüzde paludamentum sadece sol omuzda (drapery on left shoulder).',
                'reverse_diff' => 'Arka yüzde deve sola bakar, Arabia dalı aşağı eğik tutar.',
                'rarity_shift' => 'R1',
            ],
            [
                'variant_code' => 'RIC 142b',
                'obverse_diff' => 'Ön yüzde tam zırh ve pelerin (cuirassed & draped).',
                'reverse_diff' => 'Arka yüzde deve başını yukarı kaldırmış, Arabia kamış demetini göğsüne yakın tutar.',
                'rarity_shift' => 'R2',
            ],
        ]);

        // Gerçekleşen Müzayede Satış Kayıtları (Auction Records)
        $coinTrajan->auctionRecords()->delete();
        $coinTrajan->auctionRecords()->createMany([
            [
                'auction_house' => 'Classical Numismatic Group (CNG)',
                'sale_name_or_number' => 'Electronic Auction 512',
                'lot_number' => 'Lot 482',
                'sale_date' => now()->subMonths(6)->toDateString(),
                'hammer_price_usd' => 380.00,
                'realized_price_usd' => 456.00,
                'condition_grade' => 'VF',
                'source_url' => 'https://cngcoins.com/Coin.aspx?CoinID=512482',
            ],
            [
                'auction_house' => 'Heritage Auctions',
                'sale_name_or_number' => 'World Coins Signature 3094',
                'lot_number' => 'Lot 31055',
                'sale_date' => now()->subMonths(10)->toDateString(),
                'hammer_price_usd' => 425.00,
                'realized_price_usd' => 510.00,
                'condition_grade' => 'VF',
                'source_url' => 'https://ha.com/lot/3094-31055',
            ],
            [
                'auction_house' => 'Leu Numismatik',
                'sale_name_or_number' => 'Web Auction 21',
                'lot_number' => 'Lot 840',
                'sale_date' => now()->subMonths(14)->toDateString(),
                'hammer_price_usd' => 320.00,
                'realized_price_usd' => 384.00,
                'condition_grade' => 'VF',
                'source_url' => 'https://leunumismatik.com/en/lot/21/840',
            ],
            [
                'auction_house' => 'Roma Numismatics',
                'sale_name_or_number' => 'E-Sale 96',
                'lot_number' => 'Lot 711',
                'sale_date' => now()->subMonths(18)->toDateString(),
                'hammer_price_usd' => 1100.00,
                'realized_price_usd' => 1320.00,
                'condition_grade' => 'XF',
                'source_url' => 'https://romanumismatics.com/lot/96/711',
            ],
            [
                'auction_house' => 'Gorny & Mosch',
                'sale_name_or_number' => 'Auction 284',
                'lot_number' => 'Lot 1520',
                'sale_date' => now()->subMonths(8)->toDateString(),
                'hammer_price_usd' => 2800.00,
                'realized_price_usd' => 3360.00,
                'condition_grade' => 'UNC',
                'source_url' => 'https://gmcoinart.de/lot/284/1520',
            ],
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
                'title_translations' => [
                    'tr' => 'Büyük İskender Gümüş Tetradrahmi - Efes Darbı (Herakles & Zeus)',
                    'en' => 'Alexander the Great Silver Tetradrachm - Ephesus Mint (Herakles & Zeus)',
                    'de' => 'Alexander der Große Silbertetradrachme - Prägestätte Ephesos (Herakles & Zeus)',
                ],
                'catalog_reference' => 'Price 1759; Müller 522',
                'denomination' => 'Tetradrahmi',
                'metal' => CoinMetal::SILVER,
                'weight_g' => 17.20,
                'diameter_mm' => 27.5,
                'obverse_desc' => 'Nemea aslanının postunu başlık olarak giymiş genç Herakles başı sağa bakar.',
                'obverse_desc_translations' => [
                    'tr' => 'Nemea aslanının postunu başlık olarak giymiş genç Herakles başı sağa bakar.',
                    'en' => 'Head of Herakles right, wearing lion skin headdress.',
                    'de' => 'Kopf des jugendlichen Herakles nach rechts im Löwenfell.',
                ],
                'obverse_legend' => null,
                'obverse_legend_trans' => 'Yazısız ön yüz (Anepigraphic); Herakles soyundan gelen Argead hanedanının tanrısal gücünü sembolize eder.',
                'obverse_legend_trans_translations' => [
                    'tr' => 'Yazısız ön yüz (Anepigraphic); Herakles soyundan gelen Argead hanedanının tanrısal gücünü sembolize eder.',
                    'en' => 'Anepigraphic obverse, representing the divine heritage of the Argead dynasty descended from Herakles.',
                    'de' => 'Anepigraphische Vorderseite; symbolisiert das göttliche Erbe der argeadischen Dynastie, die von Herakles abstammt.',
                ],
                'reverse_desc' => 'Sırtlıksız tahtında oturan Zeus Aetophoros sola doğru; sol elinde uzun asa tutar, uzattığı sağ elinde kartal tünemiştir. Sol alanda Efes kent sembolü arı (bee) ve darphane monogramı.',
                'reverse_desc_translations' => [
                    'tr' => 'Sırtlıksız tahtında oturan Zeus Aetophoros sola doğru; sol elinde uzun asa tutar, uzattığı sağ elinde kartal tünemiştir. Sol alanda Efes kent sembolü arı (bee) ve darphane monogramı.',
                    'en' => 'Zeus Aëtophoros seated left on throne, holding sceptre in left hand and eagle on outstretched right hand. In left field, bee of Ephesus and mint monogram.',
                    'de' => 'Zeus Aëtophoros thront nach links, langes Zepter in der Linken, Adler auf der ausgestreckten Rechten. Im linken Feld die Biene von Ephesos und Prägemonogramm.',
                ],
                'reverse_legend' => 'ΒΑΣΙΛΕΩΣ ΑΛΕΞΑΝΔΡΟΥ',
                'reverse_legend_trans' => 'Basileos Aleksandrou (Kral İskender’in mülkü / adına).',
                'reverse_legend_trans_translations' => [
                    'tr' => 'Basileos Aleksandrou (Kral İskender’in mülkü / adına).',
                    'en' => 'Basileos Alexandrou (Of King Alexander).',
                    'de' => 'Basileos Alexandrou (Des Königs Alexander).',
                ],
                'rarity_grade' => RarityGrade::R2,
                'historical_notes' => 'M.Ö. 334 yılında Efes’in Pers esaretinden kurtarılmasının ardından Asya ticaret hacmini desteklemek amacıyla basılmış kraliyet gümüşüdür. Arka yüzdeki arı motifi Efes Artemis Tapınağı’nın kadim kutsal amblemidir.',
                'historical_notes_translations' => [
                    'tr' => 'M.Ö. 334 yılında Efes’in Pers esaretinden kurtarılmasının ardından Asya ticaret hacmini desteklemek amacıyla basılmış kraliyet gümüşüdür. Arka yüzdeki arı motifi Efes Artemis Tapınağı’nın kadim kutsal amblemidir.',
                    'en' => 'Struck following the liberation of Ephesus from Persian rule in 334 BC. The bee on the reverse is the ancient sacred emblem of the Temple of Artemis at Ephesus.',
                    'de' => 'Geprägt nach der Befreiung von Ephesos von der persischen Herrschaft 334 v. Chr. Das Bienenmotiv auf der Rückseite ist das heilige Emblem des Artemistempels von Ephesos.',
                ],
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

        // Çapraz Referanslar (Concordance) & Varyantlar
        $coinAlexander->references()->delete();
        $coinAlexander->references()->createMany([
            ['catalog_system' => 'Price', 'reference_number' => '1759', 'notes' => 'The Coinage in the Name of Alexander the Great No 1759'],
            ['catalog_system' => 'Muller', 'reference_number' => '522', 'notes' => 'Numismatique d\'Alexandre le Grand No 522'],
            ['catalog_system' => 'SNG', 'reference_number' => 'Cop 885', 'notes' => 'Sylloge Nummorum Graecorum Copenhagen 885'],
            ['catalog_system' => 'Sear', 'reference_number' => '6713', 'notes' => 'Greek Coins and Their Values 6713'],
        ]);

        $coinAlexander->variants()->delete();
        $coinAlexander->variants()->createMany([
            [
                'variant_code' => 'Price 1759 Var A',
                'obverse_diff' => 'Herakles postunun düğümü çene altında çift kıvrımlı.',
                'reverse_diff' => 'Sol alanda kanatları açık arı, taht altında monogram Δ.',
                'rarity_shift' => 'R2',
            ],
            [
                'variant_code' => 'Price 1759 Var B',
                'obverse_diff' => 'Herakles başı daha dar ve uzun profilli.',
                'reverse_diff' => 'Sol alanda arının üzerinde küçük 8 ışınlı yıldız, taht altında monogram AP.',
                'rarity_shift' => 'R3',
            ],
        ]);

        // Gerçekleşen Müzayede Satış Kayıtları (Alexander)
        $coinAlexander->auctionRecords()->delete();
        $coinAlexander->auctionRecords()->createMany([
            [
                'auction_house' => 'Classical Numismatic Group (CNG)',
                'sale_name_or_number' => 'Triton XXVI',
                'lot_number' => 'Lot 142',
                'sale_date' => now()->subMonths(4)->toDateString(),
                'hammer_price_usd' => 1200.00,
                'realized_price_usd' => 1440.00,
                'condition_grade' => 'VF',
                'source_url' => 'https://cngcoins.com/Coin.aspx?CoinID=260142',
            ],
            [
                'auction_house' => 'Heritage Auctions',
                'sale_name_or_number' => 'NYINC Signature 3102',
                'lot_number' => 'Lot 30045',
                'sale_date' => now()->subMonths(9)->toDateString(),
                'hammer_price_usd' => 2600.00,
                'realized_price_usd' => 3120.00,
                'condition_grade' => 'XF',
                'source_url' => 'https://ha.com/lot/3102-30045',
            ],
            [
                'auction_house' => 'Leu Numismatik',
                'sale_name_or_number' => 'Auction 14',
                'lot_number' => 'Lot 95',
                'sale_date' => now()->subMonths(13)->toDateString(),
                'hammer_price_usd' => 7500.00,
                'realized_price_usd' => 9000.00,
                'condition_grade' => 'UNC',
                'source_url' => 'https://leunumismatik.com/en/lot/14/95',
            ],
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
                'title_translations' => [
                    'tr' => 'Fatih Sultan Mehmed İlk Altın Sikkesi (Sultani / Hasene-i Şerife - H. 882)',
                    'en' => 'Mehmed the Conqueror First Gold Sultani (Hasene-i Sharifa - AH 882)',
                    'de' => 'Mehmed der Eroberer Erste Goldsultani (Hasene-i Scherife - AH 882)',
                ],
                'catalog_reference' => 'Damalı 8-AK-G1; Pere 82; Artuk 1445',
                'denomination' => 'Sultani (Hasene-i Şerife)',
                'metal' => CoinMetal::GOLD,
                'weight_g' => 3.51,
                'diameter_mm' => 20.0,
                'obverse_desc' => 'Dört satır nesih/sülüs hatla istiflenmiş kitabe; hükümdarın hükümranlık kudretini ve altın sikke bastırma imtiyazını beyan eder.',
                'obverse_desc_translations' => [
                    'tr' => 'Dört satır nesih/sülüs hatla istiflenmiş kitabe; hükümdarın hükümranlık kudretini ve altın sikke bastırma imtiyazını beyan eder.',
                    'en' => 'Four-line Naskh/Thuluth inscription declaring the ruler’s sovereign right of minting gold currency.',
                    'de' => 'Vierzeilige Naskh/Thuluth-Inschrift, die das herrscherliche Privileg der Goldmünzprägung bekundet.',
                ],
                'obverse_legend' => 'ضارب النضر صاحب العز والنصر في البر والبحر',
                'obverse_legend_trans' => 'Dâribü’n-nadr sâhibü’l-izzi ve’n-nasr fi’l-berri ve’l-bahr (Altını darbeden, karada ve denizde izzet ve zafer sahibi).',
                'obverse_legend_trans_translations' => [
                    'tr' => 'Dâribü’n-nadr sâhibü’l-izzi ve’n-nasr fi’l-berri ve’l-bahr (Altını darbeden, karada ve denizde izzet ve zafer sahibi).',
                    'en' => 'Darib al-nadr sahib al-izz wa-l-nasr fi-l-barr wa-l-bahr (Striker of gold, master of glory and victory on land and sea).',
                    'de' => 'Darib an-nadr sahib al-izz wa-n-nasr fi-l-barr wa-l-bahr (Präger des Goldes, Herr der Macht und des Sieges zu Lande und zu Wasser).',
                ],
                'reverse_desc' => 'Dört satır sülüs hatla hükümdarın soy silsilesi, dua ibaresi, darp yeri ve Hicri 882 tarihi.',
                'reverse_desc_translations' => [
                    'tr' => 'Dört satır sülüs hatla hükümdarın soy silsilesi, dua ibaresi, darp yeri ve Hicri 882 tarihi.',
                    'en' => 'Four lines stating the Sultan’s lineage, benediction, Constantinople mint name, and date AH 882.',
                    'de' => 'Vierzeilige Genealogie des Sultans mit Segensformel, Prägestätte Konstantinopel und Datum AH 882.',
                ],
                'reverse_legend' => 'سلطان محمد بن مراد خان عز نصره خلد ملكه ضرب قسطنطينية ٨٨٢',
                'reverse_legend_trans' => 'Sultân Muhammed bin Murâd Hân azze nasruhu hulide mülkuhu duribe Kostantıniyye sene 882 (Murad Han oğlu Sultan Mehmed, zaferi aziz olsun, mülkü daim olsun, Kostantiniyye’de basıldı, sene 882 [M. 1477/1478]).',
                'reverse_legend_trans_translations' => [
                    'tr' => 'Sultân Muhammed bin Murâd Hân azze nasruhu hulide mülkuhu duribe Kostantıniyye sene 882 (Murad Han oğlu Sultan Mehmed, zaferi aziz olsun, mülkü daim olsun, Kostantiniyye’de basıldı, sene 882 [M. 1477/1478]).',
                    'en' => 'Sultan Muhammad ibn Murad Khan azza nasruhu khullida mulkuhu duriba Qustantiniyya sanat 882 (Sultan Mehmed son of Murad Khan, may his victory be glorified and his kingdom perpetuated, struck in Constantinople, year 882 [1477/78 AD]).',
                    'de' => 'Sultan Muhammad ibn Murad Khan azza nasruhu khullida mulkuhu duriba Qustantiniyya sanat 882 (Sultan Mehmed, Sohn des Murad Khan, möge sein Sieg verherrlicht und sein Reich beständig sein, geprägt in Konstantinopel, Jahr 882 [1477/78 n. Chr.]).',
                ],
                'rarity_grade' => RarityGrade::R4,
                'historical_notes' => 'Osmanlı İmparatorluğu tarihinde basılan İLK ALTIN SİKKE tipidir. İstanbul’un fethinden 24 yıl sonra (H. 882 / M. 1477-78) Venedik dükasına (Filori) rakip olarak 23.5 ayar saf altından Kostantiniyye darphanesinde darbedilmiştir. Türk-İslam nümizmatiğinin en nadide ve kıymetli şaheserlerinden biridir.',
                'historical_notes_translations' => [
                    'tr' => 'Osmanlı İmparatorluğu tarihinde basılan İLK ALTIN SİKKE tipidir. İstanbul’un fethinden 24 yıl sonra (H. 882 / M. 1477-78) Venedik dükasına (Filori) rakip olarak 23.5 ayar saf altından Kostantiniyye darphanesinde darbedilmiştir. Türk-İslam nümizmatiğinin en nadide ve kıymetli şaheserlerinden biridir.',
                    'en' => 'The FIRST GOLD COIN struck in Ottoman history. Issued 24 years after the conquest of Constantinople (AH 882 / 1477-78 AD) to rival the Venetian Ducat (Flörin) in 23.5k fine gold at the Constantinople mint. A cornerstone of Islamic numismatics.',
                    'de' => 'Die ERSTE GOLDMÜNZE der osmanischen Geschichte. Geprägt 24 Jahre nach der Eroberung Konstantinopels (AH 882 / 1477-78 n. Chr.) als Konkurrenz zum venezianischen Dukaten aus 23,5-karätigem Feingold in der Münzstätte Konstantinopel.',
                ],
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

        // Çapraz Referanslar (Concordance) & Varyantlar
        $coinMehmed->references()->delete();
        $coinMehmed->references()->createMany([
            ['catalog_system' => 'Damali', 'reference_number' => '8-AK-G1', 'notes' => 'Osmanlı Sikkeleri Tarihi Cilt 2, No 8-AK-G1'],
            ['catalog_system' => 'Pere', 'reference_number' => '82', 'notes' => 'Osmanlılarda Madenî Paralar Levha 8, No 82'],
            ['catalog_system' => 'Artuk', 'reference_number' => '1445', 'notes' => 'İslami Sikkeler Kataloğu No 1445'],
        ]);

        $coinMehmed->variants()->delete();
        $coinMehmed->variants()->createMany([
            [
                'variant_code' => '8-AK-G1 Var 1',
                'obverse_diff' => 'Ön yüz 3. satır "ve\'n-nasr" ibaresi üzerinde lale motifi.',
                'reverse_diff' => 'Arka yüz "duribe" üzerinde üç nokta süslemesi.',
                'rarity_shift' => 'R4',
            ],
            [
                'variant_code' => '8-AK-G1 Var 2',
                'obverse_diff' => 'Ön yüz düz sülüs hat, süslemesiz.',
                'reverse_diff' => 'Arka yüz "duribe Kostantıniyye" arasında ebediyet düğümü motifi.',
                'rarity_shift' => 'R5',
            ],
        ]);

        // Gerçekleşen Müzayede Satış Kayıtları (Mehmed II)
        $coinMehmed->auctionRecords()->delete();
        $coinMehmed->auctionRecords()->createMany([
            [
                'auction_house' => 'Spink London',
                'sale_name_or_number' => 'Islamic & World Coins 22010',
                'lot_number' => 'Lot 38',
                'sale_date' => now()->subMonths(7)->toDateString(),
                'hammer_price_usd' => 8500.00,
                'realized_price_usd' => 10200.00,
                'condition_grade' => 'VF',
                'source_url' => 'https://spink.com/lot/22010/38',
            ],
            [
                'auction_house' => 'Sotheby\'s',
                'sale_name_or_number' => 'Arts of the Islamic World L23220',
                'lot_number' => 'Lot 104',
                'sale_date' => now()->subMonths(15)->toDateString(),
                'hammer_price_usd' => 18000.00,
                'realized_price_usd' => 22500.00,
                'condition_grade' => 'XF',
                'source_url' => 'https://sothebys.com/en/buy/auction/2023/lot.104',
            ],
        ]);

        // ==================================================================
        // 5. HAFTANIN SİKKESİ VE EDİTORYAL HİKAYE (Google Discover Uyumlu)
        // ==================================================================
        \App\Models\CoinStory::updateOrCreate(
            ['slug' => 'nebatilerin-fethi-ve-trajanin-efsanevi-denariusu'],
            [
                'coin_id' => $coinTrajan->id,
                'title' => 'Çölün Gizemli Krallığı Nasıl Düştü? Trajan’ın Deve Tasvirli Efsanevi Gümüş Denarius’u',
                'summary' => 'M.S. 106 yılında Roma orduları Petra Antik Kenti’ne girdiğinde Nebati Krallığı tarihe karıştı. İmparator Trajan, bu benzersiz çöl zaferini Roma sokaklarında dağıtılan develerle süslü gümüş sikkelerle taçlandırdı.',
                'content' => "Antik dünyanın en zengin ticaret yollarını kontrol eden Nebatiler, binlerce yıl boyunca Roma lejyonlarına direnmişti. Ancak M.S. 106 yılında 'Optimus Princeps' (En İyi İmparator) unvanlı Trajan, Suriye valisi Aulus Cornelius Palma komutasındaki lejyonları Ürdün çöllerine sürdü.\n\nPetra'nın fethiyle birlikte Nebati hazineleri Roma'ya taşındı. Trajan, Roma Senatosu'ndan aldığı yetkiyle bu zaferi tüm imparatorluğa duyurmak için darphanelere özel bir talimat verdi: 'Arabia Adquisita' (Arabistan Ele Geçirildi) sikkeleri.\n\nSikkenin arka yüzünde duran zarif kadın figürü (Arabistan personifikasyonu), elinde tütsü ve defne dalı tutarken hemen ayaklarının dibinde duran yavru tek hörgüçlü deve, Roma nümizmatiğinde Doğu egzotizminin en nadide sembollerinden biri haline geldi.\n\nBugün bu sikkeden günümüze çok az sayıda çil (UNC) kondisyonda örnek ulaşabilmiştir. Uluslararası müzayedelerde yüksek talep gören bu sikke, hem Roma'nın sınırlarını Kızıldeniz'e ulaştırmasının hem de antik Petra'nın hüzünlü vedasının madeni bir belgesidir.",
                'cover_image' => 'https://images.unsplash.com/photo-1579606032824-345be11b2b80?auto=format&fit=crop&w=1200&q=80',
                'is_coin_of_the_week' => true,
                'published_at' => now()->subDays(2),
                'view_count' => 14800,
            ]
        );

        // ==================================================================
        // 6. SİKKE KİMLİKLEME KULÜBÜ ÖRNEK TALEPLERİ
        // ==================================================================
        $request1 = \App\Models\IdentificationRequest::updateOrCreate(
            ['title' => 'Gümüş Antik Sikke - Üzerinde Deve ve Ayakta Kadın Tasviri Var'],
            [
                'user_id' => $proUser->id,
                'description' => 'Eski bir koleksiyonda bulunan gümüş denarius benzeri para. Ön yüzde defne çelenkli bir imparator büstü, arka yüzde ise deve yavrusunun yanında duran bir kadın figürü var. Hangi imparator ve darphane olduğunu öğrenmek istiyorum.',
                'obverse_image' => 'coins/trajan_denarius_arab_adquis.webp',
                'reverse_image' => 'coins/trajan_denarius_arab_adquis.webp',
                'weight_g' => 3.22,
                'diameter_mm' => 18.8,
                'status' => 'identified',
                'identified_coin_id' => $coinTrajan->id,
                'verified_by_user_id' => $expertUser->id,
                'expert_notes' => 'Lejanttaki ARAB ADQ ve COS V P P ibareleri gereğince bu sikke İmparator Trajan\'ın M.S. 106-111 arası darbedilen ünlü Arabia Adquisita (RIC II 142) gümüş denarius\'udur. Orijinal darp standartlarına uygundur.',
            ]
        );

        $request1->comments()->delete();
        $request1->comments()->createMany([
            [
                'user_id' => $proUser->id,
                'suggested_coin_id' => null,
                'comment' => 'Bence Roma imparatorluk dönemine ait bir denarius; deve figürü çok karakteristik duruyor.',
                'is_expert_verification' => false,
            ],
            [
                'user_id' => $expertUser->id,
                'suggested_coin_id' => $coinTrajan->id,
                'comment' => '✅ [UZMAN ONAYI]: Sikke kesinlikle İmparator Trajan dönemine (M.S. 106) ait gümüş Denarius’tur (RIC II 142). Katalog kaydıyla eşleştirilerek talep kapatılmıştır.',
                'is_expert_verification' => true,
            ],
        ]);

        // ==================================================================
        // 7. DARP HATALARI VE REHBER VERİLERİ (MINT ERROR TYPES)
        // ==================================================================
        $this->call(MintErrorTypeSeeder::class);
    }
}

