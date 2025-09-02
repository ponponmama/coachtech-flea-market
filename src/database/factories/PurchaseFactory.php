<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
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
        $shippingMethods = [
            'ヤマト運輸', '佐川急便', '日本郵便', 'クロネコヤマト', 'ゆうパック',
            '面交', '手渡し', 'その他'
        ];

        $paymentMethods = [
            '現金', 'クレジットカード', '銀行振込', 'コンビニ決済', '電子マネー',
            'PayPay', 'LINE Pay', 'd払い', 'その他'
        ];

        $statuses = [
            '支払い待ち', '支払い完了', '発送準備中', '発送済み', '配送中',
            '配達完了', '取引完了', 'キャンセル', '返品・交換'
        ];

        return [
            'buyer_id' => User::factory(),
            'item_id' => Item::factory(),
            'purchase_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'shipping_method' => $this->faker->randomElement($shippingMethods),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'status' => $this->faker->randomElement($statuses),
            'shipping_cost' => $this->faker->optional(0.8)->randomElement([0, 300, 500, 800, 1000]),
            'total_amount' => function (array $attributes) {
                return $attributes['shipping_cost'] + $this->faker->numberBetween(100, 50000);
            },
            'tracking_number' => $this->faker->optional(0.8)->regexify('[A-Z]{2}[0-9]{9}[A-Z]{2}'),
            'notes' => $this->faker->optional(0.3)->sentence(),
            // 発送先住所は購入者のプロフィールから取得するため、
            // ここではファクトリーで生成せず、実際の購入時に
            // ユーザーのプロフィール情報を使用する
        ];
    }
}
