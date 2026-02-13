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
       Schema::create('transactions', function (Blueprint $table) {
    $table->id('T_ID');
    $table->unsignedBigInteger('A_Number');
    $table->unsignedBigInteger('C_ID');
    $table->string('T_Type');
    $table->decimal('T_Amount', 15, 2);
    $table->timestamp('T_Date')->useCurrent();

    $table->foreign('A_Number')->references('A_Number')->on('accounts');
    $table->foreign('C_ID')->references('C_ID')->on('customers');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
