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
            $table->unsignedTinyInteger('two_factor_failed_attempts')
                ->default(0)
                ->after('two_factor_enabled');

            $table->timestamp('two_factor_locked_until')
                ->nullable()
                ->after('two_factor_failed_attempts');

            $table->timestamp('two_factor_last_sent_at')
                ->nullable()
                ->after('two_factor_locked_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_failed_attempts',
                'two_factor_locked_until',
                'two_factor_last_sent_at',
            ]);
        });
    }
};