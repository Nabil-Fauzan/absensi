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
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_suspicious')->default(false)->after('approval_status');
            $table->string('spoof_reason')->nullable()->after('is_suspicious');
            $table->boolean('is_ip_fallback')->default(false)->after('spoof_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['is_suspicious', 'spoof_reason', 'is_ip_fallback']);
        });
    }
};
