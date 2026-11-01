<?php

namespace Tests\Feature;

use App\Rules\NoIllegalTradingRule;
use App\Services\LegalContentFilterService;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LegalWarningFilterTest extends TestCase
{
    protected LegalContentFilterService $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = app(LegalContentFilterService::class);
    }

    /**
     * "satılık", "fiyat ver", "define" gibi yasaklı kelimelerin tespit edildiğini doğrular.
     */
    public function test_detects_prohibited_trading_and_treasure_hunting_keywords(): void
    {
        $this->assertTrue($this->filter->containsProhibitedContent('Elimde bir sikke var, satılık ilgilenenler yazsın.'));
        $this->assertTrue($this->filter->containsProhibitedContent('Buna kaç para teklif ver veya fiyat ne kadar verirsin?'));
        $this->assertTrue($this->filter->containsProhibitedContent('Dün tarlada define bulduk dedektör ile çıkardık.'));
        $this->assertTrue($this->filter->containsProhibitedContent('Kaçak kazı sonucu küp içinde buldum.'));
    }

    /**
     * Türkçe karakter ve büyük/küçük harf varyasyonlarının yakalandığını doğrular.
     */
    public function test_detects_case_insensitive_and_turkish_char_variations(): void
    {
        $this->assertTrue($this->filter->containsProhibitedContent('ACİL SATILIK ROMA PARASI'));
        $this->assertTrue($this->filter->containsProhibitedContent('DEFİNECİLİK ve DEDEKTÖR grubu'));
        $this->assertTrue($this->filter->containsProhibitedContent('SATMAK istiyorum elden satış'));
    }

    /**
     * Meşru akademik ve bilimsel açıklamaların filtreden sorunsuz geçtiğini doğrular.
     */
    public function test_allows_legitimate_numismatic_and_academic_descriptions(): void
    {
        $this->assertFalse($this->filter->containsProhibitedContent('Bu sikke Roma İmparatoru Trajan dönemine ait bir gümüş Denarius örneğidir.'));
        $this->assertFalse($this->filter->containsProhibitedContent('British Museum kataloğunda RIC 142 referans numarasıyla kayıtlıdır.'));
        $this->assertFalse($this->filter->containsProhibitedContent('Avers lejantında IMP NERVA TRAIANVS AVG GER DAC yazıtı okunmaktadır.'));
    }

    /**
     * Laravel NoIllegalTradingRule doğrulama kuralının 2863 sayılı kanun uyarınca validation hatası ürettiğini denetler.
     */
    public function test_validation_rule_fails_with_prohibited_terms(): void
    {
        $data = ['notes' => 'Dedektör ile buldum, satılık arıyorum.'];
        $rules = ['notes' => ['required', new NoIllegalTradingRule()]];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $errors = $validator->errors()->get('notes');
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('2863 sayılı kanun', $errors[0]);
    }

    /**
     * Temiz nümizmatik içeriğin validasyonu başarıyla geçtiğini denetler.
     */
    public function test_validation_rule_passes_for_clean_content(): void
    {
        $data = ['notes' => 'Koleksiyonda yer alan VF kondisyondaki Antoninianus nümizmatik incelemesidir.'];
        $rules = ['notes' => ['required', new NoIllegalTradingRule()]];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }
}

