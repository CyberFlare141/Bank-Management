<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $primaryKey = 'A_Number';

    protected $fillable = [
        'A_Number',
        'C_ID',
        'account_type',
        'A_Balance',
        'Operating_Date'
    ];

    protected function casts(): array
    {
        return [
            'A_Balance' => 'decimal:2',
            'Operating_Date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'A_Number', 'A_Number');
    }
}
