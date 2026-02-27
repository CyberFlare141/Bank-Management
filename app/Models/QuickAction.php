<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickAction extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'C_ID',
        'A_Number',
        'transaction_id',
        'action_type',
        'channel',
        'recipient_identifier',
        'provider',
        'bill_type',
        'bill_number',
        'amount',
        'note',
        'status',
        'meta',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'meta' => 'array',
            'performed_at' => 'datetime',
        ];
    }
}
