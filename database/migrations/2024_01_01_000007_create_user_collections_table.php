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
        Schema::create('user_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('coin_id')->constrained('coins')->cascadeOnDelete();
            $table->enum('user_condition', ['Poor', 'Fine', 'VF', 'XF', 'UNC'])->default('VF')->comment('Kullanıcının elindeki kondisyon');
            $table->date('purchase_date')->nullable()->comment('Koleksiyona ekleme veya edinme tarihi');
            $table->text('notes')->nullable()->comment('Kullanıcının özel nümizmatik notları');
            $table->timestamps();

            $table->index(['user_id', 'coin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_collections');
    }
};

