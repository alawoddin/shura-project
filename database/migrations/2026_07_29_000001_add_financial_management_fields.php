<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->after('is_monthly_fee');
        });

        Schema::table('receive_payments', function (Blueprint $table) {
            $table->string('transaction_type')->default('credit')->after('category_id');
            $table->string('review_status')->default('pending_review')->after('description');
        });

        Schema::table('member_financial_reports', function (Blueprint $table) {
            $table->foreignId('credit_id')->nullable()->after('member_id')->constrained('credits')->nullOnDelete();
        });

        DB::table('receive_payments')->update(['review_status' => 'reviewed']);
    }

    public function down(): void
    {
        Schema::table('member_financial_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('credit_id');
        });

        Schema::table('receive_payments', function (Blueprint $table) {
            $table->dropColumn(['transaction_type', 'review_status']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
