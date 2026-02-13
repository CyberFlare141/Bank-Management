<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'C_ID';

    protected $fillable = [
        'C_Name',
        'C_Address',
        'C_PhoneNumber',
        'C_Email'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'C_ID');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'C_ID');
    }
}

