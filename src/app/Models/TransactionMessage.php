<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'sender_id',
        'receiver_id',
        'message',
        'image_path',
        'is_read',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * メッセージが属する商品を取得
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 送信者を取得
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * 受信者を取得
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}