<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemCategory;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 既存のアイテムとカテゴリを取得
        $items = Item::all();
        $categories = Category::all();

        if ($items->isEmpty() || $categories->isEmpty()) {
            $this->command->error('アイテムまたはカテゴリが見つかりません。先にItemSeederとCategorySeederを実行してください。');
            return;
        }

        // 各アイテムに1-3個のカテゴリをランダムに割り当て
        foreach ($items as $item) {
            $categoryCount = rand(1, 3);
            $randomCategories = $categories->random($categoryCount);

            foreach ($randomCategories as $category) {
                ItemCategory::create([
                    'item_id' => $item->id,
                    'category_id' => $category->id,
                ]);
            }
        }

        $this->command->info('アイテムとカテゴリの関連付けを作成しました。');
    }
}
