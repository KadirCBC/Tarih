<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasMultilingualFields
{
    /**
     * Desteklenen diller listesi.
     * @var array<string, string>
     */
    public static array $supportedLocales = [
        'tr' => 'Türkçe',
        'en' => 'English',
        'de' => 'Deutsch',
    ];

    /**
     * Belirtilen alanın geçerli dildeki (veya fallback dildeki) çevirisini döndürür.
     *
     * @param string $attribute  Örn: 'title', 'obverse_legend_trans', 'historical_notes'
     * @param string|null $locale 'tr', 'en', 'de' veya null (aktif locale)
     * @return string
     */
    public function getTranslated(string $attribute, ?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();
        $translationsField = $attribute . '_translations';

        // 1. JSON Translations alanını kontrol et
        $translations = $this->getAttribute($translationsField);
        if (is_string($translations)) {
            $translations = json_decode($translations, true) ?: [];
        }

        if (is_array($translations) && !empty($translations[$locale])) {
            return (string) $translations[$locale];
        }

        // 2. Fallback dil kontrolü ('tr' veya 'en')
        if ($locale !== 'tr' && !empty($translations['tr'])) {
            return (string) $translations['tr'];
        }

        if ($locale !== 'en' && !empty($translations['en'])) {
            return (string) $translations['en'];
        }

        // 3. Ana sütun değerini fallback olarak döndür
        $fallbackValue = $this->getAttribute($attribute);
        return is_string($fallbackValue) ? $fallbackValue : '';
    }

    /**
     * Belirtilen alan için tüm dillerdeki çevirileri dizi olarak döndürür.
     *
     * @param string $attribute
     * @return array<string, string>
     */
    public function getTranslations(string $attribute): array
    {
        $translationsField = $attribute . '_translations';
        $translations = $this->getAttribute($translationsField);

        if (is_string($translations)) {
            $translations = json_decode($translations, true) ?: [];
        }

        if (!is_array($translations)) {
            $translations = [];
        }

        // Eğer 'tr' boşsa ve ana sütunda değer varsa ekle
        if (empty($translations['tr']) && !empty($this->getAttribute($attribute))) {
            $translations['tr'] = (string) $this->getAttribute($attribute);
        }

        return $translations;
    }

    /**
     * Belirli bir dile ait çeviriyi ayarlar.
     *
     * @param string $attribute
     * @param string $locale
     * @param string $value
     * @return self
     */
    public function setTranslation(string $attribute, string $locale, string $value): self
    {
        $translationsField = $attribute . '_translations';
        $translations = $this->getTranslations($attribute);
        $translations[$locale] = trim($value);

        $this->setAttribute($translationsField, $translations);

        // Varsayılan dil ise ana sütunu da güncelle
        if ($locale === 'tr' || empty($this->getAttribute($attribute))) {
            $this->setAttribute($attribute, trim($value));
        }

        return $this;
    }

    /**
     * Tüm çevirileri tek seferde ayarlar.
     *
     * @param string $attribute
     * @param array<string, string> $translations ['tr' => '...', 'en' => '...', 'de' => '...']
     * @return self
     */
    public function setTranslations(string $attribute, array $translations): self
    {
        $translationsField = $attribute . '_translations';
        $this->setAttribute($translationsField, $translations);

        if (!empty($translations['tr'])) {
            $this->setAttribute($attribute, $translations['tr']);
        } elseif (!empty($translations['en'])) {
            $this->setAttribute($attribute, $translations['en']);
        }

        return $this;
    }

    // Dinamik erişim sağlayıcıları (Accessors)

    public function getTranslatedTitleAttribute(): string
    {
        return $this->getTranslated('title');
    }

    public function getTranslatedObverseDescAttribute(): string
    {
        return $this->getTranslated('obverse_desc');
    }

    public function getTranslatedObverseLegendTransAttribute(): string
    {
        return $this->getTranslated('obverse_legend_trans');
    }

    public function getTranslatedReverseDescAttribute(): string
    {
        return $this->getTranslated('reverse_desc');
    }

    public function getTranslatedReverseLegendTransAttribute(): string
    {
        return $this->getTranslated('reverse_legend_trans');
    }

    public function getTranslatedHistoricalNotesAttribute(): string
    {
        return $this->getTranslated('historical_notes');
    }
}

