<?php

namespace Tests\Feature;

use App\Enums\ConditionGrade;
use App\Models\Coin;
use App\Models\CoinValuation;
use App\Models\Period;
use App\Services\CoinValuationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ValuationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected CoinValuationService $valuationService;
    protected Coin $coin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->valuationService = app(CoinValuationService::class);

        $period = Period::create([
            'name' => 'Roma İmparatorluğu',
            'slug' => 'roma-imparatorlugu',
            'start_year' => -27,
            'end_year' => 476,
        ]);

        $this->coin = Coin::create([
            'period_id' => $period->id,
            'name' => 'Hadrianus Denarius',
            'slug' => 'hadrianus-denarius',
            'denomination' => 'Denarius',
            'metal' => 'silver',
            'weight_g' => 3.20,
            'diameter_mm' => 18.5,
            'is_published' => true,
        ]);
    }

    /**
     * Geçerli bir kondisyon (VF) verildiğinde doğru USD değer aralığının hesaplandığını doğrular.
     */
    public function test_calculates_correct_usd_range_for_valid_condition_grade(): void
    {
        $result = $this->valuationService->calculateForCondition($this->coin, 'VF');

        $this->assertEquals('VF', $result['condition']);
        $this->assertIsFloat($result['min_value_usd']);
        $this->assertIsFloat($result['max_value_usd']);
        $this->assertGreaterThan(0, $result['min_value_usd']);
        $this->assertGreaterThan($result['min_value_usd'], $result['max_value_usd']);
        $this->assertStringContainsString('$', $result['formatted_range']);
    }

    /**
     * Geçersiz bir kondisyon verildiğinde InvalidArgumentException fırlatıldığını denetler.
     */
    public function test_throws_exception_when_invalid_condition_is_provided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Geçersiz kondisyon derecesi: 'INVALID_GRADE'");

        $this->valuationService->calculateForCondition($this->coin, 'INVALID_GRADE');
    }

    /**
     * Veritabanında kayıtlı coin_valuations kaydı varsa doğrudan bu değerlerin döndürüldüğünü doğrular.
     */
    public function test_prefers_database_persisted_valuation_records(): void
    {
        CoinValuation::create([
            'coin_id' => $this->coin->id,
            'condition_grade' => ConditionGrade::XF,
            'min_value_usd' => 350.00,
            'max_value_usd' => 550.00,
            'last_updated_at' => now(),
        ]);

        $result = $this->valuationService->calculateForCondition($this->coin, 'XF');

        $this->assertEquals(350.00, $result['min_value_usd']);
        $this->assertEquals(550.00, $result['max_value_usd']);
        $this->assertEquals('$350.00 - $550.00', $result['formatted_range']);
    }

    /**
     * Kondisyon kalitesi arttıkça (Fine -> VF -> XF -> UNC) değer aralıklarının yükseldiğini test eder.
     */
    public function test_valuation_multipliers_scale_with_higher_condition_grades(): void
    {
        $fine = $this->valuationService->calculateForCondition($this->coin, ConditionGrade::FINE);
        $vf = $this->valuationService->calculateForCondition($this->coin, ConditionGrade::VF);
        $xf = $this->valuationService->calculateForCondition($this->coin, ConditionGrade::XF);
        $unc = $this->valuationService->calculateForCondition($this->coin, ConditionGrade::UNC);

        $this->assertLessThan($vf['min_value_usd'], $fine['min_value_usd']);
        $this->assertLessThan($xf['min_value_usd'], $vf['min_value_usd']);
        $this->assertLessThan($unc['min_value_usd'], $xf['min_value_usd']);
    }
}

