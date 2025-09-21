<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のユーザーとアイテムを取得
        $users = User::all();
        $items = Item::all();

        if ($users->isEmpty() || $items->isEmpty()) {
            $this->command->error('ユーザーまたはアイテムが見つかりません。先にUserSeeder、ItemSeederを実行してください。');
            return;
        }

        // 一部のアイテムを購入済みにする
        $purchaseCount = min(20, $items->count()); // 最大20個の購入
        $randomItems = $items->random($purchaseCount);

        foreach ($randomItems as $item) {
            $buyer = $users->random();

            Purchase::create([
                'user_id' => $buyer->id,
                'item_id' => $item->id,
                'payment_method' => $this->getRandomPaymentMethod(),
                'amount' => $item->price,
                'status' => 'completed',
                'purchased_at' => now()->subDays(rand(1, 365)),
            ]);

            // アイテムの購入者情報を更新
            $item->update([
                'buyer_id' => $buyer->id,
                'sold_at' => now()->subDays(rand(1, 365)),
            ]);
        }

        $this->command->info('購入データを作成しました。');
    }

    private function getRandomPaymentMethod()
    {
        $paymentMethods = [
            'credit', 'convenience'
        ];

        return $paymentMethods[array_rand($paymentMethods)];
    }
}
