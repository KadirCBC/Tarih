<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sikke başlıkları, tasvirleri, lejant çevirileri ve tarihi notları
     * çok dilli (JSON - tr, en, de) formata uygun hale getirir.
     */
    public function up(): void
    {
        Schema::table('coins', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('obverse_desc_translations')->nullable()->after('obverse_desc');
            $table->json('obverse_legend_trans_translations')->nullable()->after('obverse_legend_trans');
            $table->json('reverse_desc_translations')->nullable()->after('reverse_desc');
            $table->json('reverse_legend_trans_translations')->nullable()->after('reverse_legend_trans');
            $table->json('historical_notes_translations')->nullable()->after('historical_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coins', function (Blueprint $table) {
            $table->dropColumn([
                'title_translations',
                'obverse_desc_translations',
                'obverse_legend_trans_translations',
                'reverse_desc_translations',
                'reverse_legend_trans_translations',
                'historical_notes_translations',
            ]);
        });
    }
};

