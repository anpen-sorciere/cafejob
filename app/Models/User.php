<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'birth_date',
        'gender',
        'profile_image_1',
        'profile_image_2',
        'status',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    /**
     * ユーザーの応募
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * ユーザーのお気に入り
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * キープしている求人
     */
    public function favoriteJobs()
    {
        return $this->belongsToMany(Job::class, 'favorites', 'user_id', 'job_id')
            ->withTimestamps();
    }

    /**
     * キープしている店舗
     */
    public function favoriteShops()
    {
        return $this->belongsToMany(Shop::class, 'favorites', 'user_id', 'shop_id')
            ->withTimestamps();
    }
}

