<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS audit_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                customer_id BIGINT UNSIGNED NULL,
                account_number BIGINT UNSIGNED NULL,
                action_type VARCHAR(100) NOT NULL,
                entity_type VARCHAR(100) NULL,
                entity_id VARCHAR(100) NULL,
                status VARCHAR(30) NOT NULL,
                message VARCHAR(255) NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                request_payload LONGTEXT NULL,
                response_payload LONGTEXT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX audit_logs_user_id_index (user_id),
                INDEX audit_logs_customer_id_index (customer_id),
                INDEX audit_logs_account_number_index (account_number),
                INDEX audit_logs_action_type_index (action_type),
                INDEX audit_logs_status_index (status),
                INDEX audit_logs_created_at_index (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TABLE IF EXISTS audit_logs');
    }
};
