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

    /**
     * 発送先住所を取得
     */
    public function getShippingAddressAttribute()
    {
        if ($this->profile) {
            return [
                'postal_code' => $this->profile->postal_code,
                'address' => $this->profile->address,
                'building_name' => $this->profile->building_name,
            ];
        }

        // プロフィールがない場合は購入者のプロフィールから取得
        if ($this->user && $this->user->profile) {
            return [
                'postal_code' => $this->user->profile->postal_code,
                'address' => $this->user->profile->address,
                'building_name' => $this->user->profile->building_name,
            ];
        }

        return null;
    }

    /**
     * 発送先住所の完全な文字列を取得
     */
    public function getFullShippingAddressAttribute()
    {
        $address = $this->shipping_address;

        if (!$address) {
            return '住所が設定されていません';
        }

        $fullAddress = $address['address'];
        if ($address['building_name']) {
            $fullAddress .= ' ' . $address['building_name'];
        }

        return $fullAddress;
    }
}
