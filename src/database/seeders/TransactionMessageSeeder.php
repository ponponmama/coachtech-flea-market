<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\TransactionMessage;
use Carbon\Carbon;

class TransactionMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のユーザーと取引中の商品を取得
        $users = User::all();
        $tradingItems = Item::whereNull('buyer_id')->get(); // 取引中の商品（buyer_idがnull）

        if ($users->isEmpty() || $tradingItems->isEmpty()) {
            $this->command->error('ユーザーまたは取引中の商品が見つかりません。先にUserSeederとItemSeederを実行してください。');
            return;
        }

        // 各取引中の商品に対してメッセージを作成
        foreach ($tradingItems as $item) {
            $seller = $item->seller; // 出品者
            $buyer = $users->where('id', '!=', $seller->id)->random(); // 購入希望者（出品者以外のランダムなユーザー）

            // メッセージのやり取りをシミュレート（2-10個のメッセージ）
            $messageCount = rand(2, 10);
            $baseTime = Carbon::now()->subDays(rand(1, 30)); // 1-30日前から開始

            // 出品者が受信者になる未読メッセージを確実に作成するためのフラグ
            $hasUnreadForSeller = false;
            // 未読メッセージ数をランダムに設定（1-3件）
            $unreadCountForSeller = rand(1, 3);

            for ($i = 0; $i < $messageCount; $i++) {
                // 送信者を交互に変更（最初は購入希望者から）
                $sender = ($i % 2 === 0) ? $buyer : $seller;
                $receiver = ($i % 2 === 0) ? $seller : $buyer;

                // メッセージ作成時間を時系列順に
                $createdAt = $baseTime->copy()->addMinutes($i * rand(10, 60));

                // 既読フラグ
                // 出品者が受信者になるメッセージ（偶数番目）で、未読メッセージを作成
                $isRead = true;
                if ($i % 2 === 0 && $receiver->id === $seller->id) {
                    // 出品者が受信者の場合
                    if ($unreadCountForSeller > 0) {
                        // 未読メッセージを作成
                        $isRead = false;
                        $unreadCountForSeller--;
                    }
                }

                TransactionMessage::create([
                    'item_id' => $item->id,
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'message' => $this->getRandomMessage(),
                    'image_path' => null, // 画像は後で追加可能
                    'is_read' => $isRead,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('取引メッセージデータを作成しました。');
    }

    private function getRandomMessage()
    {
        $messages = [
            'こんにちは。この商品について質問があります。',
            '商品の状態を詳しく教えていただけますか？',
            '送料は込みですか？',
            '面交可能ですか？',
            'サイズを確認させてください。',
            '色は写真通りですか？',
            '使用年数はどのくらいですか？',
            '傷や汚れはありますか？',
            '箱や説明書は付属していますか？',
            '値下げ交渉可能ですか？',
            'ありがとうございます。',
            '購入を検討中です。',
            '詳細を教えてください。',
            '写真を追加で撮ってもらえますか？',
            '発送方法は何がありますか？',
            '支払い方法は何がありますか？',
            '即購入希望です！',
            'この商品について詳しく知りたいです。',
            '了解しました。',
            'よろしくお願いします。',
            '商品を受け取りました。ありがとうございました！',
            '発送お願いします。',
            '支払い完了しました。',
        ];

        return $messages[array_rand($messages)];
    }
}