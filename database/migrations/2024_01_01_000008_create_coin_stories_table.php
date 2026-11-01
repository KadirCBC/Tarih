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
        Schema::create('coin_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->constrained('coins')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->comment('Google Discover ve kart önizlemesi için kanca metin');
            $table->longText('content')->comment('Haftanın sikkesi detaylı editoryal hikaye ve tarihsel arka plan');
            $table->string('cover_image')->nullable()->comment('Google Discover uyumlu büyük 1200px+ görsel');
            $table->boolean('is_coin_of_the_week')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['is_coin_of_the_week', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_stories');
    }
};

