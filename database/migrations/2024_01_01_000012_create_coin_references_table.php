<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Çapraz Referans (Concordance) Tablosu:
     * Farklı uluslararası nümizmatik katalog sistemlerindeki kodları tek sikkeye bağlar.
     */
    public function up(): void
    {
        Schema::create('coin_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->constrained('coins')->cascadeOnDelete();
            $table->string('catalog_system', 50)->comment('RIC, BMC, Sear, Crawford, Cohen, Babelon, SNG vb.');
            $table->string('reference_number', 100)->comment('Örn: II 142, 1850, 335/1c, 8-AK-G1');
            $table->text('notes')->nullable()->comment('Cilt, levha (plate) veya özel katalog notu');
            $table->timestamps();

            // Hızlı arama ve birebir eşleme için bileşik indeks
            $table->index(['catalog_system', 'reference_number'], 'idx_concordance_system_ref');
            $table->index('reference_number', 'idx_reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_references');
    }
};

