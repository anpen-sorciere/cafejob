<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserApplicationBan extends Model
{
    use HasFactory;

    protected $table = 'user_application_bans';

    protected $fillable = [
        'user_id',
        'shop_id',
        'user_report_id',
        'reason',
        'banned_until',
        'banned_by',
        'status',
        'revoked_by',
        'revoked_at',
    ];

    protected $casts = [
        'banned_until' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    /**
     * 禁止された求職者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 禁止した店舗
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * 関連する報告
     */
    public function userReport()
    {
        return $this->belongsTo(UserReport::class, 'user_report_id');
    }

    /**
     * 禁止した店舗管理者
     */
    public function bannedBy()
    {
        return $this->belongsTo(ShopAdmin::class, 'banned_by');
    }

    /**
     * 解除したシステム管理者
     */
    public function revokedBy()
    {
        return $this->belongsTo(Admin::class, 'revoked_by');
    }

    /**
     * 有効な禁止かどうか
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->banned_until->isFuture();
    }

    /**
     * 期限切れかどうか
     */
    public function isExpired()
    {
        return $this->banned_until->isPast();
    }
}

