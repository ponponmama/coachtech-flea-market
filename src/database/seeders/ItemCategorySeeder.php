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

        // 各アイテムに適切なカテゴリを割り当て
        foreach ($items as $item) {
            // アイテムのカテゴリー名を取得（ItemFactoryで設定されたもの）
            if (isset($item->category_names)) {
                $categoryNames = $item->category_names;
            } else {
                // フォールバック：商品名に基づいてカテゴリーを推測
                $categoryNames = $this->getCategoryByItemName($item->name);
            }

            // カテゴリー名からカテゴリーIDを取得
            foreach ($categoryNames as $categoryName) {
                $category = $categories->where('name', $categoryName)->first();
                if ($category) {
                    ItemCategory::create([
                        'item_id' => $item->id,
                        'category_id' => $category->id,
                    ]);
                }
            }
        }

        $this->command->info('アイテムとカテゴリの関連付けを作成しました。');
    }

    /**
     * 商品名に基づいてカテゴリーを推測（フォールバック用）
     */
    private function getCategoryByItemName($itemName)
    {
        $categoryMapping = [
            '腕時計' => ['アクセサリー', 'メンズ'],
            'HDD' => ['家電'],
            '玉ねぎ' => ['キッチン'],
            '革靴' => ['ファッション', 'メンズ'],
            'ノートPC' => ['家電'],
            'マイク' => ['家電'],
            'ショルダーバッグ' => ['ファッション', 'レディース'],
            'タンブラー' => ['キッチン'],
            'コーヒーミル' => ['キッチン'],
            'メイクセット' => ['コスメ', 'レディース'],
        ];

        foreach ($categoryMapping as $keyword => $categories) {
            if (strpos($itemName, $keyword) !== false) {
                return $categories;
            }
        }

        // デフォルト
        return ['ファッション'];
    }
}
