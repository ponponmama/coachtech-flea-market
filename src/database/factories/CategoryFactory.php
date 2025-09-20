<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = [
            'ファッション', '家電', 'インテリア', 'レディース', 'メンズ',
            'コスメ', '本・雑誌', 'ゲーム', 'スポーツ', 'キッチン',
            'ハンドメイド', 'アクセサリー', 'おもちゃ', 'ベビー・キッズ'
        ];

        return [
            'name' => $this->faker->randomElement($categories),
        ];
    }
}