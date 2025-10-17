<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;

class PurchaseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: 「購入する」ボタンを押下すると購入が完了する
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 商品購入画面を開く
     * 3. 商品を選択して「購入する」ボタンを押下
     *
     * 期待結果: 購入が完了する
     */
    public function test_purchase_button_completes_purchase()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        $response = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'credit_card',
            'shipping_address' => 'テスト住所'
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', '購入が完了しました。');

        // 商品が購入済みになっていることを確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'buyer_id' => $user->id,
        ]);

        // sold_atが設定されていることを確認
        $item->refresh();
        $this->assertNotNull($item->sold_at);
    }

    /**
     * テスト項目: 購入した商品は商品一覧画面にて「sold」と表示される
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 商品購入画面を開く
     * 3. 商品を選択して「購入する」ボタンを押下
     * 4. 商品一覧画面を表示する
     *
     * 期待結果: 購入した商品が「sold」として表示されている
     */
    public function test_purchased_item_shows_sold_on_index()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // 購入処理
        $this->post("/purchase/{$item->id}", [
            'payment_method' => 'credit_card',
            'shipping_address' => 'テスト住所'
        ]);

        // 商品一覧画面を表示
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('SOLD');
    }

    /**
     * テスト項目: 「プロフィール/購入した商品一覧」に追加されている
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 商品購入画面を開く
     * 3. 商品を選択して「購入する」ボタンを押下
     * 4. プロフィール画面を表示する
     *
     * 期待結果: 購入した商品がプロフィールの購入した商品一覧に追加されている
     */
    public function test_purchased_item_added_to_profile_purchased_list()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'name' => '購入した商品',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // 購入処理
        $this->post("/purchase/{$item->id}", [
            'payment_method' => 'credit_card',
            'shipping_address' => 'テスト住所'
        ]);

        // プロフィール画面の購入した商品一覧を表示
        $response = $this->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertSee('購入した商品');

        // データベースでも確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'buyer_id' => $user->id,
            'name' => '購入した商品'
        ]);
    }
}
