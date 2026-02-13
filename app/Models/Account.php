<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $primaryKey = 'A_Number';

    protected $fillable = [
        'C_ID',
        'A_Balance',
        'Operating_Date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }
}

