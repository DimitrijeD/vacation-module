<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationRequest extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'STATUS_PENDING';
    const STATUS_REJECTED = 'STATUS_REJECTED';
    const STATUS_APPROVED = 'STATUS_APPROVED';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REJECTED,
        self::STATUS_APPROVED,
    ];

    protected $table = 'vacation_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start',
        'end',
        'status',
        'working_days_duration'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
