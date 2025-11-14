<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * 市区町村
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }

    /**
     * 店舗
     */
    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}

