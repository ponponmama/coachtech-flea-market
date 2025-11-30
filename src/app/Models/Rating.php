<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'rater_id',
        'rated_user_id',
        'rating',
        'comment',
    ];

    /**
     * 評価対象の商品を取得
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 評価するユーザーを取得
     */
    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    /**
     * 評価されるユーザーを取得
     */
    public function ratedUser()
    {
        return $this->belongsTo(User::class, 'rated_user_id');
    }
}
