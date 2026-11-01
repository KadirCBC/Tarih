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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_expert')->default(false)->after('subscription_id')->comment('Doğrulanmış Nümizmat / Editör yetkisi');
            $table->string('expert_title')->nullable()->after('is_expert')->comment('Örn: Kıdemli Nümizmat, Arkeolog, Akademik Editör');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_expert', 'expert_title']);
        });
    }
};

