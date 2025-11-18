<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenderMst extends Model
{
    use HasFactory;

    protected $table = 'gender_mst';

    protected $fillable = [
        'gender',
    ];

    /**
     * この性別を持つユーザー
     */
    public function users()
    {
        return $this->hasMany(User::class, 'gender_id');
    }
}
