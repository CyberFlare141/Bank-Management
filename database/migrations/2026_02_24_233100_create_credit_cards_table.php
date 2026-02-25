<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id('Card_ID');
            $table->unsignedBigInteger('C_ID')->unique();
            $table->string('card_number');
            $table->string('expiry_date', 7);
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->decimal('available_credit', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('C_ID')->references('C_ID')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_cards');
    }
};
