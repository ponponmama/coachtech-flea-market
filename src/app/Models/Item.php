<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'brand',
        'description',
        'price',
        'condition',
        'image_path',
        'seller_id',
        'buyer_id',
        'sold_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sold_at' => 'datetime',
    ];

    /**
     * 出品者を取得
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * 購入者を取得
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * 商品のコメントを取得
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 商品のいいねを取得
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * 商品のカテゴリを取得
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }
}