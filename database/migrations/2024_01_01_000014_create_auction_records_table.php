<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Uluslararası açık artırma evlerinin (Heritage, CNG, Leu, Gorny vb.)
     * kamuya açık satış ve çekiç fiyatı kayıtları.
     */
    public function up(): void
    {
        Schema::create('auction_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->constrained('coins')->cascadeOnDelete();
            $table->string('auction_house', 100)->comment('Heritage Auctions, CNG, Gorny & Mosch, Roma Numismatics vb.');
            $table->string('sale_name_or_number', 100)->comment('Müzayede no veya adı (Örn: Triton XXVII, Auction 540)');
            $table->string('lot_number', 50)->comment('Lot numarası (Örn: 245, Lot 1082)');
            $table->date('sale_date')->index()->comment('Açık artırma tarihi');
            $table->decimal('hammer_price_usd', 10, 2)->comment('Çekiç fiyatı (USD)');
            $table->decimal('realized_price_usd', 10, 2)->comment('Alıcı komisyonu (%15-20) dahil net gerçekleşen fiyat (USD)');
            $table->string('condition_grade', 20)->comment('Poor, Fine, VF, XF, UNC vb.');
            $table->string('source_url', 255)->nullable()->comment('Müzayede evindeki lot arşiv bağlantısı');
            $table->timestamps();

            // Konsolidasyon ve piyasa değeri hesaplama indeksleri
            $table->index(['coin_id', 'condition_grade', 'sale_date'], 'idx_coin_grade_date');
            $table->index(['auction_house', 'sale_name_or_number'], 'idx_house_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_records');
    }
};

