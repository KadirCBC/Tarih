<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kişisel Nümizmatik Koleksiyon Sertifikası</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: a4 portrait;
        }

        body {
            font-family: 'DejaVu Serif', Georgia, serif;
            color: #1e293b;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-container {
            border-bottom: 2px solid #b45309;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-title {
            font-size: 18pt;
            font-weight: bold;
            color: #78350f;
            text-align: center;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }

        .header-subtitle {
            font-size: 9pt;
            text-align: center;
            color: #64748b;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .meta-table td {
            font-size: 8.5pt;
            padding: 4px 6px;
            vertical-align: top;
        }

        .badge-box {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
        }

        .badge-title {
            font-size: 8pt;
            font-weight: bold;
            color: #92400e;
            text-transform: uppercase;
        }

        .badge-value {
            font-size: 13pt;
            font-weight: bold;
            color: #78350f;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .legal-notice {
            background-color: #f8fafc;
            border-left: 3px solid #b45309;
            padding: 6px 10px;
            font-size: 7.5pt;
            color: #475569;
            margin-bottom: 15px;
        }

        .coin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .coin-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 4px;
            border: 1px solid #334155;
            text-align: left;
        }

        .coin-table td {
            font-size: 8pt;
            padding: 6px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .coin-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .coin-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid #cbd5e1;
        }

        .grade-badge {
            font-weight: bold;
            font-size: 8pt;
            padding: 2px 5px;
            border-radius: 3px;
            background-color: #e2e8f0;
            color: #1e293b;
            display: inline-block;
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Üst Başlık -->
    <div class="header-container">
        <h1 class="header-title">Nümizmatik Koleksiyon Envanter Sertifikası</h1>
        <div class="header-subtitle">Akademik Arşiv Kaydı & Portföy Ekspertiz Dökümü</div>
    </div>

    <!-- Belge ve Koleksiyoner Meta Bilgileri -->
    <table class="meta-table">
        <tr>
            <td width="50%">
                <strong>Koleksiyoner:</strong> {{ $user->name }}<br>
                <strong>E-Posta:</strong> {{ $user->email }}<br>
                <strong>Koleksiyon Kaydı:</strong> {{ $summary['total_coins'] }} Adet Sikke
            </td>
            <td width="50%" style="text-align: right;">
                <strong>Sertifika No:</strong> {{ $documentNumber }}<br>
                <strong>Tanzim Tarihi:</strong> {{ $issuedDate }}<br>
                <strong>Belge Türü:</strong> Kişisel Arşiv Dökümü
            </td>
        </tr>
    </table>

    <!-- 2863 Sayılı Kanun Yasal Şerhi -->
    <div class="legal-notice">
        <strong>YASAL BİLDİRİM (2863 Sayılı Kanun):</strong> Bu belge; kullanıcının şahsi nümizmatik veri tabanı ve akademik tanıtım envanter dökümüdür. Alım-satım veya ticari sertifika vasfı taşımaz. Kültür ve Tabiat Varlıklarını Koruma Kanunu uyarınca tarihi eserlerin izinsiz satışı veya yurtdışına çıkarılması yasaktır.
    </div>

    <!-- Toplam Değerleme Özeti -->
    <div class="badge-box">
        <div class="badge-title">Tahmini Toplam Müzayede Değer Bandı (USD)</div>
        <div class="badge-value">{{ $summary['price_range_formatted'] }}</div>
        <div style="font-size: 7.5pt; color: #78350f; margin-top: 3px;">
            * Değerler CNG, Heritage ve Gorny & Mosch uluslararası müzayede veritabanı referans aralıklarına göre hesaplanmıştır.
        </div>
    </div>

    <!-- Sikke Envanter Listesi Tablosu -->
    <table class="coin-table">
        <thead>
            <tr>
                <th width="8%">Görsel</th>
                <th width="32%">Sikke Tanımı & Hükümdar</th>
                <th width="15%">Katalog No</th>
                <th width="12%">Dönem / Darphane</th>
                <th width="9%">Maden</th>
                <th width="9%">Kondisyon</th>
                <th width="15%" style="text-align: right;">Değer Aralığı</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php
                    $coin = $item->coin;
                    $val = $item->estimated_valuation;
                @endphp
                <tr>
                    <td align="center">
                        @if($item->display_image_url)
                            <img src="{{ $item->display_image_url }}" class="coin-thumb" alt="Coin">
                        @else
                            <span style="font-size: 7pt; color: #94a3b8;">Görsel Yok</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $coin?->title }}</strong><br>
                        <span style="font-size: 7pt; color: #64748b;">
                            {{ $coin?->ruler?->name ? 'Hükümdar: ' . $coin->ruler->name . ' • ' : '' }}
                            {{ $coin?->denomination }}
                            {{ $coin?->weight_g ? ' (' . $coin->weight_g . 'g)' : '' }}
                        </span>
                        @if($item->notes)
                            <br><em style="font-size: 7pt; color: #0284c7;">Not: {{ $item->notes }}</em>
                        @endif
                    </td>
                    <td style="font-family: 'DejaVu Sans', monospace; font-size: 7.5pt;">
                        <strong>{{ $coin?->catalog_reference }}</strong>
                    </td>
                    <td style="font-size: 7pt;">
                        {{ $coin?->period?->name }}<br>
                        <span style="color: #64748b;">{{ $coin?->mint?->name ?? 'Darphane Belirtilmemiş' }}</span>
                    </td>
                    <td style="font-size: 7.5pt; text-transform: capitalize;">
                        {{ $coin?->metal?->value }}
                    </td>
                    <td align="center">
                        <span class="grade-badge">{{ $item->user_condition->value }}</span>
                    </td>
                    <td align="right" style="font-family: 'DejaVu Sans', monospace; font-weight: bold; font-size: 8pt; color: #78350f;">
                        @if($val)
                            ${{ number_format($val->min_value_usd, 0) }} - ${{ number_format($val->max_value_usd, 0) }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center" style="padding: 20px; color: #64748b;">
                        Koleksiyonunuzda henüz kayıtlı sikke bulunmamaktadır.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Alt Bilgi (Footer) -->
    <div class="footer">
        Belge Doğrulama Kodu: {{ $documentNumber }} | Antik Sikke Nümizmatik Bilgi ve Referans Kataloğu | Sayfa 1/1
    </div>

</body>
</html>

