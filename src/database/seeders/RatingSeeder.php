<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Rating;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // test@03.com〜test@10.comの8人を取得
        $users = [];
        for ($i = 3; $i <= 10; $i++) {
            $email = 'test@' . str_pad($i, 2, '0', STR_PAD_LEFT) . '.com';
            $user = User::where('email', $email)->first();
            if ($user) {
                $users[] = $user;
            }
        }

        if (empty($users)) {
            $this->command->warn('test@03.com〜test@10.comのユーザーが見つかりません。');
            return;
        }

        // 購入済みの商品を取得
        $purchasedItems = Item::whereNotNull('buyer_id')
            ->whereNotNull('seller_id')
            ->get();

        if ($purchasedItems->isEmpty()) {
            $this->command->warn('購入済みの商品が見つかりません。');
            return;
        }

        $ratingCount = 0;

        // 各ユーザーに対して評価を作成（購入者が購入した商品に対してのみ評価）
        foreach ($users as $user) {
            // このユーザーが購入した商品を取得
            $userPurchasedItems = $purchasedItems->where('buyer_id', $user->id);

            // 購入した商品に対して、出品者を評価（購入者のみが評価できる）
            foreach ($userPurchasedItems as $item) {
                // 既に評価済みかチェック
                $existingRating = Rating::where('item_id', $item->id)
                    ->where('rater_id', $user->id)
                    ->first();

                if (!$existingRating) {
                    // 星評価を平均的に設定（3〜5の範囲）
                    $rating = rand(3, 5);

                    Rating::create([
                        'item_id' => $item->id,
                        'rater_id' => $user->id, // 購入者
                        'rated_user_id' => $item->seller_id, // 出品者
                        'rating' => $rating,
                        'comment' => null, // コメントは任意なのでnull
                    ]);
                    $ratingCount++;
                }
            }
        }

        $this->command->info("{$ratingCount}件の評価データを作成しました。");
    }
}