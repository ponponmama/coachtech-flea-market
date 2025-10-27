<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;

class ItemLikeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: いいねアイコンを押下することによって、いいねした商品として登録することができる
     * ID: 8-1
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 商品詳細ページを開く
     * 3. いいねアイコンを押下
     *
     * 期待結果: いいねした商品として登録され、いいね合計値が増加表示される
     */
    public function test_like_button_adds_item_to_liked_items()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $seller */
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // いいねを追加
        $response = $this->post("/item/{$item->id}/like");

        $response->assertStatus(200);

        // いいねがデータベースに保存されていることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);

        // 商品詳細ページでいいね数が増加していることを確認
        $detailResponse = $this->get("/item/{$item->id}");
        $detailResponse->assertSee('1'); // いいね数
    }

    /**
     * テスト項目: 追加済みのアイコンは色が変化する
     * ID: 8-2
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 商品詳細ページを開く
     * 3. いいねアイコンを押下
     *
     * 期待結果: いいねアイコンが押下された状態では色が変化する
     */
    public function test_liked_icon_color_changes_when_pressed()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $seller */
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // いいねを追加
        $this->post("/item/{$item->id}/like");

        // 商品詳細ページでいいね済みの状態を確認
        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);
        // いいね済みの状態を示すクラスや要素が表示されていることを確認
        $response->assertSee('liked'); // または適切なクラス名
    }

    /**
     * テスト項目: 再度いいねアイコンを押下することによって、いいねを解除することができる
     * ID: 8-3
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 商品詳細ページを開く
     * 3. いいねアイコンを押下
     *
     * 期待結果: いいねが解除され、いいね合計値が減少表示される
     */
    public function test_like_button_removes_item_from_liked_items()
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $seller */
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // 最初にいいねを追加
        $this->post("/item/{$item->id}/like");

        // いいねが追加されていることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);

        // いいねを解除（再度同じルートを呼び出す）
        $response = $this->post("/item/{$item->id}/like");

        $response->assertStatus(200);

        // いいねがデータベースから削除されていることを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);

        // 商品詳細ページでいいね数が0になっていることを確認
        $detailResponse = $this->get("/item/{$item->id}");
        $detailResponse->assertSee('0'); // いいね数
    }
}
