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

        // 既に購入済みの商品を除外（buyer_idがnullの商品のみを対象）
        $availableItems = $items->whereNull('buyer_id');

        if ($availableItems->isEmpty()) {
            $this->command->info('購入可能な商品がありません。');
            return;
        }

        // 一部のアイテムを購入済みにする（取引中の商品を残すため、一部のみ購入）
        $purchaseCount = min(3, $availableItems->count()); // 最大3個の購入（取引中の商品を残す）
        $randomItems = $availableItems->random($purchaseCount);

        foreach ($randomItems as $item) {
            $buyer = $users->where('id', '!=', $item->seller_id)->random(); // 出品者以外を購入者に

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