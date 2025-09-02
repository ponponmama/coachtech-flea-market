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

    /**
     * 発送用の住所情報を取得
     */
    public function getShippingInfoAttribute()
    {
        return [
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'building_name' => $this->building_name,
        ];
    }

    /**
     * 完全な住所文字列を取得
     */
    public function getFullAddressAttribute()
    {
        $fullAddress = $this->address;
        if ($this->building_name) {
            $fullAddress .= ' ' . $this->building_name;
        }
        return $fullAddress;
    }

    /**
     * 郵便番号付きの完全な住所を取得
     */
    public function getCompleteAddressAttribute()
    {
        $address = $this->postal_code ? '〒' . $this->postal_code . ' ' : '';
        $address .= $this->full_address;
        return $address;
    }
}