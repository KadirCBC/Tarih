<?php

namespace App\Enums;

enum RarityGrade: string
{
    case C = 'C';
    case R1 = 'R1';
    case R2 = 'R2';
    case R3 = 'R3';
    case R4 = 'R4';
    case R5 = 'R5';

    public function label(): string
    {
        return match ($this) {
            self::C => 'Yaygın (Common)',
            self::R1 => 'Nadir (Rare - R1)',
            self::R2 => 'Çok Nadir (Very Rare - R2)',
            self::R3 => 'Oldukça Nadir (Extremely Rare - R3)',
            self::R4 => 'Müzelik / İstisnai (Museum / Exceptionally Rare - R4)',
            self::R5 => 'Benzersiz / Bilinen Birkaç Adet (Unique / Highest Rarity - R5)',
        };
    }
}

