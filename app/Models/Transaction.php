<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
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
}
