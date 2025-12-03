<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Rating;
use App\Models\TransactionMessage;

class TransactionRatingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: 取引を完了ボタンをクリックすると取引完了モーダルからユーザーの評価をすることができる（購入者）
     * ID: FN012
     *
     * テストシナリオ:
     * 1. 購入者としてログインする
     * 2. 取引中の商品の取引チャット画面を開く
     * 3. 「取引を完了する」ボタンをクリックする
     * 4. 評価モーダルが表示される
     * 5. 評価を選択して送信する
     *
     * 期待結果: 評価が保存され、商品一覧画面に遷移する
     */
    public function test_buyer_can_rate_after_completing_transaction()
    {
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null, // 取引中
        ]);

        // 取引メッセージを作成
        TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $buyer->id,
            'receiver_id' => $seller->id,
            'message' => 'テストメッセージ',
            'is_read' => false,
        ]);

        $this->actingAs($buyer);

        // 取引を完了する
        $completeResponse = $this->post("/transaction-chat/{$item->id}/complete");
        $completeResponse->assertRedirect();

        // 評価を送信する
        $ratingResponse = $this->post("/transaction-chat/{$item->id}/rating", [
            'rating' => 5,
        ]);

        $ratingResponse->assertRedirect('/');
        $ratingResponse->assertSessionHas('success', '評価を送信しました。');

        // 評価がデータベースに保存されていることを確認
        $this->assertDatabaseHas('ratings', [
            'item_id' => $item->id,
            'rater_id' => $buyer->id,
            'rated_user_id' => $seller->id,
            'rating' => 5,
        ]);
    }

    /**
     * テスト項目: 商品の購入者が取引を完了した後に、取引チャット画面を開くと取引完了モーダルからユーザーの評価をすることができる（出品者）
     * ID: FN013
     *
     * テストシナリオ:
     * 1. 出品者としてログインする
     * 2. 購入者が取引を完了した商品の取引チャット画面を開く
     * 3. 評価モーダルが自動的に表示される
     * 4. 評価を選択して送信する
     *
     * 期待結果: 評価が保存され、商品一覧画面に遷移する
     */
    public function test_seller_can_rate_after_buyer_completes_transaction()
    {
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id, // 購入者が取引を完了済み
            'sold_at' => now(),
        ]);

        // 取引メッセージを作成
        TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $buyer->id,
            'receiver_id' => $seller->id,
            'message' => 'テストメッセージ',
            'is_read' => false,
        ]);

        $this->actingAs($seller);

        // 取引チャット画面を開く（モーダルが自動的に表示される）
        $chatResponse = $this->get("/transaction-chat/{$item->id}");
        $chatResponse->assertStatus(200);

        // 評価を送信する
        $ratingResponse = $this->post("/transaction-chat/{$item->id}/rating", [
            'rating' => 4,
        ]);

        $ratingResponse->assertRedirect('/');
        $ratingResponse->assertSessionHas('success', '評価を送信しました。');

        // 評価がデータベースに保存されていることを確認
        $this->assertDatabaseHas('ratings', [
            'item_id' => $item->id,
            'rater_id' => $seller->id,
            'rated_user_id' => $buyer->id,
            'rating' => 4,
        ]);
    }

    /**
     * テスト項目: 評価を送信した後は、商品一覧画面に遷移する
     * ID: FN014
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引完了後に評価を送信する
     *
     * 期待結果: 商品一覧画面（トップページ）にリダイレクトされる
     */
    public function test_redirect_to_item_list_after_rating()
    {
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id, // 購入者が取引を完了済み
            'sold_at' => now(),
        ]);

        $this->actingAs($buyer);

        $response = $this->post("/transaction-chat/{$item->id}/rating", [
            'rating' => 5,
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', '評価を送信しました。');
    }

    /**
     * テスト項目: 評価が選択されていない場合、バリデーションメッセージが表示される
     * ID: FN012, FN013
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引完了後に評価を送信しようとする（評価を選択しない）
     *
     * 期待結果: 「評価を選択してください」というエラーメッセージが表示される
     */
    public function test_rating_required_validation()
    {
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'sold_at' => now(),
        ]);

        $this->actingAs($buyer);

        $response = $this->post("/transaction-chat/{$item->id}/rating", [
            'rating' => 0, // 評価が選択されていない
        ]);

        $response->assertSessionHasErrors(['rating']);
        $response->assertSessionHasErrors(['rating' => '評価を選択してください']);

        // 評価がデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('ratings', [
            'item_id' => $item->id,
            'rater_id' => $buyer->id,
        ]);
    }

    /**
     * テスト項目: 決済処理が完了していない場合、評価できない
     * ID: FN012, FN013
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引中の商品（buyer_idがnull）で評価を送信しようとする
     *
     * 期待結果: 400エラーが返される
     */
    public function test_cannot_rate_before_payment_processed()
    {
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null, // 決済処理が完了していない
        ]);

        $this->actingAs($buyer);

        $response = $this->post("/transaction-chat/{$item->id}/rating", [
            'rating' => 5,
        ]);

        $response->assertStatus(400);
        $response->assertSee('決済処理が完了していないため、評価できません。');

        // 評価がデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('ratings', [
            'item_id' => $item->id,
        ]);
    }

    /**
     * テスト項目: 購入者または出品者以外は評価できない
     * ID: FN012, FN013
     *
     * テストシナリオ:
     * 1. 関係のないユーザーとしてログインする
     * 2. 取引完了済みの商品で評価を送信しようとする
     *
     * 期待結果: 403エラーが返される
     */
    public function test_unauthorized_user_cannot_rate()
    {
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $otherUser = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'sold_at' => now(),
        ]);

        $this->actingAs($otherUser);

        $response = $this->post("/transaction-chat/{$item->id}/rating", [
            'rating' => 5,
        ]);

        $response->assertStatus(403);
        $response->assertSee('この取引を評価する権限がありません。');

        // 評価がデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('ratings', [
            'item_id' => $item->id,
            'rater_id' => $otherUser->id,
        ]);
    }

    /**
     * テスト項目: 決済処理が完了した後でも、購入者がまだ評価していない場合は取引完了ボタンが表示される
     * ID: FN012
     *
     * テストシナリオ:
     * 1. 購入者としてログインする
     * 2. 決済処理が完了した商品（buyer_idが設定されている）の取引チャット画面を開く
     * 3. まだ評価していない場合
     *
     * 期待結果: 取引完了ボタンが表示される
     */
    public function test_complete_button_displayed_for_purchased_item_without_rating()
    {
        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        // 決済処理が完了した商品（buyer_idが設定されている）
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'sold_at' => now(),
        ]);

        $this->actingAs($buyer);

        // 取引チャット画面を開く
        $response = $this->get("/transaction-chat/{$item->id}");

        $response->assertStatus(200);
        // 取引完了ボタンが表示されることを確認
        $response->assertSee('取引を完了する');
    }

    /**
     * テスト項目: 出品者は取引完了ボタンが表示されない
     * ID: FN012
     *
     * テストシナリオ:
     * 1. 出品者としてログインする
     * 2. 取引中の商品の取引チャット画面を開く
     *
     * 期待結果: 取引完了ボタンが表示されない
     */
    public function test_seller_cannot_see_complete_button()
    {
        /** @var User $seller */
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        // 取引中の商品
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // 取引メッセージを作成
        TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $buyer->id,
            'receiver_id' => $seller->id,
            'message' => 'テストメッセージ',
            'is_read' => false,
        ]);

        $this->actingAs($seller);

        // 取引チャット画面を開く
        $response = $this->get("/transaction-chat/{$item->id}");

        $response->assertStatus(200);
        // 取引完了ボタンが表示されないことを確認
        $response->assertDontSee('取引を完了する');
    }
}

