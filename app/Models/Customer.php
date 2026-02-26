<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $primaryKey = 'C_ID';

    protected $fillable = [
        'C_Name',
        'C_Address',
        'C_PhoneNumber',
        'C_Email'
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'C_ID');
    }

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'C_ID');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'C_ID');
    }

    public function loanRequests(): HasMany
    {
        return $this->hasMany(LoanRequest::class, 'C_ID');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'C_ID');
    }

    public function creditCard(): HasOne
    {
        return $this->hasOne(CreditCard::class, 'C_ID');
    }
}
