<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id('LR_ID');
            $table->unsignedBigInteger('C_ID');
            $table->unsignedBigInteger('B_ID');
            $table->decimal('requested_amount', 15, 2);
            $table->string('status')->default('processing');
            $table->string('decision_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('approved_loan_id')->nullable();
            $table->timestamps();

            $table->foreign('C_ID')->references('C_ID')->on('customers');
            $table->foreign('B_ID')->references('B_ID')->on('branches');
            $table->foreign('approved_loan_id')->references('L_ID')->on('loans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_requests');
    }
};
