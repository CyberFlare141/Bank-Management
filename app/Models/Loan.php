<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $primaryKey = 'L_ID';

    protected $fillable = [
        'C_ID',
        'B_ID',
        'L_Type',
        'L_Amount',
        'Interest_Rate',
        'remaining_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'L_Amount' => 'decimal:2',
            'Interest_Rate' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }
}
