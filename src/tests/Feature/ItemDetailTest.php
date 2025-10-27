<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: 必要な情報が表示される
     * ID: 7-1
     *
     * テストシナリオ:
     * 1. 商品詳細ページを開く
     *
     * 期待結果: すべての情報が商品詳細ページに表示されている
     */
    public function test_item_detail_displays_required_information()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        // カテゴリを作成
        $category1 = Category::factory()->create(['name' => 'スマートフォン']);
        $category2 = Category::factory()->create(['name' => 'アクセサリー']);

        $item = Item::factory()->create([
            'name' => 'iPhone 15 Pro',
            'brand' => 'Apple',
            'price' => 150000,
            'description' => '最新のiPhoneです',
            'condition' => '新品',
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // カテゴリを関連付け
        $item->categories()->attach([$category1->id, $category2->id]);

        // コメントを作成
        $comment = Comment::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'content' => 'とても良い商品です'
        ]);

        // いいねを作成
        Like::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id
        ]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertViewIs('item-detail');

        // 商品基本情報
        $response->assertSee('iPhone 15 Pro'); // 商品名
        $response->assertSee('Apple'); // ブランド名
        $response->assertSee('150,000'); // 価格（カンマ区切り）
        $response->assertSee('最新のiPhoneです'); // 商品説明

        // 商品情報
        $response->assertSee('スマートフォン'); // カテゴリ1
        $response->assertSee('アクセサリー'); // カテゴリ2
        $response->assertSee('新品'); // 商品の状態

        // いいね数とコメント数
        $response->assertSee('1'); // いいね数
        $response->assertSee('1'); // コメント数

        // コメント情報
        $response->assertSee('とても良い商品です'); // コメント内容
    }

    /**
     * テスト項目: 複数選択されたカテゴリが表示されているか
     * ID: 7-2
     *
     * テストシナリオ:
     * 1. 商品詳細ページを開く
     *
     * 期待結果: 複数選択されたカテゴリが商品詳細ページに表示されている
     */
    public function test_item_detail_displays_multiple_categories()
    {
        $seller = User::factory()->create();

        // 複数のカテゴリを作成
        $category1 = Category::factory()->create(['name' => 'スマートフォン']);
        $category2 = Category::factory()->create(['name' => 'アクセサリー']);
        $category3 = Category::factory()->create(['name' => 'Apple']);

        $item = Item::factory()->create([
            'name' => 'iPhone 15 Pro',
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // 3つのカテゴリを関連付け
        $item->categories()->attach([$category1->id, $category2->id, $category3->id]);

        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        // すべてのカテゴリが表示されていることを確認
        $response->assertSee('スマートフォン');
        $response->assertSee('アクセサリー');
        $response->assertSee('Apple');
    }
}
