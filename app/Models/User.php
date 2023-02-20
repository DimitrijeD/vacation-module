<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_USER = 'ROLE_USER';
    const ROLE_MANAGER = 'ROLE_MANAGER';

    const ROLES = [
        self::ROLE_USER,
        self::ROLE_MANAGER,
    ];

    const DEFAULT_NUM_VACATION_DAYS = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'available_vacation_days',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pending_vacation_request()
    {
        return $this->hasOne(VacationRequest::class )->where('status', VacationRequest::STATUS_PENDING);
    }

    public function all_vacation_requests()
    {
        return $this->hasMany(VacationRequest::class );
    }
}
