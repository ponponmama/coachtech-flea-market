<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'profile_image_path',
        'postal_code',
        'address',
        'building_name',
    ];

    /**
     * プロフィールの所有者を取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
