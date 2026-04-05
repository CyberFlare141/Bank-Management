<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedDeposit extends Model
{
    protected $fillable = [
        'product_id',
        'C_ID',
        'A_Number',
        'principal_amount',
        'annual_interest_rate',
        'term_months',
        'started_at',
        'maturity_date',
        'projected_interest',
        'maturity_amount',
        'interest_paid',
        'penalty_amount',
        'payout_amount',
        'status',
        'closed_at',
        'closure_reason',
    ];

    protected function casts(): array
    {
        return [
            'principal_amount' => 'decimal:2',
            'annual_interest_rate' => 'decimal:4',
            'started_at' => 'date',
            'maturity_date' => 'date',
            'projected_interest' => 'decimal:2',
            'maturity_amount' => 'decimal:2',
            'interest_paid' => 'decimal:2',
            'penalty_amount' => 'decimal:2',
            'payout_amount' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(DepositProduct::class, 'product_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'A_Number', 'A_Number');
    }
}
