<?php

namespace Tests\Unit;

use App\Models\Coin;
use App\Services\AuthenticityDiagnosticService;
use PHPUnit\Framework\TestCase;

class WeightDeviationTest extends TestCase
{
    protected AuthenticityDiagnosticService $diagnosticService;
    protected Coin $referenceCoin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->diagnosticService = new AuthenticityDiagnosticService();

        // Standart bir Roma Denarius referansı: 3.40g ağırlık, 19.0mm çap
        $this->referenceCoin = new Coin([
            'weight_g' => 3.40,
            'diameter_mm' => 19.0,
        ]);
    }

    /**
     * %8'in altındaki normal antik darp flan toleranslarının 'normal' teşhis aldığını test eder.
     */
    public function test_flags_tolerable_weight_deviations_as_normal(): void
    {
        // 3.40g yerine 3.30g (-%2.9 sapma)
        $analysis = $this->diagnosticService->analyzePhysicalDeviations($this->referenceCoin, 3.30, 19.0);

        $this->assertEquals('normal', $analysis['status']);
        $this->assertEquals('success', $analysis['level']);
        $this->assertLessThanOrEqual(8.0, abs($analysis['weight_deviation_percent']));
        $this->assertStringContainsString('tolerans', strtolower($analysis['title']));
    }

    /**
     * %8 - %15 arasındaki sapmaların dolaşımdan kaynaklı hafif aşınma ('warning') olduğunu test eder.
     */
    public function test_flags_moderate_weight_loss_as_wear_warning(): void
    {
        // 3.40g yerine 3.05g (-%10.3 sapma)
        $analysis = $this->diagnosticService->analyzePhysicalDeviations($this->referenceCoin, 3.05, 19.0);

        $this->assertEquals('warning', $analysis['status']);
        $this->assertEquals('warning', $analysis['level']);
        $this->assertGreaterThan(8.0, abs($analysis['weight_deviation_percent']));
        $this->assertLessThanOrEqual(15.0, abs($analysis['weight_deviation_percent']));
    }

    /**
     * Referans değerden %15'ten daha hafif olan numunelerin 'critical' sahte/döküm uyarısı aldığını test eder.
     */
    public function test_flags_severely_underweight_coin_as_critical_counterfeit_suspect(): void
    {
        // 3.40g yerine 2.40g (-%29.4 sapma - örn: kurşun/kalay dolgu veya aşırı traşlama)
        $analysis = $this->diagnosticService->analyzePhysicalDeviations($this->referenceCoin, 2.40, 19.0);

        $this->assertEquals('critical', $analysis['status']);
        $this->assertEquals('danger', $analysis['level']);
        $this->assertLessThan(-15.0, $analysis['weight_deviation_percent']);
        $this->assertStringContainsString('Sahtelik', $analysis['title']);
        $this->assertStringContainsString('döküm', strtolower($analysis['message']));
    }

    /**
     * Referans değerden %15'ten daha ağır olan numunelerin (modern kalın döküm kopyalar) 'critical' uyarı aldığını test eder.
     */
    public function test_flags_severely_overweight_coin_as_critical_cast_replica(): void
    {
        // 3.40g yerine 4.20g (+%23.5 sapma - kalıp alma modern döküm replika)
        $analysis = $this->diagnosticService->analyzePhysicalDeviations($this->referenceCoin, 4.20, 19.0);

        $this->assertEquals('critical', $analysis['status']);
        $this->assertEquals('danger', $analysis['level']);
        $this->assertGreaterThan(15.0, $analysis['weight_deviation_percent']);
        $this->assertStringContainsString('Sahtelik', $analysis['title']);
    }

    /**
     * Çap sapmasının tolerans dışına çıkması durumunu test eder.
     */
    public function test_flags_abnormal_diameter_deviation(): void
    {
        // Ağırlık normal (3.40g) ancak çap 19mm yerine 22mm (+%15.8 sapma)
        $analysis = $this->diagnosticService->analyzePhysicalDeviations($this->referenceCoin, 3.40, 22.0);

        $this->assertEquals('critical', $analysis['status']);
        $this->assertGreaterThan(10.0, abs($analysis['diameter_deviation_percent']));
    }
}

