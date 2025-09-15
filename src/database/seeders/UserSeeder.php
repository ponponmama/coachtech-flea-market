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
        // 21人のユーザーを作成
        $users = User::factory(21)->create();

        // user_idに合わせてemailを更新
        foreach ($users as $user) {
            $user->update([
                'email' => 'test@' . str_pad($user->id, 2, '0', STR_PAD_LEFT) . '.com'
            ]);
        }

        $this->command->info('21人のユーザーを作成しました。');
        $this->command->info('ログイン情報: test@01.com 〜 test@21.com / user_pass');
    }
}
