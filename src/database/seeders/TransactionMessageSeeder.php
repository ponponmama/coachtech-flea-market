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

        // テスト用：test@01.com（出品者）とtest@02.com（購入者）を取得
        $testSeller = User::where('email', 'test@01.com')->first();
        $testBuyer = User::where('email', 'test@02.com')->first();

        // 各取引中の商品に対してメッセージを作成
        foreach ($tradingItems as $item) {
            $seller = $item->seller; // 出品者

            // テスト用：test@01.comとtest@02.comの間で取引メッセージを作成
            if ($testSeller && $testBuyer) {
                if ($seller->email === 'test@01.com') {
                    // test@01.comが出品した商品の場合、test@02.comを購入希望者にする
                    $buyer = $testBuyer;
                } elseif ($seller->email === 'test@02.com') {
                    // test@02.comが出品した商品の場合、test@01.comを購入希望者にする
                    $buyer = $testSeller;
                } else {
                    // それ以外はランダムな購入希望者
                    $buyer = $users->where('id', '!=', $seller->id)->random();
                }
            } else {
                // テストユーザーが見つからない場合はランダムな購入希望者
                $buyer = $users->where('id', '!=', $seller->id)->random();
            }

            // メッセージのやり取りをシミュレート（2-10個のメッセージ）
            $messageCount = rand(2, 10);
            $baseTime = Carbon::now()->subDays(rand(1, 30)); // 1-30日前から開始

            // 未読メッセージ数をランダムに設定（1-3件）
            // 出品者と購入者の両方に未読メッセージを作成
            $unreadCountForSeller = rand(1, 3);
            $unreadCountForBuyer = rand(1, 3);

            for ($i = 0; $i < $messageCount; $i++) {
                // 送信者を交互に変更（最初は購入希望者から）
                $sender = ($i % 2 === 0) ? $buyer : $seller;
                $receiver = ($i % 2 === 0) ? $seller : $buyer;

                // メッセージ作成時間を時系列順に
                $createdAt = $baseTime->copy()->addMinutes($i * rand(10, 60));

                // 既読フラグ
                // 最新のメッセージから未読メッセージを作成（現実的なシミュレーション）
                $isRead = true;

                // 出品者が受信者の場合
                if ($receiver->id === $seller->id && $unreadCountForSeller > 0) {
                    // 最新のメッセージから未読を作成（後ろから数える）
                    $remainingMessages = $messageCount - $i;
                    if ($remainingMessages <= $unreadCountForSeller) {
                        $isRead = false;
                        $unreadCountForSeller--;
                    }
                }

                // 購入者が受信者の場合
                if ($receiver->id === $buyer->id && $unreadCountForBuyer > 0) {
                    // 最新のメッセージから未読を作成（後ろから数える）
                    $remainingMessages = $messageCount - $i;
                    if ($remainingMessages <= $unreadCountForBuyer) {
                        $isRead = false;
                        $unreadCountForBuyer--;
                    }
                }

                // 送信者が出品者か購入者かでメッセージを分ける
                $isSeller = ($sender->id === $seller->id);
                $message = $isSeller ? $this->getSellerMessage() : $this->getBuyerMessage();

                TransactionMessage::create([
                    'item_id' => $item->id,
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'message' => $message,
                    'image_path' => null, // 画像は後で追加可能
                    'is_read' => $isRead,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('取引メッセージデータを作成しました。');
    }

    /**
     * 購入者が送るメッセージを取得
     */
    private function getBuyerMessage()
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
            '購入を検討中です。',
            '詳細を教えてください。',
            '写真を追加で撮ってもらえますか？',
            '発送方法は何がありますか？',
            '支払い方法は何がありますか？',
            '即購入希望です！',
            'この商品について詳しく知りたいです。',
            'よろしくお願いします。',
            '商品を受け取りました。ありがとうございました！',
            '発送お願いします。',
            '支払い完了しました。',
        ];

        return $messages[array_rand($messages)];
    }

    /**
     * 出品者が送るメッセージを取得
     */
    private function getSellerMessage()
    {
        $messages = [
            'こんにちは。ご質問ありがとうございます。',
            '商品の状態は良好です。',
            '送料は別途かかります。',
            '面交可能です。',
            'サイズは写真に記載の通りです。',
            '色は写真通りです。',
            '使用年数は約2年です。',
            '目立った傷や汚れはありません。',
            '箱と説明書は付属しています。',
            '値下げは難しいです。',
            'ありがとうございます。',
            '詳細については写真をご確認ください。',
            '写真を追加で送ります。',
            '発送方法は宅配便のみです。',
            '支払い方法は銀行振込またはクレジットカードです。',
            '了解しました。',
            'よろしくお願いします。',
            '発送しました。',
            '商品は本日発送いたします。',
            'お待ちしております。',
        ];

        return $messages[array_rand($messages)];
    }
}