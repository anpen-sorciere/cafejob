<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'title',
        'description',
        'job_type',
        'salary_min',
        'salary_max',
        'work_hours',
        'requirements',
        'benefits',
        'job_conditions',
        'uniform_description',
        'uniform_images',
        'trial_visit_available',
        'gender_requirement',
        'age_min',
        'age_max',
        'status',
        'application_deadline',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'job_conditions' => 'array',
        'uniform_images' => 'array',
        'trial_visit_available' => 'boolean',
    ];

    /**
     * 店舗
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * 応募
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * お気に入りに追加しているユーザー
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'job_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * 応募数を取得
     */
    public function getApplicationCountAttribute()
    {
        return $this->applications()->count();
    }
}

