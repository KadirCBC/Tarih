<?php

namespace App\Services;

use App\Models\Coin;

class AcademicLinkedDataService
{
    /**
     * Nomisma.org Denomination ID Eşleştirmeleri
     */
    protected array $denominationMap = [
        'denarius' => 'http://nomisma.org/id/denarius',
        'denar' => 'http://nomisma.org/id/denarius',
        'sestertius' => 'http://nomisma.org/id/sestertius',
        'sesterce' => 'http://nomisma.org/id/sestertius',
        'aureus' => 'http://nomisma.org/id/aureus',
        'antoninianus' => 'http://nomisma.org/id/antoninianus',
        'solidus' => 'http://nomisma.org/id/solidus',
        'semissis' => 'http://nomisma.org/id/semissis',
        'tremissis' => 'http://nomisma.org/id/tremissis',
        'follis' => 'http://nomisma.org/id/follis',
        'tetradrachm' => 'http://nomisma.org/id/tetradrachm',
        'tetradrahmi' => 'http://nomisma.org/id/tetradrachm',
        'drachm' => 'http://nomisma.org/id/drachma',
        'drahmi' => 'http://nomisma.org/id/drachma',
        'obol' => 'http://nomisma.org/id/obol',
        'stater' => 'http://nomisma.org/id/stater',
        'as' => 'http://nomisma.org/id/as',
        'dupondius' => 'http://nomisma.org/id/dupondius',
        'akce' => 'http://nomisma.org/id/akce',
        'sultani' => 'http://nomisma.org/id/sultani',
        'dirham' => 'http://nomisma.org/id/dirham',
        'dirhem' => 'http://nomisma.org/id/dirham',
    ];

    /**
     * Nomisma.org Metal (Material) ID Eşleştirmeleri
     */
    protected array $metalMap = [
        'gold' => 'http://nomisma.org/id/av',
        'altın' => 'http://nomisma.org/id/av',
        'silver' => 'http://nomisma.org/id/ar',
        'gümüş' => 'http://nomisma.org/id/ar',
        'bronze' => 'http://nomisma.org/id/ae',
        'bronz' => 'http://nomisma.org/id/ae',
        'copper' => 'http://nomisma.org/id/ae',
        'bakır' => 'http://nomisma.org/id/ae',
        'billon' => 'http://nomisma.org/id/billon',
        'bilyon' => 'http://nomisma.org/id/billon',
        'electrum' => 'http://nomisma.org/id/el',
        'elektrum' => 'http://nomisma.org/id/el',
    ];

    /**
     * Nomisma.org Darphane (Mint) ID Eşleştirmeleri
     */
    protected array $mintMap = [
        'roma' => 'http://nomisma.org/id/rome',
        'rome' => 'http://nomisma.org/id/rome',
        'konstantinopolis' => 'http://nomisma.org/id/constantinople',
        'constantinople' => 'http://nomisma.org/id/constantinople',
        'antiochia' => 'http://nomisma.org/id/antioch',
        'antioch' => 'http://nomisma.org/id/antioch',
        'efes' => 'http://nomisma.org/id/ephesus',
        'ephesos' => 'http://nomisma.org/id/ephesus',
        'alexandria' => 'http://nomisma.org/id/alexandria',
        'iskenderiye' => 'http://nomisma.org/id/alexandria',
        'siscia' => 'http://nomisma.org/id/siscia',
        'athens' => 'http://nomisma.org/id/athens',
        'atina' => 'http://nomisma.org/id/athens',
    ];

    /**
     * Nomisma.org Hükümdar / Otorite ID Eşleştirmeleri
     */
    protected array $rulerMap = [
        'trajan' => 'http://nomisma.org/id/trajan',
        'hadrian' => 'http://nomisma.org/id/hadrian',
        'augustus' => 'http://nomisma.org/id/augustus',
        'tiberius' => 'http://nomisma.org/id/tiberius',
        'nero' => 'http://nomisma.org/id/nero',
        'vespasian' => 'http://nomisma.org/id/vespasian',
        'marcus-aurelius' => 'http://nomisma.org/id/marcus_aurelius',
        'constantine' => 'http://nomisma.org/id/constantine_i',
        'alexander' => 'http://nomisma.org/id/alexander_iii',
    ];

    /**
     * Nomisma.org Ontolojisine ve W3C Linked Open Data Standartlarına
     * Uygun JSON-LD Yapısal Verisi Üretir.
     */
    public function generateNomismaJsonLd(Coin $coin): array
    {
        $coinUrl = route('coins.show', $coin->slug);
        $metalKey = strtolower($coin->metal?->value ?? (string)$coin->metal);
        $denomKey = strtolower(trim((string)$coin->denomination));
        $mintSlug = strtolower(trim((string)($coin->mint?->slug ?? '')));
        $rulerSlug = strtolower(trim((string)($coin->ruler?->slug ?? '')));

        $nomismaDenomination = $this->denominationMap[$denomKey] ?? "http://nomisma.org/id/{$denomKey}";
        $nomismaMaterial = $this->metalMap[$metalKey] ?? 'http://nomisma.org/id/ar';
        $nomismaMint = $this->mintMap[$mintSlug] ?? ($coin->mint ? "http://nomisma.org/id/{$mintSlug}" : null);
        $nomismaAuthority = $this->rulerMap[$rulerSlug] ?? ($coin->ruler ? "http://nomisma.org/id/{$rulerSlug}" : null);

        $primaryImage = $coin->primaryImage?->url ?? asset('images/coin-placeholder.webp');

        $jsonLd = [
            '@context' => [
                'nmo' => 'http://nomisma.org/ontology#',
                'crm' => 'http://www.cidoc-crm.org/cidoc-crm/',
                'skos' => 'http://www.w3.org/2004/02/skos/core#',
                'dcterms' => 'http://purl.org/dc/terms/',
                'geo' => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
                'xsd' => 'http://www.w3.org/2001/XMLSchema#',
                'foaf' => 'http://xmlns.com/foaf/0.1/',
            ],
            '@id' => $coinUrl,
            '@type' => ['nmo:TypeSeriesItem', 'crm:E22_Human-Made_Object'],
            'dcterms:title' => $coin->title,
            'dcterms:identifier' => $coin->catalog_reference,
            'dcterms:publisher' => 'Tarih Nümizmatik Araştırma ve Referans Platformu',
            'nmo:hasDenomination' => [
                '@id' => $nomismaDenomination,
                'skos:prefLabel' => $coin->denomination,
            ],
            'nmo:hasMaterial' => [
                '@id' => $nomismaMaterial,
                'skos:prefLabel' => ucfirst($metalKey),
            ],
        ];

        if ($nomismaAuthority) {
            $jsonLd['nmo:hasAuthority'] = [
                '@id' => $nomismaAuthority,
                'skos:prefLabel' => $coin->ruler?->name,
            ];
        }

        if ($nomismaMint) {
            $jsonLd['nmo:hasMint'] = [
                '@id' => $nomismaMint,
                'skos:prefLabel' => $coin->mint?->name,
            ];
            if ($coin->mint?->latitude && $coin->mint?->longitude) {
                $jsonLd['nmo:hasMint']['geo:lat'] = (float)$coin->mint->latitude;
                $jsonLd['nmo:hasMint']['geo:long'] = (float)$coin->mint->longitude;
            }
        }

        if ($coin->weight_g) {
            $jsonLd['nmo:hasWeight'] = [
                '@value' => (float)$coin->weight_g,
                '@type' => 'xsd:decimal',
                'qudt:unit' => 'http://qudt.org/vocab/unit/GM',
            ];
        }

        if ($coin->diameter_mm) {
            $jsonLd['nmo:hasDiameter'] = [
                '@value' => (float)$coin->diameter_mm,
                '@type' => 'xsd:decimal',
                'qudt:unit' => 'http://qudt.org/vocab/unit/MilliM',
            ];
        }

        // Ön Yüz (Obverse)
        $jsonLd['nmo:hasObverse'] = [
            '@type' => 'nmo:Obverse',
            'nmo:hasLegend' => $coin->obverse_legend,
            'dcterms:description' => $coin->obverse_desc,
        ];

        // Arka Yüz (Reverse)
        $jsonLd['nmo:hasReverse'] = [
            '@type' => 'nmo:Reverse',
            'nmo:hasLegend' => $coin->reverse_legend,
            'dcterms:description' => $coin->reverse_desc,
        ];

        // IIIF Manifest Referansı
        if ($coin->primaryImage) {
            $jsonLd['foaf:depiction'] = [
                '@id' => $primaryImage,
                'dcterms:isReferencedBy' => url("/api/iiif/image/{$coin->primaryImage->id}/info.json"),
            ];
        }

        return $jsonLd;
    }

    /**
     * Standart BibTeX Akademik Alıntı Metni Üretir.
     */
    public function generateBibTeX(Coin $coin): string
    {
        $idKey = 'tarih_' . str_replace('-', '_', $coin->slug);
        $title = addslashes($coin->title);
        $catalog = addslashes($coin->catalog_reference);
        $denom = addslashes($coin->denomination);
        $metal = addslashes($coin->metal?->value ?? (string)$coin->metal);
        $mint = addslashes($coin->mint?->name ?? 'Bilinmiyor');
        $ruler = addslashes($coin->ruler?->name ?? 'Antik Otorite');
        $url = route('coins.show', $coin->slug);
        $year = date('Y');

        return <<<BIBTEX
@misc{{$idKey},
  author       = {{Tarih Nümizmatik Araştırma Platformu}},
  title        = {{{$title}}},
  howpublished = {\\url{{$url}}},
  year         = {{$year}},
  note         = {Katalog Ref: {$catalog}, Hükümdar: {$ruler}, Darphane: {$mint}, Birim: {$denom}, Maden: {$metal}. Nomisma.org ontoloji uyumlu akademik veri.}
}
BIBTEX;
    }

    /**
     * Standart APA (7. Baskı) Akademik Alıntı Metni Üretir.
     */
    public function generateAPA(Coin $coin): string
    {
        $year = date('Y');
        $catalog = $coin->catalog_reference;
        $title = $coin->title;
        $url = route('coins.show', $coin->slug);
        $ruler = $coin->ruler?->name ? "{$coin->ruler->name}, " : '';

        return "Tarih Nümizmatik Araştırma Platformu. ({$year}). {$title} [{$catalog}] ({$ruler}{$coin->denomination}). Nümizmatik Dijital Kütüphanesi. {$url}";
    }
}

