<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Topluluk önerileri ve doğrulanmış nümizmat yorumları tablosu.
     */
    public function up(): void
    {
        Schema::create('identification_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('identification_request_id')->constrained('identification_requests')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('suggested_coin_id')->nullable()->constrained('coins')->nullOnDelete()->comment('Topluluğun önerdiği katalog sikkesi');
            $table->text('comment');
            $table->boolean('is_expert_verification')->default(false)->comment('Onaylayan kişinin uzman olup olmadığı');
            $table->timestamps();

            $table->index(['identification_request_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identification_comments');
    }
};

