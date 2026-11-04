<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            // deposit = دریافت پول به حساب, withdraw = ارسال پول از حساب, transfer = انتقال بین دو حساب
            $table->enum('type', ['deposit', 'withdraw', 'transfer']);
            $table->foreignId('from_account_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('party')->nullable(); // شخص / منبع ارسال‌کننده یا دریافت‌کننده
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
