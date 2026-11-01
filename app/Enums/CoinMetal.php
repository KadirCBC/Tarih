<?php

namespace App\Enums;

enum CoinMetal: string
{
    case GOLD = 'gold';
    case SILVER = 'silver';
    case BRONZE = 'bronze';
    case BILLON = 'billon';
    case ELECTRUM = 'electrum';
    case COPPER = 'copper';

    public function label(): string
    {
        return match ($this) {
            self::GOLD => 'Altın (Gold / Aurum)',
            self::SILVER => 'Gümüş (Silver / Argentum)',
            self::BRONZE => 'Bronz (Bronze)',
            self::BILLON => 'Billon (Düşük Ayar Gümüş)',
            self::ELECTRUM => 'Elektrum (Doğal Altın-Gümüş Alaşımı)',
            self::COPPER => 'Bakır (Copper / Cuprum)',
        };
    }
}

