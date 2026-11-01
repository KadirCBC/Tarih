<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class FetchEcbCurrencyRatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:fetch-currency-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Avrupa Merkez Bankası (ECB) günlük döviz kurlarını çekerek USD, EUR ve TRY değerleme oranlarını günceller.';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyService $currencyService): int
    {
        $this->info("Avrupa Merkez Bankası (ECB) döviz kuru beslemesi sorgulanıyor...");

        $rawRates = $currencyService->fetchRatesFromEcb();

        if (empty($rawRates)) {
            $this->error("Kurlar ECB sunucusundan çekilemedi. Önbellekteki veya veritabanındaki son kurlar korunuyor.");
            return Command::FAILURE;
        }

        $cachedRates = $currencyService->getCachedRates();

        $rows = [];
        foreach ($cachedRates as $currency => $info) {
            $rows[] = [
                $info['code'],
                $info['name'],
                $info['symbol'],
                number_format($info['rate_to_usd'], 4),
                number_format($info['rate_to_eur'], 4),
            ];
        }

        $this->newLine();
        $this->info("✓ Döviz kurları başarıyla güncellendi:");
        $this->table(
            ['Kod', 'Para Birimi', 'Sembol', '1 USD Değeri', '1 EUR Değeri'],
            $rows
        );

        return Command::SUCCESS;
    }
}

