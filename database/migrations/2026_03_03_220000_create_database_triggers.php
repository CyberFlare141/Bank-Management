<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_transactions_after_insert');
        DB::unprepared('
            CREATE TRIGGER trg_transactions_after_insert
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                IF NEW.T_Type IN (\'transfer_in\', \'recharge_credit\', \'loan_disbursement\', \'loan_credit\') THEN
                    UPDATE accounts
                    SET A_Balance = A_Balance + NEW.T_Amount,
                        updated_at = NOW()
                    WHERE A_Number = NEW.A_Number;
                ELSEIF NEW.T_Type IN (\'transfer_out\', \'bill_payment\', \'loan_repayment\', \'debit\') THEN
                    UPDATE accounts
                    SET A_Balance = A_Balance - NEW.T_Amount,
                        updated_at = NOW()
                    WHERE A_Number = NEW.A_Number;
                END IF;
            END
        ');

        DB::unprepared('DROP TRIGGER IF EXISTS trg_loans_after_update');
        DB::unprepared('
            CREATE TRIGGER trg_loans_after_update
            AFTER UPDATE ON loans
            FOR EACH ROW
            BEGIN
                DECLARE target_account BIGINT;

                IF LOWER(NEW.status) = \'approved\' AND LOWER(OLD.status) <> \'approved\' THEN
                    SELECT A_Number INTO target_account
                    FROM accounts
                    WHERE C_ID = NEW.C_ID
                    ORDER BY A_Number ASC
                    LIMIT 1;

                    IF target_account IS NOT NULL THEN
                        INSERT INTO transactions (A_Number, C_ID, T_Type, T_Amount, T_Date, created_at, updated_at)
                        VALUES (target_account, NEW.C_ID, \'loan_disbursement\', NEW.L_Amount, NOW(), NOW(), NOW());
                    END IF;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_loans_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_transactions_after_insert');
    }
};
