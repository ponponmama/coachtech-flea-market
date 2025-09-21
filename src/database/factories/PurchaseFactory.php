<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Purchase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $paymentMethods = [
            'credit', 'convenience'
        ];

        return [
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'status' => 'completed',
            'purchased_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            // user_id, item_id, amountはSeederで指定（item->priceを使用）
        ];
    }
}
