<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('ethnic_branch_id')->nullable()->after('monthly_fee')->constrained('ethnic_branches')->nullOnDelete();
            $table->foreignId('representative_id')->nullable()->after('ethnic_branch_id')->constrained('representatives')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ethnic_branch', 'representative_name']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ethnic_branch')->nullable()->after('monthly_fee');
            $table->string('representative_name')->nullable()->after('ethnic_branch');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('representative_id');
            $table->dropConstrainedForeignId('ethnic_branch_id');
        });
    }
};
