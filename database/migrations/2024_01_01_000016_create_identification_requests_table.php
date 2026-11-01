<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sikke Kimlikleme Kulübü:
     * Kullanıcıların ellerindeki paranın türünü öğrenmek için topluluğa ve uzmanlara danıştığı moderasyonlu talep tablosu.
     */
    public function up(): void
    {
        Schema::create('identification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('obverse_image');
            $table->string('reverse_image')->nullable();
            $table->decimal('weight_g', 5, 2)->nullable()->comment('Ölçülen ağırlık (gram)');
            $table->decimal('diameter_mm', 4, 1)->nullable()->comment('Ölçülen çap (mm)');
            $table->enum('status', ['pending', 'identified', 'disputed', 'rejected'])->default('pending')->index();
            $table->foreignId('identified_coin_id')->nullable()->constrained('coins')->nullOnDelete();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Onaylayan uzman nümizmat');
            $table->text('expert_notes')->nullable()->comment('Uzmanın kesinleşen tipolojik kararı');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identification_requests');
    }
};

