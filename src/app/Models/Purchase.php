<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'profile_id',
        'purchased_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    /**
     * 購入者を取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 購入した商品を取得
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 配送先プロフィールを取得
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
