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
        Schema::create('employees', function (Blueprint $table) {
    $table->id('E_ID');
    $table->string('E_Name');
    $table->string('E_Role')->nullable();
    $table->string('E_ContactNumber')->nullable();
    $table->unsignedBigInteger('B_ID');
    $table->foreign('B_ID')->references('B_ID')->on('branches');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
