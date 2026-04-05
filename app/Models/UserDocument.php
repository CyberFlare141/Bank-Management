<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends Model
{
    protected $fillable = [
        'user_id',
        'account_type',
        'nid_or_birth_certificate',
        'photo',
        'job_id',
        'student_id',
        'electric_bill',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
