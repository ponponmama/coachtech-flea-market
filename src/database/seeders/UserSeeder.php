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
    }
}