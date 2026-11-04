<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receive_payments', function (Blueprint $table) {
            // When the payment is a loan repayment (دریافت قرض), it is linked to the loan it repays.
            $table->foreignId('credit_id')->nullable()->after('category_id')->constrained('credits')->nullOnDelete();
            $table->foreignId('credit_payment_id')->nullable()->after('credit_id')->constrained('credit_payments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('receive_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('credit_payment_id');
            $table->dropConstrainedForeignId('credit_id');
        });
    }
};
