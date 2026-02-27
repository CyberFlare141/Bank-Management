<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quick_actions', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 40)->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('C_ID');
            $table->unsignedBigInteger('A_Number');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('action_type', 30);
            $table->string('channel', 30)->nullable();
            $table->string('recipient_identifier')->nullable();
            $table->string('provider', 50)->nullable();
            $table->string('bill_type', 50)->nullable();
            $table->string('bill_number', 80)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('note', 255)->nullable();
            $table->string('status', 20)->default('success');
            $table->json('meta')->nullable();
            $table->timestamp('performed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('C_ID')->references('C_ID')->on('customers')->cascadeOnDelete();
            $table->foreign('A_Number')->references('A_Number')->on('accounts')->cascadeOnDelete();
            $table->foreign('transaction_id')->references('T_ID')->on('transactions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_actions');
    }
};
