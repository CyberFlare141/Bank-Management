<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('product_code')->unique();
            $table->string('product_type')->default('fixed_deposit');
            $table->decimal('minimum_amount', 15, 2);
            $table->decimal('maximum_amount', 15, 2)->nullable();
            $table->unsignedInteger('term_months');
            $table->decimal('annual_interest_rate', 8, 4);
            $table->boolean('allow_early_break')->default(true);
            $table->decimal('early_break_penalty_rate', 8, 4)->default(0);
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_products');
    }
};
