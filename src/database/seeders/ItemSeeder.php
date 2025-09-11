<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のユーザーを取得
        $users = \App\Models\User::all();

        if ($users->isEmpty()) {
            $this->command->error('ユーザーが見つかりません。先にUserSeederを実行してください。');
            return;
        }

        // 50個のアイテムを作成し、既存のユーザーを適切に割り当て
        for ($i = 0; $i < 50; $i++) {
            $item = Item::factory()->create([
                'seller_id' => $users->random()->id,
            ]);
        }

        $this->command->info('50個のアイテムを作成しました。');
    }
}
