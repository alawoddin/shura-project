<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->foreignId('source_account_id')
                ->nullable()
                ->after('category_id')
                ->constrained('categories')
                ->nullOnDelete();
        });

        Schema::table('aids', function (Blueprint $table) {
            $table->foreignId('source_account_id')
                ->nullable()
                ->after('category_id')
                ->constrained('categories')
                ->nullOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('source_account_id')
                ->nullable()
                ->after('category_id')
                ->constrained('categories')
                ->nullOnDelete();
        });

        Schema::table('undepositeds', function (Blueprint $table) {
            $table->foreignId('target_account_id')
                ->nullable()
                ->after('status')
                ->constrained('categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_account_id');
        });

        Schema::table('aids', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_account_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_account_id');
        });

        Schema::table('undepositeds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_account_id');
        });
    }
};
