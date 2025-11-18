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
        'password', // ミューテータでpassword_hashに変換される
        'password_hash', // 直接設定する場合用
        'first_name',
        'last_name',
        'phone',
        'birth_date',
        'gender',
        'prefecture_id',
        'city_id',
        'address',
        'postal_code',
        'profile_image',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Laravelの認証システムとの互換性のため、passwordアクセサを追加
     */
    public function getPasswordAttribute()
    {
        return $this->password_hash;
    }

    /**
     * Laravelの認証システムとの互換性のため、passwordミューテータを追加
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = \Hash::make($value);
    }

    /**
     * Get the password for authentication.
     * Laravelの認証システムがpassword_hashカラムを使用できるようにする
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
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

