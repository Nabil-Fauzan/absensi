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
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null')->after('role');
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null')->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['users_branch_id_foreign']);
            $table->dropForeign(['users_shift_id_foreign']);
            $table->dropColumn(['branch_id', 'shift_id']);
        });
    }
};
