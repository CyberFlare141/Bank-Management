<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'account_type')) {
                $table->string('account_type')->nullable()->after('C_ID');
            }
        });

        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'remaining_amount')) {
                $table->decimal('remaining_amount', 15, 2)->nullable()->after('L_Amount');
            }

            if (!Schema::hasColumn('loans', 'status')) {
                $table->string('status')->default('active')->after('Interest_Rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'account_type')) {
                $table->dropColumn('account_type');
            }
        });

        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'remaining_amount')) {
                $table->dropColumn('remaining_amount');
            }

            if (Schema::hasColumn('loans', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
