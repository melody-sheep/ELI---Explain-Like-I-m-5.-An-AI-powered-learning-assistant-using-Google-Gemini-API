<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'tags')) {
                $table->json('tags')->nullable();
            }
            if (!Schema::hasColumn('conversations', 'understood')) {
                $table->boolean('understood')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['tags', 'understood']);
        });
    }
};