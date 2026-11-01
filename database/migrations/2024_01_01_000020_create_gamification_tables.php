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
        // 1. Sınav ve Günlük Bilgi Soruları Tablosu
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coin_id')->nullable()->constrained('coins')->nullOnDelete();
            $table->enum('question_type', ['which_emperor', 'guess_metal', 'real_or_fake', 'correct_translation']);
            $table->text('question_text');
            $table->json('options_json');
            $table->unsignedSmallInteger('correct_option_index');
            $table->text('explanation')->nullable();
            $table->boolean('is_daily_challenge')->default(false);
            $table->date('challenge_date')->nullable()->index();
            $table->timestamps();
        });

        // 2. Kullanıcı Başarı Rozetleri (Gamification Badges)
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('badge_type', ['novice_collector', 'roman_expert', 'legend_reader', 'keen_eye']);
            $table->timestamp('unlocked_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'badge_type']);
        });

        // 3. Kullanıcı Günlük Seri ve Nümizmatik Puan Tablosu
        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('current_streak_days')->default(0);
            $table->unsignedInteger('longest_streak_days')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamps();
        });

        // Kullanıcı tablosuna puan alanı eklenmesi
        if (!Schema::hasColumn('users', 'numismatic_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('numismatic_points')->default(0)->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'numismatic_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('numismatic_points');
            });
        }

        Schema::dropIfExists('user_streaks');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('quiz_questions');
    }
};

