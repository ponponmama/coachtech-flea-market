<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のユーザーを取得
        $users = \App\Models\User::all();

        if ($users->isEmpty()) {
            $this->command->error('ユーザーが見つかりません。先にUserSeederを実行してください。');
            return;
        }

        $itemsData = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'product-images/watch_1.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'product-images/hdd_2.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'product-images/onion_3.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'product-images/shoes_4.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image_path' => 'product-images/laptop_5.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'product-images/mic_6.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'product-images/bag_7.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'image_path' => 'product-images/tumbler_8.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'product-images/coffee_grinder_9.jpg',
                'condition' => '良好',
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image_path' => 'product-images/makeup_10.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($itemsData as $index => $itemData) {
            $isSold = $index < 3;

            Item::create(array_merge($itemData, [
                'seller_id' => $users->random()->id,
                'buyer_id' => $isSold ? $users->random()->id : null,
                'sold_at' => $isSold ? now()->subDays(rand(1, 30)) : null,
            ]));
        }

        // Factoryを使ってテスト用の商品を追加作成
        // 出品者: test@01.com (ID: 1)
        // 購入者: test@02.com (ID: 2)
        $seller01 = User::where('email', 'test@01.com')->first(); // 出品者
        $buyer02 = User::where('email', 'test@02.com')->first(); // 購入者

        if ($seller01 && $buyer02) {
            // 取引中の商品を3つ作成（出品者: test@01.com、購入希望者: test@02.com）
            for ($j = 0; $j < 3; $j++) {
                Item::factory()
                    ->trading() // 取引中（buyer_idがnull）
                    ->create([
                        'seller_id' => $seller01->id, // 出品者ID: 1
                    ]);
            }

            // 取引中の商品を3つ作成（出品者: test@02.com、購入希望者: test@01.com）
            for ($j = 0; $j < 3; $j++) {
                Item::factory()
                    ->trading() // 取引中（buyer_idがnull）
                    ->create([
                        'seller_id' => $buyer02->id, // 出品者ID: 2
                    ]);
            }

            // 決済処理済みの商品を3つ作成（test@01.comが出品、test@02.comが購入）
            // 評価機能のテスト用に決済処理済みにする
            for ($j = 0; $j < 3; $j++) {
                Item::factory()
                    ->create([
                        'seller_id' => $seller01->id, // 出品者ID: 1
                        'buyer_id' => $buyer02->id, // 購入者ID: 2（決済完了済み）
                        'sold_at' => now()->subDays(rand(1, 30)), // 1-30日前に購入
                    ]);
            }

            // 決済処理済みの商品を3つ作成（test@02.comが出品、test@01.comが購入）
            // 評価機能のテスト用に決済処理済みにする
            for ($j = 0; $j < 3; $j++) {
                Item::factory()
                    ->create([
                        'seller_id' => $buyer02->id, // 出品者ID: 2
                        'buyer_id' => $seller01->id, // 購入者ID: 1（決済完了済み）
                        'sold_at' => now()->subDays(rand(1, 30)), // 1-30日前に購入
                    ]);
            }

            // 購入済みの商品を3つ作成（test@01.comが出品、test@02.comが購入）
            for ($j = 0; $j < 3; $j++) {
                Item::factory()
                    ->create([
                        'seller_id' => $seller01->id, // 出品者ID: 1
                        'buyer_id' => $buyer02->id, // 購入者ID: 2（決済完了済み）
                        'sold_at' => now()->subDays(rand(1, 30)), // 1-30日前に購入
                    ]);
            }

            // test@02.comが出品、test@01.comが購入した商品を3つ作成
            for ($j = 0; $j < 3; $j++) {
                Item::factory()
                    ->create([
                        'seller_id' => $buyer02->id, // 出品者ID: 2
                        'buyer_id' => $seller01->id, // 購入者ID: 1（test@01.comが購入）
                        'sold_at' => now()->subDays(rand(1, 30)), // 1-30日前に購入
                    ]);
            }

            // test@03.com〜test@10.comのユーザーが購入した商品を作成（評価用）
            $allUsers = User::all();
            for ($i = 3; $i <= 10; $i++) {
                $buyerEmail = 'test@' . str_pad($i, 2, '0', STR_PAD_LEFT) . '.com';
                $buyer = User::where('email', $buyerEmail)->first();

                if ($buyer) {
                    // 各購入者に対して、ランダムな出品者を選択（自分以外）
                    $seller = $allUsers->where('id', '!=', $buyer->id)->random();

                    // 購入済みの商品を1つ作成
                    Item::factory()
                        ->create([
                            'seller_id' => $seller->id,
                            'buyer_id' => $buyer->id,
                            'sold_at' => now()->subDays(rand(1, 30)),
                        ]);
                }
            }

            $this->command->info('3件の取引中の商品を作成しました（test@01.comが出品）。');
            $this->command->info('3件の購入済み商品を作成しました（test@01.comが出品、test@02.comが購入）。');
            $this->command->info('3件の購入済み商品を作成しました（test@02.comが出品、test@01.comが購入）。');
            $this->command->info('test@03.com〜test@10.comのユーザーが購入した商品を作成しました。');
            $this->command->info('出品者: test@01.com (ID: ' . $seller01->id . ')');
            $this->command->info('購入者: test@02.com (ID: ' . $buyer02->id . ')');
        } else {
            $this->command->warn('test@01.com または test@02.com のユーザーが見つかりません。');
        }

        $this->command->info(count($itemsData) . '件のアイテムを作成しました。');
    }
}