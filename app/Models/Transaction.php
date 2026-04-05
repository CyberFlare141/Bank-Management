<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';
    public const TYPE_TRANSFER = 'transfer';
    public const TYPE_BILL_PAYMENT = 'bill_payment';
    public const TYPE_RECHARGE = 'recharge';
    public const TYPE_LOAN = 'loan';
    public const TYPE_FIXED_DEPOSIT = 'fixed_deposit';

    protected $primaryKey = 'T_ID';

    protected $fillable = [
        'A_Number',
        'C_ID',
        'T_Type',
        'T_Amount',
        'T_Date',
    ];

    protected function casts(): array
    {
        return [
            'T_Amount' => 'decimal:2',
            'T_Date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'A_Number', 'A_Number');
    }

    public function scopeForFilterType($query, ?string $type)
    {
        if (!$type) {
            return $query;
        }

        return match ($type) {
            self::TYPE_CREDIT => $query->where(function ($builder) {
                $builder->where('T_Type', 'like', 'Fund Transfer Received%')
                    ->orWhere('T_Type', 'like', 'Recharge Received%')
                    ->orWhere('T_Type', 'Loan Disbursement')
                    ->orWhere('T_Type', 'Fixed Deposit Maturity Payout')
                    ->orWhere('T_Type', 'Fixed Deposit Early Break Payout');
            }),
            self::TYPE_DEBIT => $query->where(function ($builder) {
                $builder->where('T_Type', 'like', 'Fund Transfer Sent%')
                    ->orWhere('T_Type', 'like', 'Bill Payment - %')
                    ->orWhere('T_Type', 'Loan Repayment')
                    ->orWhere('T_Type', 'Fixed Deposit Booking');
            }),
            self::TYPE_TRANSFER => $query->where('T_Type', 'like', 'Fund Transfer %'),
            self::TYPE_BILL_PAYMENT => $query->where('T_Type', 'like', 'Bill Payment - %'),
            self::TYPE_RECHARGE => $query->where('T_Type', 'like', 'Recharge %'),
            self::TYPE_LOAN => $query->where('T_Type', 'like', 'Loan %'),
            self::TYPE_FIXED_DEPOSIT => $query->where('T_Type', 'like', 'Fixed Deposit %'),
            default => $query->where('T_Type', $type),
        };
    }
}
