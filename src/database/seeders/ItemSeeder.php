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

        // 要件に基づいた商品データ（CO01〜CO10）
        $itemsData = [
            // CO01~CO05: test@01.comが出品
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'product-images/watch_1.jpg',
                'condition' => '良好',
                'seller_email' => 'test@01.com', // CO01
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'product-images/hdd_2.jpg',
                'condition' => '目立った傷や汚れなし',
                'seller_email' => 'test@01.com', // CO02
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'product-images/onion_3.jpg',
                'condition' => 'やや傷や汚れあり',
                'seller_email' => 'test@01.com', // CO03
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'product-images/shoes_4.jpg',
                'condition' => '状態が悪い',
                'seller_email' => 'test@01.com', // CO04
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image_path' => 'product-images/laptop_5.jpg',
                'condition' => '良好',
                'seller_email' => 'test@01.com', // CO05
            ],
            // CO06~CO10: test@02.comが出品
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'product-images/mic_6.jpg',
                'condition' => '目立った傷や汚れなし',
                'seller_email' => 'test@02.com', // CO06
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'product-images/bag_7.jpg',
                'condition' => 'やや傷や汚れあり',
                'seller_email' => 'test@02.com', // CO07
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'image_path' => 'product-images/tumbler_8.jpg',
                'condition' => '状態が悪い',
                'seller_email' => 'test@02.com', // CO08
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'product-images/coffee_grinder_9.jpg',
                'condition' => '良好',
                'seller_email' => 'test@02.com', // CO09
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image_path' => 'product-images/makeup_10.jpg',
                'condition' => '目立った傷や汚れなし',
                'seller_email' => 'test@02.com', // CO10
            ],
        ];

        // 要件に基づいて商品を作成
        foreach ($itemsData as $index => $itemData) {
            $sellerEmail = $itemData['seller_email'];
            unset($itemData['seller_email']); // seller_emailを削除

            $seller = User::where('email', $sellerEmail)->first();

            if (!$seller) {
                $this->command->warn("出品者 {$sellerEmail} が見つかりません。スキップします。");
                continue;
            }

            Item::create(array_merge($itemData, [
                'seller_id' => $seller->id,
                'buyer_id' => null, // デフォルトは取引中
                'sold_at' => null,
            ]));
        }

        $this->command->info('要件に基づく商品データを作成しました:');
        $this->command->info('  - CO01~CO05: test@01.comが出品');
        $this->command->info('  - CO06~CO10: test@02.comが出品');

        // 要件通りに10件のみ作成するため、以下のテスト用商品作成はコメントアウト
        // Factoryを使ってテスト用の商品を追加作成
        // 出品者: test@01.com (ID: 1)
        // 購入者: test@02.com (ID: 2)
        /*
        $seller01 = User::where('email', 'test@01.com')->first(); // 出品者
        $buyer02 = User::where('email', 'test@02.com')->first(); // 購入者

        if ($seller01 && $buyer02) {
            // メールテスト用：test@01.comが出品、test@02.comが購入した商品を1つ作成（取引完了済み）
            Item::factory()
                ->create([
                    'seller_id' => $seller01->id, // 出品者ID: 1
                    'buyer_id' => $buyer02->id, // 購入者ID: 2（決済完了済み）
                    'sold_at' => now()->subDays(1), // 1日前に購入
                ]);

            // メールテスト用：test@02.comが出品、test@01.comが購入した商品を1つ作成（取引完了済み）
            Item::factory()
                ->create([
                    'seller_id' => $buyer02->id, // 出品者ID: 2
                    'buyer_id' => $seller01->id, // 購入者ID: 1（決済完了済み）
                    'sold_at' => now()->subDays(1), // 1日前に購入
                ]);

            // メールテスト用：test@01.comが出品した商品（取引中）- test@02.comが取引完了ボタンをクリックできる
            Item::factory()
                ->create([
                    'seller_id' => $seller01->id, // 出品者ID: 1
                    'buyer_id' => null, // 取引中（buyer_idがnull）
                    'sold_at' => null,
                ]);

            // メールテスト用：test@02.comが出品した商品（取引中）- test@01.comが取引完了ボタンをクリックできる
            Item::factory()
                ->create([
                    'seller_id' => $buyer02->id, // 出品者ID: 2
                    'buyer_id' => null, // 取引中（buyer_idがnull）
                    'sold_at' => null,
                ]);

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

            $this->command->info('メールテスト用の商品を作成しました:');
            $this->command->info('  - test@01.comが出品、test@02.comが購入した商品（決済完了済み）: 1件');
            $this->command->info('  - test@02.comが出品、test@01.comが購入した商品（決済完了済み）: 1件');
            $this->command->info('  - test@01.comが出品した商品（取引中）: 1件 → test@02.comが取引完了ボタンをクリック可能');
            $this->command->info('  - test@02.comが出品した商品（取引中）: 1件 → test@01.comが取引完了ボタンをクリック可能');
            $this->command->info('出品者: test@01.com (ID: ' . $seller01->id . ')');
            $this->command->info('購入者: test@02.com (ID: ' . $buyer02->id . ')');
        } else {
            $this->command->warn('test@01.com または test@02.com のユーザーが見つかりません。');
        }
        */

        $this->command->info(count($itemsData) . '件のアイテムを作成しました。');
    }
}