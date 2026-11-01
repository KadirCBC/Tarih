<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique()->comment('USD, EUR, TRY, GBP vb.');
            $table->string('name')->comment('Amerikan Doları, Euro, Türk Lirası');
            $table->string('symbol', 10)->comment('$, €, ₺, £');
            $table->decimal('rate_to_eur', 14, 6)->comment('1 EUR karşılığı döviz tutarı');
            $table->decimal('rate_to_usd', 14, 6)->comment('1 USD karşılığı döviz tutarı');
            $table->boolean('is_active')->default(true);
            $table->timestamp('fetched_at')->nullable()->comment('ECB tarafından son güncellenme zamanı');
            $table->timestamps();
        });

        // Varsayılan kurları ekle
        DB::table('currency_rates')->insert([
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'rate_to_eur' => 1.085000,
                'rate_to_usd' => 1.000000,
                'is_active' => true,
                'fetched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'rate_to_eur' => 1.000000,
                'rate_to_usd' => 0.921659, // 1 / 1.085
                'is_active' => true,
                'fetched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TRY',
                'name' => 'Turkish Lira',
                'symbol' => '₺',
                'rate_to_eur' => 38.500000,
                'rate_to_usd' => 35.483870, // 38.50 / 1.085
                'is_active' => true,
                'fetched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'rate_to_eur' => 0.855000,
                'rate_to_usd' => 0.788018,
                'is_active' => true,
                'fetched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};

