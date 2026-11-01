<?php

namespace App\Enums;

enum CatalogSystem: string
{
    case RIC = 'RIC';           // Roman Imperial Coinage (Harold Mattingly, Edward A. Sydenham)
    case BMC = 'BMC';           // Coins of the Roman Empire in the British Museum
    case SEAR = 'Sear';         // David R. Sear (Roman, Greek, Byzantine Coins and Their Values)
    case CRAWFORD = 'Crawford'; // Roman Republican Coinage (RRC) by Michael H. Crawford
    case COHEN = 'Cohen';       // Henry Cohen (Description historique des monnaies frappées sous l'Empire romain)
    case BABELON = 'Babelon';   // Ernest Babelon (Description historique et chronologique des monnaies de la république romaine)
    case SNG = 'SNG';           // Sylloge Nummorum Graecorum (SNG Cop, SNG ANS, SNG von Aulock)
    case PRICE = 'Price';       // Martin J. Price (The Coinage in the Name of Alexander the Great)
    case MULLER = 'Muller';     // Ludvig Müller (Numismatique d'Alexandre le Grand)
    case RSC = 'RSC';           // Roman Silver Coins (Seaby)
    case PERE = 'Pere';         // Nuri Pere (Osmanlılarda Madenî Paralar)
    case DAMALI = 'Damali';     // Atom Damalı (Osmanlı Sikkeleri Tarihi)
    case ARTUK = 'Artuk';       // İbrahim Artuk, Cevriye Artuk (İstanbul Arkeoloji Müzeleri İslami Sikkeler Kataloğu)

    public function label(): string
    {
        return match ($this) {
            self::RIC => 'RIC (Roman Imperial Coinage)',
            self::BMC => 'BMC (British Museum Catalog)',
            self::SEAR => 'Sear (David Sear Reference)',
            self::CRAWFORD => 'Crawford (Roman Republican Coinage)',
            self::COHEN => 'Cohen (Monnaies de l’Empire Romain)',
            self::BABELON => 'Babelon (Monnaies de la République)',
            self::SNG => 'SNG (Sylloge Nummorum Graecorum)',
            self::PRICE => 'Price (Alexander Coinage)',
            self::MULLER => 'Müller (Alexandre le Grand)',
            self::RSC => 'RSC (Roman Silver Coins)',
            self::PERE => 'Pere (Osmanlı Paraları Kataloğu)',
            self::DAMALI => 'Damalı (Osmanlı Sikkeleri Tarihi)',
            self::ARTUK => 'Artuk (İslami Sikkeler Kataloğu)',
        };
    }

    public function shortCode(): string
    {
        return $this->value;
    }
}

