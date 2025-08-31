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
        // 20人のユーザーを作成
        User::factory(20)->create()->each(function ($user) {
            // 一部のユーザー（最初の10人）にプロフィール情報を追加
            if ($user->id <= 10) {
                $user->profile()->create([
                    'postal_code' => '1234567', // ハイフンなしで保存
                    'address' => '東京都渋谷区テスト住所' . $user->id,
                    'building_name' => 'テストビル' . $user->id,
                ]);
            }
        });

        // テスト用のユーザーを作成（ログイン用）
        $testUser = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'is_first_login' => false,
        ]);

        // テストユーザーにプロフィール情報を追加
        $testUser->profile()->create([
            'postal_code' => '1234567',
            'address' => '東京都渋谷区テスト住所',
            'building_name' => 'テストビル',
        ]);

        $this->command->info('20人のユーザーとテストユーザーを作成しました。');
        $this->command->info('テストユーザーのログイン情報:');
        $this->command->info('Email: test@example.com');
        $this->command->info('Password: password123');
    }
}
