<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'C_Email', 'email');
    }

    public function account(): HasOneThrough
    {
        return $this->hasOneThrough(
            Account::class,
            Customer::class,
            'C_Email',
            'C_ID',
            'email',
            'C_ID'
        );
    }

    public function loans(): HasManyThrough
    {
        return $this->hasManyThrough(
            Loan::class,
            Customer::class,
            'C_Email',
            'C_ID',
            'email',
            'C_ID'
        );
    }

    public function creditCard(): HasOneThrough
    {
        return $this->hasOneThrough(
            CreditCard::class,
            Customer::class,
            'C_Email',
            'C_ID',
            'email',
            'C_ID'
        );
    }

    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class,
            Customer::class,
            'C_Email',
            'C_ID',
            'email',
            'C_ID'
        );
    }

    public function cardApplications(): HasManyThrough
    {
        return $this->hasManyThrough(
            CardApplication::class,
            Customer::class,
            'C_Email',
            'C_ID',
            'email',
            'C_ID'
        );
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
