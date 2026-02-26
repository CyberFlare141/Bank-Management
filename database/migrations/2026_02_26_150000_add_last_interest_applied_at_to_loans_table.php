<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'last_interest_applied_at')) {
                $table->timestamp('last_interest_applied_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'last_interest_applied_at')) {
                $table->dropColumn('last_interest_applied_at');
            }
        });
    }
};
