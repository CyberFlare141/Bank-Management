<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('deposit_products')->cascadeOnDelete();
            $table->unsignedBigInteger('C_ID');
            $table->unsignedBigInteger('A_Number');
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('annual_interest_rate', 8, 4);
            $table->unsignedInteger('term_months');
            $table->date('started_at');
            $table->date('maturity_date');
            $table->decimal('projected_interest', 15, 2)->default(0);
            $table->decimal('maturity_amount', 15, 2)->default(0);
            $table->decimal('interest_paid', 15, 2)->default(0);
            $table->decimal('penalty_amount', 15, 2)->default(0);
            $table->decimal('payout_amount', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamp('closed_at')->nullable();
            $table->string('closure_reason')->nullable();
            $table->timestamps();

            $table->foreign('C_ID')->references('C_ID')->on('customers')->cascadeOnDelete();
            $table->foreign('A_Number')->references('A_Number')->on('accounts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_deposits');
    }
};
