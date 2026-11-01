<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('periods')->restrictOnDelete();
            $table->foreignId('ruler_id')->nullable()->constrained('rulers')->nullOnDelete();
            $table->foreignId('mint_id')->nullable()->constrained('mints')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('catalog_reference')->index()->comment('RIC, BMC, Crawford veya Sear no');
            $table->string('denomination')->comment('Denarius, Follis, Drahmi, Tetradrahmi, Akçe vb.');
            $table->enum('metal', ['gold', 'silver', 'bronze', 'billon', 'electrum', 'copper']);
            $table->decimal('weight_g', 5, 2)->nullable()->comment('Gram cinsinden ağırlık');
            $table->decimal('diameter_mm', 4, 1)->nullable()->comment('Milimetre cinsinden çap');
            $table->text('obverse_desc')->nullable()->comment('Ön yüz tasvir açıklaması');
            $table->string('obverse_legend')->nullable()->comment('Ön yüz orijinal sikke yazısı/lejant');
            $table->text('obverse_legend_trans')->nullable()->comment('Ön yüz lejant çözümlemesi ve Türkçe çevirisi');
            $table->text('reverse_desc')->nullable()->comment('Arka yüz tasvir açıklaması');
            $table->string('reverse_legend')->nullable()->comment('Arka yüz orijinal sikke yazısı/lejant');
            $table->text('reverse_legend_trans')->nullable()->comment('Arka yüz lejant çözümlemesi ve Türkçe çevirisi');
            $table->enum('rarity_grade', ['C', 'R1', 'R2', 'R3', 'R4', 'R5'])->default('C')->comment('Nadirlik derecesi');
            $table->longText('historical_notes')->nullable()->comment('Tarihsel ve nümizmatik açıklamalar');
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();

            // Sık filtrelenen kombinasyonlar için indeksler
            $table->index(['period_id', 'metal']);
            $table->index(['ruler_id', 'denomination']);
            $table->index(['rarity_grade']);

            // Fulltext Arama İndeksi: başlık, lejantlar ve katalog numarası (MySQL / MariaDB)
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->fullText(
                    ['title', 'obverse_legend', 'reverse_legend', 'catalog_reference'],
                    'coins_fulltext_search'
                );
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins');
    }
};

