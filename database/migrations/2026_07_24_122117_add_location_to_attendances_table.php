<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('latitude_in')->nullable()->after('notes');
            $table->string('longitude_in')->nullable()->after('latitude_in');
            $table->string('latitude_out')->nullable()->after('longitude_in');
            $table->string('longitude_out')->nullable()->after('latitude_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['latitude_in', 'longitude_in', 'latitude_out', 'longitude_out']);
        });
    }
};
