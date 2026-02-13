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
        Schema::create('customer_services', function (Blueprint $table) {
    $table->id('CS_ID');
    $table->unsignedBigInteger('C_ID');
    $table->unsignedBigInteger('S_ID');

    $table->foreign('C_ID')->references('C_ID')->on('customers');
    $table->foreign('S_ID')->references('S_ID')->on('services');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_services');
    }
};
