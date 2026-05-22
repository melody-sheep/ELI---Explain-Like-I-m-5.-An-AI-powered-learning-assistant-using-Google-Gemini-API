<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('time_limit_per_question')->default(30)->after('settings');
            $table->integer('time_spent_total')->default(0)->after('time_limit_per_question');
            $table->decimal('last_score', 5, 2)->nullable()->after('time_spent_total');
            $table->integer('times_taken')->default(0)->after('last_score');
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->text('user_answer')->nullable()->after('correct_answer');
            $table->boolean('is_correct')->nullable()->after('user_answer');
            $table->integer('time_spent')->default(0)->after('is_correct');
        });
    }

    public function down()
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn([
                'time_limit_per_question',
                'time_spent_total',
                'last_score',
                'times_taken'
            ]);
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn([
                'user_answer',
                'is_correct',
                'time_spent'
            ]);
        });
    }
};