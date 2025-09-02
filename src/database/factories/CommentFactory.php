<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $commentTemplates = [
            'とても良い商品ですね！',
            '状態はどうですか？',
            '送料は込みですか？',
            '面交可能ですか？',
            'サイズは何センチですか？',
            '色は何色ですか？',
            '使用年数はどのくらいですか？',
            '傷や汚れはありますか？',
            '箱や説明書は付属していますか？',
            '値下げ交渉可能ですか？',
            '素敵な商品ですね！',
            'お気に入りです！',
            '購入を検討中です。',
            '詳細を教えてください。',
            '写真を追加で撮ってもらえますか？',
            '発送方法は何がありますか？',
            '支払い方法は何がありますか？',
            '即購入希望です！',
            'この商品について詳しく知りたいです。',
            '他の商品も出品されていますか？'
        ];

        return [
            'content' => $this->faker->randomElement($commentTemplates),
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
        ];
    }
}



