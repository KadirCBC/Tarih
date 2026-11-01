<?php

namespace App\Enums;

enum ConditionGrade: string
{
    case POOR = 'Poor';
    case FINE = 'Fine';
    case VF = 'VF';
    case XF = 'XF';
    case UNC = 'UNC';

    public function label(): string
    {
        return match ($this) {
            self::POOR => 'Kötü / Yıpranmış (Poor - P/G)',
            self::FINE => 'İyi (Fine - F)',
            self::VF => 'Çok İyi (Very Fine - VF)',
            self::XF => 'Son Derece İyi (Extremely Fine - XF/EF)',
            self::UNC => 'Çil / Darphaneden Çıktığı Gibi (Uncirculated - UNC)',
        };
    }
}

