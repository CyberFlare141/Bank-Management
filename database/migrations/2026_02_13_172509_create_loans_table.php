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
   Schema::create('loans', function (Blueprint $table) {
    $table->id('L_ID');
    $table->unsignedBigInteger('C_ID');
    $table->unsignedBigInteger('B_ID');
    $table->string('L_Type');
    $table->decimal('L_Amount', 15, 2);
    $table->decimal('Interest_Rate', 5, 2);

    $table->foreign('C_ID')->references('C_ID')->on('customers');
    $table->foreign('B_ID')->references('B_ID')->on('branches');
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
