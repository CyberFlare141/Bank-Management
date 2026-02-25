<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditCard extends Model
{
    protected $primaryKey = 'Card_ID';

    protected $fillable = [
        'C_ID',
        'card_number',
        'expiry_date',
        'credit_limit',
        'available_credit',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'available_credit' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }

    protected function maskedCardNumber(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (!$this->card_number) {
                    return null;
                }

                $digits = preg_replace('/\D/', '', $this->card_number);

                if (!$digits || strlen($digits) < 4) {
                    return '****';
                }

                return '**** **** **** '.substr($digits, -4);
            },
        );
    }
}
