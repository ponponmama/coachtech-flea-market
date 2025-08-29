<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'category_id',
    ];

    /**
     * 関連する商品を取得
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 関連するカテゴリを取得
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}