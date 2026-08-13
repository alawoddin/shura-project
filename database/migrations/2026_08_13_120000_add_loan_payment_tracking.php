<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->timestamp('last_payment_at')->nullable()->after('status');
        });

        Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->constrained('credits')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_payments');

        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('last_payment_at');
        });
    }
};
