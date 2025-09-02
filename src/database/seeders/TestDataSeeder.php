<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Purchase;
use App\Models\Profile;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // カテゴリーが存在しない場合は作成
        if (Category::count() == 0) {
            $this->call(CategorySeeder::class);
        }

        // ユーザーが存在しない場合は作成
        if (User::count() == 0) {
            $this->call(UserSeeder::class);
        }

        // 商品が存在しない場合は作成
        if (Item::count() == 0) {
            $this->call(ItemSeeder::class);
        }

        // 追加のテストデータを作成
        $this->createAdditionalTestData();

        $this->command->info('テストデータの作成が完了しました。');
    }

    private function createAdditionalTestData(): void
    {
        // 追加のユーザーを作成（プロフィール付き）
        User::factory(15)->create()->each(function ($user) {
            $user->profile()->create(Profile::factory()->make()->toArray());
        });

        // 追加の商品を作成
        Item::factory(30)->create();

        // コメントを作成
        Comment::factory(50)->create();

        // いいねを作成
        Like::factory(100)->create();

        // 購入履歴を作成
        Purchase::factory(20)->create();

        $this->command->info('追加のテストデータを作成しました：');
        $this->command->info('- ユーザー: ' . User::count() . '人');
        $this->command->info('- 商品: ' . Item::count() . '個');
        $this->command->info('- コメント: ' . Comment::count() . '件');
        $this->command->info('- いいね: ' . Like::count() . '件');
        $this->command->info('- 購入履歴: ' . Purchase::count() . '件');
    }
}



