<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];

        $itemNames = [
            '腕時計', 'バッグ', '靴', '洋服', '帽子', 'アクセサリー', 'スマートフォン',
            'ノートPC', 'タブレット', 'カメラ', 'ゲーム機', '本', 'CD', 'DVD',
            '家具', '家電', 'キッチン用品', 'スポーツ用品', '楽器', 'おもちゃ',
            'コスメ', '香水', '時計', '財布', 'ベルト', 'ネクタイ', 'スカーフ'
        ];

        $brands = [
            'ユニクロ', 'ZARA', 'H&M', 'GU', '無印良品', 'ニトリ', 'IKEA',
            'Apple', 'Samsung', 'Sony', 'Panasonic', 'シャープ', '東芝',
            'Nike', 'Adidas', 'Puma', 'Converse', 'Vans', 'New Balance',
            'ルイ・ヴィトン', 'シャネル', 'グッチ', 'プラダ', 'エルメス'
        ];

        $descriptions = [
            'とても良い状態です。お気に入りの一品でした。',
            '新品同様の美品です。',
            '少し使用感がありますが、まだまだ使えます。',
            '状態良好です。大切に使ってきました。',
            '目立った傷や汚れはありません。',
            'やや傷や汚れがありますが、機能は問題ありません。',
            '状態は悪いですが、まだ使えると思います。',
            '思い出の品です。大切に扱ってください。',
            '高級感のある一品です。',
            'シンプルで使いやすいデザインです。',
            'トレンドの商品です。',
            'クラシックなデザインで長く使えます。',
            'コンパクトで持ち運びしやすいです。',
            '高品質な素材を使用しています。',
            'デザイン性と機能性を兼ね備えています。'
        ];

        return [
            'name' => $this->faker->randomElement($itemNames),
            'brand' => $this->faker->optional(0.7)->randomElement($brands),
            'description' => $this->faker->randomElement($descriptions),
            'price' => $this->faker->numberBetween(100, 50000),
            'condition' => $this->faker->randomElement($conditions),
            'image_path' => null, // 後で実際の画像パスを設定
            'seller_id' => User::factory(),
            'buyer_id' => null,
            'sold_at' => null,
        ];
    }
}
