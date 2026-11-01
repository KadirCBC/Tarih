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
        Schema::create('coin_valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->constrained('coins')->cascadeOnDelete();
            $table->enum('condition_grade', ['Poor', 'Fine', 'VF', 'XF', 'UNC'])->comment('Kondisyon derecesi');
            $table->decimal('min_value_usd', 10, 2)->comment('Müzayede taban referans değeri (USD)');
            $table->decimal('max_value_usd', 10, 2)->comment('Müzayede tavan referans değeri (USD)');
            $table->date('last_updated_at')->comment('Fiyat referansının son güncellenme tarihi');
            $table->timestamps();

            $table->index(['coin_id', 'condition_grade']);
            $table->index('last_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_valuations');
    }
};

