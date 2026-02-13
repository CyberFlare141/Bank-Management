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
   Schema::create('accounts', function (Blueprint $table) {
    $table->id('A_Number');
    $table->unsignedBigInteger('C_ID');
    $table->decimal('A_Balance', 15, 2)->default(0);
    $table->date('Operating_Date')->nullable();

    $table->foreign('C_ID')->references('C_ID')->on('customers');

    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
