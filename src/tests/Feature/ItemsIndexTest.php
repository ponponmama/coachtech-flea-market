<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;

class ItemsIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目1: 全商品を取得できる（未ログイン時は全件表示）
     * ID: 4-1
     *
     * シナリオ:
     * 1. 複数の商品のダミーデータを作成する
     * 2. 商品一覧ページを開く（未ログイン）
     * 3. すべての商品名が表示される
     */
    public function test_index_shows_all_items_for_guest()
    {
        $sellerA = User::factory()->create();
        $sellerB = User::factory()->create();

        $item1 = Item::factory()->create(['name' => '商品A', 'seller_id' => $sellerA->id, 'buyer_id' => null]);
        $item2 = Item::factory()->create(['name' => '商品B', 'seller_id' => $sellerB->id, 'buyer_id' => null]);
        $item3 = Item::factory()->create(['name' => '商品C', 'seller_id' => $sellerA->id, 'buyer_id' => null]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertSee('商品A');
        $response->assertSee('商品B');
        $response->assertSee('商品C');
    }

    /**
     * テスト項目2: 購入済み商品は「SOLD」と表示される
     * ID: 4-2
     *
     * シナリオ:
     * 1. 購入済み（buyer_id が存在）の商品を作成する
     * 2. 商品一覧ページを開く
     * 3. 当該商品に「SOLD」ラベルが表示される
     */
    public function test_index_shows_sold_badge_for_purchased_items()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $soldItem = Item::factory()->create([
            'name' => '売れた商品',
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
        ]);

        $unsoldItem = Item::factory()->create([
            'name' => '未販売商品',
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertSee('売れた商品');
        $response->assertSee('SOLD');
        $response->assertSee('未販売商品');
    }

    /**
     * テスト項目3: 自分が出品した商品は表示されない（ログイン時）
     * ID: 4-3
     *
     * シナリオ:
     * 1. ログインユーザーを作成し、そのユーザーが出品した商品と他ユーザーの商品を作成する
     * 2. 商品一覧ページを開く（ログイン状態）
     * 3. 自分の出品は表示されず、他ユーザーの商品は表示される
     */
    public function test_index_hides_items_listed_by_authenticated_user()
    {
        /** @var User $me */
        $me = User::factory()->create();
        /** @var User $other */
        $other = User::factory()->create();

        $myItem = Item::factory()->create(['name' => '自分の出品', 'seller_id' => $me->id]);
        $otherItem = Item::factory()->create(['name' => '他人の出品', 'seller_id' => $other->id]);

        $this->actingAs($me);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('index');
        $response->assertDontSee('自分の出品');
        $response->assertSee('他人の出品');
    }
}
