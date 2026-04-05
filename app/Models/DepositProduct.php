<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepositProduct extends Model
{
    protected $fillable = [
        'name',
        'product_code',
        'product_type',
        'minimum_amount',
        'maximum_amount',
        'term_months',
        'annual_interest_rate',
        'allow_early_break',
        'early_break_penalty_rate',
        'status',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'minimum_amount' => 'decimal:2',
            'maximum_amount' => 'decimal:2',
            'annual_interest_rate' => 'decimal:4',
            'allow_early_break' => 'boolean',
            'early_break_penalty_rate' => 'decimal:4',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fixedDeposits(): HasMany
    {
        return $this->hasMany(FixedDeposit::class, 'product_id');
    }
}
