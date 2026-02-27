<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardApplication extends Model
{
    protected $table = 'card_applications';

    protected $primaryKey = 'App_ID';

    protected $fillable = [
        'C_ID',
        'B_ID',
        'application_id',
        'card_category',
        'card_network',
        'card_design',
        'delivery_method',
        'full_name',
        'date_of_birth',
        'national_id_passport',
        'contact_number',
        'email_address',
        'residential_address',
        'existing_account_number',
        'account_type',
        'branch_name',
        'occupation',
        'employer_name',
        'monthly_income',
        'source_of_income',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'monthly_income' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'C_ID');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'B_ID');
    }
}
