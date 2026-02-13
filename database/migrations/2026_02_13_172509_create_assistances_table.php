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
        Schema::create('assistances', function (Blueprint $table) {
    $table->id('A_ID');
    $table->unsignedBigInteger('E_ID');
    $table->unsignedBigInteger('C_ID');
    $table->string('A_Type');
    $table->date('A_Date');

    $table->foreign('E_ID')->references('E_ID')->on('employees');
    $table->foreign('C_ID')->references('C_ID')->on('customers');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistances');
    }
};
