<?php

namespace Tests\Feature;

use App\Models\Coin;
use App\Models\Mint;
use App\Models\Period;
use App\Models\Ruler;
use App\Services\Epigraphy\LegendReconstructionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoinSearchTest extends TestCase
{
    use RefreshDatabase;

    protected Period $romanPeriod;
    protected Period $greekPeriod;
    protected Ruler $trajan;
    protected Ruler $alexander;
    protected Mint $romeMint;
    protected Coin $trajanDenarius;
    protected Coin $alexanderTetradrachm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->romanPeriod = Period::create([
            'name' => 'Roma İmparatorluğu',
            'slug' => 'roma-imparatorlugu',
            'start_year' => -27,
            'end_year' => 476,
        ]);

        $this->greekPeriod = Period::create([
            'name' => 'Klasik & Hellenistik Grek',
            'slug' => 'klasik-hellenistik-grek',
            'start_year' => -500,
            'end_year' => -31,
        ]);

        $this->trajan = Ruler::create([
            'period_id' => $this->romanPeriod->id,
            'name' => 'Traianus (Trajan)',
            'slug' => 'trajan',
            'reign_start_year' => 98,
            'reign_end_year' => 117,
        ]);

        $this->alexander = Ruler::create([
            'period_id' => $this->greekPeriod->id,
            'name' => 'Büyük İskender (Alexander III)',
            'slug' => 'buyuk-iskender',
            'reign_start_year' => -336,
            'reign_end_year' => -323,
        ]);

        $this->romeMint = Mint::create([
            'name' => 'Roma Darphanesi',
            'slug' => 'roma',
            'location' => 'Roma, İtalya',
        ]);

        $this->trajanDenarius = Coin::create([
            'period_id' => $this->romanPeriod->id,
            'ruler_id' => $this->trajan->id,
            'mint_id' => $this->romeMint->id,
            'name' => 'Trajan Denarius - Optimus Princeps',
            'slug' => 'trajan-denarius-optimus',
            'obverse_legend' => 'IMP CAES NERVA TRAIAN AVG GERM',
            'reverse_legend' => 'P M TR P COS IIII P P',
            'denomination' => 'Denarius',
            'metal' => 'silver',
            'weight_g' => 3.35,
            'diameter_mm' => 19.0,
            'is_published' => true,
        ]);

        $this->alexanderTetradrachm = Coin::create([
            'period_id' => $this->greekPeriod->id,
            'ruler_id' => $this->alexander->id,
            'name' => 'Büyük İskender Tetradrahmi',
            'slug' => 'buyuk-iskender-tetradrahmi',
            'obverse_legend' => 'HERAKLES BASILEOS',
            'reverse_legend' => 'ΒΑΣΙΛΕΩΣ ΑΛΕΞΑΝΔΡΟΥ',
            'denomination' => 'Tetradrachm',
            'metal' => 'silver',
            'weight_g' => 17.20,
            'diameter_mm' => 28.0,
            'is_published' => true,
        ]);
    }

    /**
     * Latince lejant metniyle arama yapıldığında doğru sikkenin listelendiğini doğrular.
     */
    public function test_can_search_coin_by_latin_obverse_legend(): void
    {
        $response = $this->getJson('/api/v1/coins?q=NERVA+TRAIAN');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $this->trajanDenarius->id)
            ->assertJsonPath('data.0.slug', 'trajan-denarius-optimus');
    }

    /**
     * Grekçe lejant arandığında doğru sikkenin bulunduğunu doğrular.
     */
    public function test_can_search_coin_by_greek_reverse_legend(): void
    {
        $response = $this->getJson('/api/v1/coins?q=ΑΛΕΞΑΝΔΡΟΥ');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.id', $this->alexanderTetradrachm->id)
            ->assertJsonPath('data.0.slug', 'buyuk-iskender-tetradrahmi');
    }

    /**
     * Aşınmış parçalı lejant girdisinde LegendReconstructionService'in doğru sikkeyi aday gösterdiğini doğrular.
     */
    public function test_legend_reconstruction_service_matches_worn_latin_legend(): void
    {
        $service = app(LegendReconstructionService::class);
        $results = $service->reconstruct('...NERV...TRAI...GERM...', 'obverse');

        $this->assertNotEmpty($results);
        $this->assertEquals($this->trajanDenarius->id, $results[0]['coin_id']);
        $this->assertStringContainsString('NERVA TRAIAN', $results[0]['target_legend']);
        $this->assertGreaterThanOrEqual(50, $results[0]['confidence_score']);
    }

    /**
     * Eşleşmeyen anlamsız bir lejant arandığında boş sonuç döndüğünü doğrular.
     */
    public function test_non_matching_search_returns_empty_data(): void
    {
        $response = $this->getJson('/api/v1/coins?q=XYZNONEXISTENTLEGEND123');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEmpty($data);
    }
}

