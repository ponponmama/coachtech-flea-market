<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentSeeder extends Seeder
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

        // 各アイテムにランダムにコメントを追加
        foreach ($items as $item) {
            $commentCount = rand(0, 5); // 0-5個のコメント

            for ($i = 0; $i < $commentCount; $i++) {
                $randomUser = $users->random();

                Comment::create([
                    'user_id' => $randomUser->id,
                    'item_id' => $item->id,
                    'content' => $this->getRandomComment(),
                ]);
            }
        }

        $this->command->info('コメントデータを作成しました。');
    }

    private function getRandomComment()
    {
        $comments = [
            'とても良い商品ですね！',
            '状態はどうですか？',
            '送料は込みですか？',
            '面交可能ですか？',
            'サイズは何センチですか？',
            '色は何色ですか？',
            '使用年数はどのくらいですか？',
            '傷や汚れはありますか？',
            '箱や説明書は付属していますか？',
            '値下げ交渉可能ですか？',
            '素敵な商品ですね！',
            'お気に入りです！',
            '購入を検討中です。',
            '詳細を教えてください。',
            '写真を追加で撮ってもらえますか？',
            '発送方法は何がありますか？',
            '支払い方法は何がありますか？',
            '即購入希望です！',
            'この商品について詳しく知りたいです。',
            '他の商品も出品されていますか？'
        ];

        return $comments[array_rand($comments)];
    }
}
