<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テスト用の出品者と購入者を確実に作成
        // 出品者: test@01.com (ID: 1)
        // 購入者: test@02.com (ID: 2)
        $seller = User::where('email', 'test@01.com')->first();
        if (!$seller) {
            $seller = User::factory()->create([
                'email' => 'test@01.com',
                'name' => '出品者ユーザー'
            ]);
            $this->command->info('出品者を作成しました: test@01.com (ID: ' . $seller->id . ')');
        } else {
            $this->command->info('出品者は既に存在します: test@01.com (ID: ' . $seller->id . ')');
        }

        $buyer = User::where('email', 'test@02.com')->first();
        if (!$buyer) {
            $buyer = User::factory()->create([
                'email' => 'test@02.com',
                'name' => '購入者ユーザー'
            ]);
            $this->command->info('購入者を作成しました: test@02.com (ID: ' . $buyer->id . ')');
        } else {
            $this->command->info('購入者は既に存在します: test@02.com (ID: ' . $buyer->id . ')');
        }

        // 既存のユーザーをチェックして、不足しているメールアドレスのユーザーを作成
        $createdCount = 0;

        for ($i = 1; $i <= 21; $i++) {
            $email = 'test@' . str_pad($i, 2, '0', STR_PAD_LEFT) . '.com';

            // 既に存在するかチェック
            $existingUser = User::where('email', $email)->first();

            if (!$existingUser) {
                // ユーザーを作成（メールアドレスを直接指定）
                $user = User::factory()->create([
                    'email' => $email
                ]);
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            $this->command->info("{$createdCount}人のユーザーを作成しました。");
        } else {
            $this->command->info('既に21人のユーザーが存在します。スキップします。');
        }

        $this->command->info('ログイン情報: test@01.com 〜 test@21.com / user_pass');
        $this->command->info('テスト用: 出品者 test@01.com / 購入者 test@02.com');
    }
}
