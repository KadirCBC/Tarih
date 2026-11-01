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
            $table->string('subscription_tier')->default('free')->index()->comment('free | pro');
            $table->timestamp('subscription_expires_at')->nullable()->index();
            $table->string('subscription_provider')->nullable()->comment('apple | google | paddle | iyzico');
            $table->string('subscription_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_tier',
                'subscription_expires_at',
                'subscription_provider',
                'subscription_id',
            ]);
        });
    }
};

