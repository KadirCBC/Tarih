<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sikke Varyantları Tablosu:
     * Aynı temel tipin küçük lejant kırılmaları, darphane monogramı veya portre varyasyonları.
     */
    public function up(): void
    {
        Schema::create('coin_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->constrained('coins')->cascadeOnDelete();
            $table->string('variant_code', 50)->comment('Örn: Var. A, RIC 142a, Monogram 1');
            $table->text('obverse_diff')->nullable()->comment('Ön yüzdeki tipolojik veya lejant farkı');
            $table->text('reverse_diff')->nullable()->comment('Arka yüzdeki lejant kırılması, monogram veya işaret farkı');
            $table->string('rarity_shift', 50)->nullable()->comment('Nadirlik derecesi farkı (Örn: C -> R2)');
            $table->timestamps();

            $table->index(['coin_id', 'variant_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_variants');
    }
};

