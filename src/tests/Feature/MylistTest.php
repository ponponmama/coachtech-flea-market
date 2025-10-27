<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class MylistTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: いいねした商品だけが表示される
     * ID: 5-1
     *
     * テストシナリオ:
     * 1. ユーザーにログインをする
     * 2. マイリストページを開く
     *
     * 期待結果: いいねをした商品が表示される
     */
    public function test_mylist_shows_only_liked_items()
    {
        /** @var User $user */
        $user = User::factory()->create();

        // いいねした商品
        $likedItem = Item::factory()->create([
            'name' => 'いいねした商品',
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);
        Like::create(['user_id' => $user->id, 'item_id' => $likedItem->id]);

        // いいねしていない商品
        $notLikedItem = Item::factory()->create([
            'name' => 'いいねしていない商品',
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいねした商品');
        $response->assertDontSee('いいねしていない商品');
    }

    /**
     * テスト項目: 購入済み商品は「Sold」と表示される
     * ID: 5-2
     *
     * テストシナリオ:
     * 1. ユーザーにログインをする
     * 2. マイリストページを開く
     * 3. 購入済み商品を確認する
     *
     * 期待結果: 購入済み商品に「Sold」のラベルが表示される
     */
    public function test_mylist_shows_sold_badge_for_purchased_items()
    {
        /** @var User $user */
        $user = User::factory()->create();

        // 購入済みの商品（いいね済み）
        $soldItem = Item::factory()->create([
            'name' => '購入済み商品',
            'seller_id' => $user->id,
            'buyer_id' => $user->id
        ]);
        Like::create(['user_id' => $user->id, 'item_id' => $soldItem->id]);

        // 未購入の商品（いいね済み）
        $availableItem = Item::factory()->create([
            'name' => '未購入商品',
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);
        Like::create(['user_id' => $user->id, 'item_id' => $availableItem->id]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('購入済み商品');
        $response->assertSee('未購入商品');
        $response->assertSee('SOLD');
    }

    /**
     * テスト項目: 未認証の場合は何も表示されない
     * ID: 5-3
     *
     * テストシナリオ:
     * 1. マイリストページを開く
     *
     * 期待結果: 何も表示されない
     */
    public function test_mylist_shows_nothing_for_guest()
    {
        // 商品を作成（いいねもある）
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);
        Like::create(['user_id' => $user->id, 'item_id' => $item->id]);

        // 未認証でアクセス
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('テスト商品');
    }
}
