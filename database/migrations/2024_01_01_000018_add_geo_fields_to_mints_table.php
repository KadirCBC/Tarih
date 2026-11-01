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
        Schema::table('mints', function (Blueprint $table) {
            $table->string('ancient_name')->nullable()->after('name')->comment('Antik dnemdeki tam ad (rn: Ephesos / Arsinoeia)');
            $table->string('modern_name')->nullable()->after('ancient_name')->comment('Gnmzdeki ehir/blge (rn: Seluk, zmir, Trkiye)');
            $table->string('active_period')->nullable()->after('modern_name')->comment('Aktif darp yaplan yzyllar (rn: M.. 6. yy - M.S. 3. yy)');
            $table->text('historical_significance')->nullable()->after('active_period')->comment('Darphanenin nmizmatik ve ticari nemi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mints', function (Blueprint $table) {
            $table->dropColumn(['ancient_name', 'modern_name', 'active_period', 'historical_significance']);
        });
    }
};

