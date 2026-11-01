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
        Schema::create('rulers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('periods')->cascadeOnDelete();
            $table->string('name');
            $table->string('title')->nullable()->comment('İmparator, Augustus, Sezar, Kral, Sultan vb.');
            $table->string('slug')->unique();
            $table->integer('reign_start')->nullable()->comment('Hüküm başlangıcı (M.Ö. için negatif)');
            $table->integer('reign_end')->nullable()->comment('Hüküm bitişi (M.Ö. için negatif)');
            $table->text('biography')->nullable();
            $table->timestamps();

            $table->index(['period_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rulers');
    }
};

