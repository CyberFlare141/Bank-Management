<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_requests', 'request_type')) {
                $table->string('request_type')->default('loan_request')->after('requested_amount');
            }

            if (!Schema::hasColumn('loan_requests', 'target_loan_id')) {
                $table->unsignedBigInteger('target_loan_id')->nullable()->after('approved_loan_id');
                $table->foreign('target_loan_id')->references('L_ID')->on('loans');
            }
        });

        DB::table('loan_requests')
            ->whereNull('request_type')
            ->update(['request_type' => 'loan_request']);
    }

    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            if (Schema::hasColumn('loan_requests', 'target_loan_id')) {
                $table->dropForeign(['target_loan_id']);
                $table->dropColumn('target_loan_id');
            }

            if (Schema::hasColumn('loan_requests', 'request_type')) {
                $table->dropColumn('request_type');
            }
        });
    }
};
