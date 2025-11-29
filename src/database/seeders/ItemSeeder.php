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

        // Factoryを使って取引中の商品を追加作成（テスト用）
        // 取引中の商品を1人につき3つ作成（test@01.com〜test@05.comのユーザーに固定）
        for ($i = 1; $i <= 5; $i++) {
            $email = 'test@' . str_pad($i, 2, '0', STR_PAD_LEFT) . '.com';
            $seller = User::where('email', $email)->first();

            if ($seller) {
                // 1人につき3つの取引中の商品を作成
                for ($j = 0; $j < 3; $j++) {
                    Item::factory()
                        ->trading() // 取引中（buyer_idがnull）
                        ->create([
                            'seller_id' => $seller->id,
                        ]);
                }
            }
        }

        $this->command->info(count($itemsData) . '件のアイテムを作成しました。');
        $this->command->info('15件の取引中の商品をFactoryで作成しました。（test@01.com〜test@05.comのユーザーに各3件ずつ）');
    }
}