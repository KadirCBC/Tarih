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
        Schema::create('mint_error_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Hata adı (Off-Center Strike, Double Die, Brockage vb.)');
            $table->string('slug')->unique();
            $table->string('severity_impact')->comment('Fiyat çarpanı aralığı: 1.5x, 3x, 10x');
            $table->decimal('multiplier_min', 4, 2)->default(1.50);
            $table->decimal('multiplier_max', 4, 2)->default(3.00);
            $table->string('visual_guide_image')->nullable()->comment('Eğitim görseli / SVG illüstrasyonu');
            $table->text('explanation_tr')->comment('Detaylı Türkçe teknik ve nümizmatik açıklaması');
            $table->timestamps();
        });

        // Kullanıcı envanterine darp hatası alanları eklenmesi
        Schema::table('user_collections', function (Blueprint $table) {
            $table->boolean('has_mint_error')->default(false)->after('user_condition')->comment('Darp hatası onay kutusu');
            $table->foreignId('mint_error_type_id')->nullable()->after('has_mint_error')->constrained('mint_error_types')->nullOnDelete();
            $table->string('mint_error_notes')->nullable()->after('mint_error_type_id')->comment('Hatanın detayı: %30 sağa kayık, ayna brockage vb.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_collections', function (Blueprint $table) {
            $table->dropForeign(['mint_error_type_id']);
            $table->dropColumn(['has_mint_error', 'mint_error_type_id', 'mint_error_notes']);
        });

        Schema::dropIfExists('mint_error_types');
    }
};

