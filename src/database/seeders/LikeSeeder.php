<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class LikeSeeder extends Seeder
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
            $this->command->error('ユーザーまたはアイテムが見つかりません。先にUserSeederとItemSeederを実行してください。');
            return;
        }

        // 各ユーザーがランダムにアイテムをお気に入りに追加
        foreach ($users as $user) {
            $likeCount = rand(0, 10); // 0-10個のお気に入り
            $randomItems = $items->random(min($likeCount, $items->count()));

            foreach ($randomItems as $item) {
                // 重複チェック
                $existingLike = Like::where('user_id', $user->id)
                                  ->where('item_id', $item->id)
                                  ->first();

                if (!$existingLike) {
                    Like::create([
                        'user_id' => $user->id,
                        'item_id' => $item->id,
                    ]);
                }
            }
        }

        $this->command->info('いいねデータを作成しました。');
    }
}
