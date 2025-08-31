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
        // テスト用ユーザーを作成（既存のユーザーがない場合）
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テストユーザー',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // ダミー商品データ
        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'product-images/watch_1.jpg',
                'condition' => '良好'
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'product-images/hdd_2.jpg',
                'condition' => '目立った傷や汚れなし'
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'product-images/onion_3.jpg',
                'condition' => 'やや傷や汚れあり'
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'product-images/shoes_4.jpg',
                'condition' => '状態が悪い'
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image_path' => 'product-images/laptop_5.jpg',
                'condition' => '良好'
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'product-images/mic_6.jpg',
                'condition' => '目立った傷や汚れなし'
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'product-images/bag_7.jpg',
                'condition' => 'やや傷や汚れあり'
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'image_path' => 'product-images/tumbler_8.jpg',
                'condition' => '状態が悪い'
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_path' => 'product-images/coffee_grinder_9.jpg',
                'condition' => '良好'
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image_path' => 'product-images/makeup_10.jpg',
                'condition' => '目立った傷や汚れなし'
            ]
        ];

        // 商品を作成
        foreach ($items as $itemData) {
            Item::create([
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'brand' => $itemData['brand'],
                'description' => $itemData['description'],
                'image_path' => $itemData['image_path'],
                'condition' => $itemData['condition'],
                'seller_id' => $user->id,
                'buyer_id' => null,
                'sold_at' => null,
            ]);
        }
    }
}
