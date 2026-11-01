<?php

namespace App\Enums;

enum ImageType: string
{
    case OBVERSE = 'obverse';
    case REVERSE = 'reverse';
    case BOTH = 'both';
    case SLAB = 'slab';

    public function label(): string
    {
        return match ($this) {
            self::OBVERSE => 'Ön Yüz (Avers)',
            self::REVERSE => 'Arka Yüz (Revers)',
            self::BOTH => 'Ön ve Arka Yüz Bir Arada (Avers & Revers)',
            self::SLAB => 'Sertifikalı Kapsül (Graded Slab - NGC/PCGS)',
        };
    }
}

