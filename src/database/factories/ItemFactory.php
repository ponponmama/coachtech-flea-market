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

        return [
            'name' => $this->faker->words(2, true),
            'brand' => $this->faker->optional()->company(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 50000),
            'condition' => $this->faker->randomElement($conditions),
            'image_path' => null, // 後で実際の画像パスを設定
            'seller_id' => User::factory(),
            'buyer_id' => null,
            'sold_at' => null,
        ];
    }
}
