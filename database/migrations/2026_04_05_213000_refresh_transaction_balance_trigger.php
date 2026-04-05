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
                IF NEW.T_Type IN (\'transfer_in\', \'recharge_credit\', \'loan_disbursement\', \'loan_credit\')
                    OR NEW.T_Type LIKE \'Fund Transfer Received%\'
                    OR NEW.T_Type LIKE \'Recharge Received%\'
                    OR NEW.T_Type = \'Loan Disbursement\'
                    OR NEW.T_Type = \'Fixed Deposit Maturity Payout\'
                    OR NEW.T_Type = \'Fixed Deposit Early Break Payout\'
                THEN
                    UPDATE accounts
                    SET A_Balance = A_Balance + NEW.T_Amount,
                        updated_at = NOW()
                    WHERE A_Number = NEW.A_Number;
                ELSEIF NEW.T_Type IN (\'transfer_out\', \'bill_payment\', \'loan_repayment\', \'debit\')
                    OR NEW.T_Type LIKE \'Fund Transfer Sent%\'
                    OR NEW.T_Type LIKE \'Bill Payment - %\'
                    OR NEW.T_Type = \'Loan Repayment\'
                    OR NEW.T_Type = \'Fixed Deposit Booking\'
                THEN
                    UPDATE accounts
                    SET A_Balance = A_Balance - NEW.T_Amount,
                        updated_at = NOW()
                    WHERE A_Number = NEW.A_Number;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_transactions_after_insert');
        DB::unprepared('
            CREATE TRIGGER trg_transactions_after_insert
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                IF NEW.T_Type IN (\'transfer_in\', \'recharge_credit\', \'loan_disbursement\', \'loan_credit\')
                THEN
                    UPDATE accounts
                    SET A_Balance = A_Balance + NEW.T_Amount,
                        updated_at = NOW()
                    WHERE A_Number = NEW.A_Number;
                ELSEIF NEW.T_Type IN (\'transfer_out\', \'bill_payment\', \'loan_repayment\', \'debit\')
                THEN
                    UPDATE accounts
                    SET A_Balance = A_Balance - NEW.T_Amount,
                        updated_at = NOW()
                    WHERE A_Number = NEW.A_Number;
                END IF;
            END
        ');
    }
};
