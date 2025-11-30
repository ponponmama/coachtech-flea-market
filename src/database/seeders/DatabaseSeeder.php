<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // テーブル間の関連付けを考慮した順序でシーダーを実行
        $this->call([
            UserSeeder::class,        // ユーザーを最初に作成
            CategorySeeder::class,    // カテゴリを作成
            ItemSeeder::class,        // アイテムを作成（ユーザーとカテゴリが必要）
            ProfileSeeder::class,     // プロフィールを作成（ユーザーが必要）
            ItemCategorySeeder::class, // アイテムとカテゴリの関連付け
            LikeSeeder::class,        // いいねを作成（ユーザーとアイテムが必要）
            CommentSeeder::class,     // コメントを作成（ユーザーとアイテムが必要）
            PurchaseSeeder::class,    // 購入を作成（ユーザー、アイテム、プロフィールが必要）
            TransactionMessageSeeder::class, // 取引メッセージを作成（ユーザーとアイテムが必要）
            RatingSeeder::class,      // 評価を作成（ユーザーとアイテムが必要）
        ]);
    }
}