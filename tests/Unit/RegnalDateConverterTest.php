<?php

namespace Tests\Unit;

use App\Services\RegnalDateConverterService;
use PHPUnit\Framework\TestCase;

class RegnalDateConverterTest extends TestCase
{
    protected RegnalDateConverterService $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new RegnalDateConverterService();
    }

    /**
     * "COS III TR P IIII" gibi Roma unvanlarının doğru miladi yıla çözümlendiğini test eder.
     */
    public function test_converts_trajan_cos_iii_tr_p_iiii_to_exact_ad_year(): void
    {
        $result = $this->converter->convertRoman(
            rulerSlug: 'trajan',
            rawTitles: 'COS III TR P IIII'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(100, $result['gregorian_start']);
        $this->assertEquals(100, $result['gregorian_end']);
        $this->assertEquals('M.S. 100', $result['formatted_date']);
        $this->assertNotEmpty($result['explanation']);
    }

    /**
     * Standart Romen rakamı IV ve aditif IIII yazımlarının ikisini de aynı şekilde parse ettiğini doğrular.
     */
    public function test_parses_both_subtractive_and_additive_roman_numerals(): void
    {
        $this->assertEquals(4, $this->converter->parseRomanNumeral('IV'));
        $this->assertEquals(4, $this->converter->parseRomanNumeral('IIII'));
        $this->assertEquals(9, $this->converter->parseRomanNumeral('IX'));
        $this->assertEquals(14, $this->converter->parseRomanNumeral('XIIII'));
        $this->assertEquals(14, $this->converter->parseRomanNumeral('XIV'));
    }

    /**
     * "TR P IX COS IIII" kombinasyonunun M.S. 104-105 aralığına denk geldiğini doğrular.
     */
    public function test_converts_trajan_tr_p_ix_to_ad_104_105(): void
    {
        $result = $this->converter->convertRoman(
            rulerSlug: 'trajan',
            rawTitles: 'TR P IX COS IIII'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(104, $result['gregorian_start']);
        $this->assertEquals(105, $result['gregorian_end']);
        $this->assertStringContainsString('104', $result['formatted_date']);
    }

    /**
     * Grek / Seleukos takvim yılından (Anno Seleuci - A.S.) miladi yıla çevrimi doğrular.
     */
    public function test_converts_seleucid_calendar_year_correctly(): void
    {
        // A.S. 112 -> M.Ö. 312 referansıyla M.Ö. 201 - 200 aralığı
        $result = $this->converter->convertSeleucid(112);

        $this->assertTrue($result['success']);
        $this->assertEquals(-201, $result['gregorian_start']);
        $this->assertEquals(-200, $result['gregorian_end']);
        $this->assertStringContainsString('M.Ö. 201 - 200', $result['formatted_date']);
    }

    /**
     * Osmanlı / İslami cülus hesaplamasını doğrular (Örn: II. Mahmud 1223 Cülus + 15. Sene = Miladi 1822).
     */
    public function test_converts_ottoman_regnal_year_correctly(): void
    {
        $result = $this->converter->convertIslamicOttoman(
            accessionHijri: 1223,
            regnalYear: 15,
            sultanSlug: 'mahmud-ii'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals(1821, $result['gregorian_start']);
        $this->assertEquals(1822, $result['gregorian_end']);
        $this->assertStringContainsString('1822', $result['formatted_date']);
    }

    /**
     * Bilinmeyen bir hükümdar sorgulandığında başarısız yanıt ve açıklayıcı hata mesajı döndüğünü doğrular.
     */
    public function test_returns_error_for_unknown_emperor(): void
    {
        $result = $this->converter->convertRoman('unknown-caesar-xyz');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Bilinmeyen Roma imparatoru', $result['error']);
    }
}
