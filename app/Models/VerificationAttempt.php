<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'attempt_type',
        'verification_code',
        'input_code',
        'ip_address',
        'user_agent',
        'is_successful',
        'attempt_time',
    ];

    protected $casts = [
        'is_successful' => 'boolean',
        'attempt_time' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * 店舗
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

