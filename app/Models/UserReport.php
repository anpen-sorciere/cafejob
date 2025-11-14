<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;

    protected $table = 'user_reports';

    protected $fillable = [
        'application_id',
        'shop_admin_id',
        'user_id',
        'report_type',
        'message',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * 応募
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * 報告した店舗管理者
     */
    public function shopAdmin()
    {
        return $this->belongsTo(ShopAdmin::class, 'shop_admin_id');
    }

    /**
     * 報告された求職者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * レビューしたシステム管理者
     */
    public function reviewedBy()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}

