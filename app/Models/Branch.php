<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $primaryKey = 'B_ID';

    protected $fillable = [
        'B_Name',
        'B_Location',
        'IFSC_Code',
    ];
}
