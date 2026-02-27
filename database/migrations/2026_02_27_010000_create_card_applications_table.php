<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_applications', function (Blueprint $table) {
            $table->id('App_ID');
            $table->unsignedBigInteger('C_ID');
            $table->unsignedBigInteger('B_ID')->nullable();
            $table->string('application_id')->unique();
            $table->string('card_category', 20); // debit | credit
            $table->string('card_network', 30);
            $table->string('card_design', 100)->nullable();
            $table->string('delivery_method', 30);

            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('national_id_passport', 60);
            $table->string('contact_number', 30);
            $table->string('email_address');
            $table->text('residential_address');

            $table->unsignedBigInteger('existing_account_number')->nullable();
            $table->string('account_type', 100)->nullable();
            $table->string('branch_name')->nullable();

            $table->string('occupation')->nullable();
            $table->string('employer_name')->nullable();
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->string('source_of_income')->nullable();

            $table->string('status', 30)->default('pending_review');
            $table->timestamps();

            $table->foreign('C_ID')->references('C_ID')->on('customers')->onDelete('cascade');
            $table->foreign('B_ID')->references('B_ID')->on('branches')->nullOnDelete();
            $table->foreign('existing_account_number')->references('A_Number')->on('accounts')->nullOnDelete();
            $table->index(['C_ID', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_applications');
    }
};
