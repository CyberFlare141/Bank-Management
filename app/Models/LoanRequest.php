<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRequest extends Model
{
    protected $primaryKey = 'LR_ID';

    protected $fillable = [
        'C_ID',
        'B_ID',
        'requested_amount',
        'status',
        'decision_note',
        'processed_at',
        'approved_loan_id',
    ];

    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'approved_loan_id', 'L_ID');
    }
}
