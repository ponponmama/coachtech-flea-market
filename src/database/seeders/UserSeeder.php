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
        // 要件に基づいたユーザーデータを3つ作成
        // 1. CO01~CO05の商品データを出品したユーザー
        $user1 = User::where('email', 'test@01.com')->first();
        if (!$user1) {
            $user1 = User::factory()->create([
                'email' => 'test@01.com',
                'name' => 'CO01~CO05出品ユーザー'
            ]);
            $this->command->info('CO01~CO05出品ユーザーを作成しました: test@01.com (ID: ' . $user1->id . ')');
        } else {
            $this->command->info('CO01~CO05出品ユーザーは既に存在します: test@01.com (ID: ' . $user1->id . ')');
        }

        // 2. CO06~CO10の商品データを出品したユーザー
        $user2 = User::where('email', 'test@02.com')->first();
        if (!$user2) {
            $user2 = User::factory()->create([
                'email' => 'test@02.com',
                'name' => 'CO06~CO10出品ユーザー'
            ]);
            $this->command->info('CO06~CO10出品ユーザーを作成しました: test@02.com (ID: ' . $user2->id . ')');
        } else {
            $this->command->info('CO06~CO10出品ユーザーは既に存在します: test@02.com (ID: ' . $user2->id . ')');
        }

        // 3. 何も紐づけられていないユーザー
        $user3 = User::where('email', 'test@03.com')->first();
        if (!$user3) {
            $user3 = User::factory()->create([
                'email' => 'test@03.com',
                'name' => '紐づけなしユーザー'
            ]);
            $this->command->info('紐づけなしユーザーを作成しました: test@03.com (ID: ' . $user3->id . ')');
        } else {
            $this->command->info('紐づけなしユーザーは既に存在します: test@03.com (ID: ' . $user3->id . ')');
        }

        // 既存のユーザーをチェックして、不足しているメールアドレスのユーザーを作成（test@04.com以降）
        $createdCount = 0;

        for ($i = 4; $i <= 21; $i++) {
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
            $this->command->info("{$createdCount}人の追加ユーザーを作成しました。");
        } else {
            $this->command->info('既に全ユーザーが存在します。スキップします。');
        }

        $this->command->info('ログイン情報: test@01.com 〜 test@21.com / user_pass');
        $this->command->info('要件に基づくユーザー:');
        $this->command->info('  - test@01.com: CO01~CO05の商品を出品');
        $this->command->info('  - test@02.com: CO06~CO10の商品を出品');
        $this->command->info('  - test@03.com: 何も紐づけられていないユーザー');
    }
}
