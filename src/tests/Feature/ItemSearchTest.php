<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: 「商品名」で部分一致検索ができる
     * ID: 6-1
     *
     * テストシナリオ:
     * 1. 検索欄にキーワードを入力
     * 2. 検索ボタンを押す
     *
     * 期待結果: 部分一致する商品が表示される
     */
    public function test_item_name_partial_search_works()
    {
        $seller = User::factory()->create();

        // 検索対象の商品
        $targetItem1 = Item::factory()->create([
            'name' => 'iPhone 15 Pro',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);
        $targetItem2 = Item::factory()->create([
            'name' => 'iPhone 15 Pro Max',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        // 検索対象外の商品
        $otherItem = Item::factory()->create([
            'name' => 'Samsung Galaxy',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $response = $this->get('/?search=iPhone');

        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro');
        $response->assertSee('iPhone 15 Pro Max');
        $response->assertDontSee('Samsung Galaxy');
    }

    /**
     * テスト項目: 検索状態がマイリストでも保持されている
     * ID: 6-2
     *
     * テストシナリオ:
     * 1. ホームページで商品を検索
     * 2. 検索結果が表示される
     * 3. マイリストページに遷移
     *
     * 期待結果: 検索キーワードが保持されている
     */
    public function test_search_state_persists_in_mylist()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $seller */
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'name' => 'iPhone 15 Pro',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // 1. ホームページで商品を検索
        $searchResponse = $this->get('/?search=iPhone');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('iPhone 15 Pro');

        // 2. マイリストページに遷移
        $mylistResponse = $this->get('/?tab=mylist&search=iPhone');

        $mylistResponse->assertStatus(200);
        // 検索キーワードが保持されていることを確認
        $mylistResponse->assertSee('iPhone');
    }
}
