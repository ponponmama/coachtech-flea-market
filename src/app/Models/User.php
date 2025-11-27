<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Purchase;
use App\Models\TransactionMessage;
use App\Mail\VerifyEmailCustom;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_first_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_first_login' => 'boolean',
    ];

    /**
     * ユーザーのプロフィールを取得
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * ユーザーが出品した商品を取得
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    /**
     * ユーザーが購入した商品を取得
     */
    public function purchasedItems()
    {
        return $this->hasMany(Item::class, 'buyer_id');
    }

    /**
     * ユーザーのコメントを取得
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * ユーザーのいいねを取得
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * ユーザーの購入履歴を取得
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * ユーザーがいいねした商品を取得
     */
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id');
    }

    /**
     * ユーザーが送信した取引メッセージを取得
     */
    public function sentMessages()
    {
        return $this->hasMany(TransactionMessage::class, 'sender_id');
    }

    /**
     * ユーザーが受信した取引メッセージを取得
     */
    public function receivedMessages()
    {
        return $this->hasMany(TransactionMessage::class, 'receiver_id');
    }

    /**
     * カスタムメール認証メールを送信
     */
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );
        Mail::to($this->email)->send(new VerifyEmailCustom($this, $verificationUrl));
    }
}
