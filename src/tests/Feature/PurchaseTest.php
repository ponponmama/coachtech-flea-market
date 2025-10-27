<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use Mockery;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Stripeをモックする
     */
    private function mockStripe($itemId = null, $userId = null)
    {
        // StripeのAPIキーを設定（テスト用）
        Stripe::setApiKey('sk_test_mock_key');

        // StripeのCheckout\Sessionをモック
        $mockSession = Mockery::mock('overload:Stripe\Checkout\Session');
        $mockSession->shouldReceive('create')
            ->andReturn((object)[
                'id' => 'cs_test_mock_session_id',
                'url' => 'https://checkout.stripe.com/test'
            ]);

        // Session::retrieveもモック
        $mockSession->shouldReceive('retrieve')
            ->andReturn((object)[
                'id' => 'cs_test_mock_session_id',
                'payment_status' => 'paid',
                'amount_total' => 1000,
                'metadata' => (object)[
                    'item_id' => $itemId ?? '1',
                    'user_id' => $userId ?? '1',
                    'payment_method' => 'credit'
                ]
            ]);
    }

    /**
     * テスト項目: 「購入する」ボタンを押下すると購入が完了する
     * ID: 10-1
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

        // Stripeをモックしてテスト環境で動作するようにする
        $this->mockStripe($item->id, $user->id);

        $response = $this->post("/create-payment-session", [
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'shipping_address' => 'テスト住所'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['session_url', 'session_id']);

        // 決済セッション作成後、successメソッドを呼び出して購入を完了させる
        $sessionId = $response->json('session_id');

        // successメソッドを呼び出して購入を完了
        $successResponse = $this->get("/payment/success?session_id={$sessionId}");
        $successResponse->assertRedirect("/purchase/{$item->id}");
        $successResponse->assertSessionHas('success', '決済が完了しました！');

        // 商品が購入済みになっていることを確認
        $item->refresh();
        $this->assertEquals($user->id, $item->buyer_id);
        $this->assertNotNull($item->sold_at);
    }

    /**
     * テスト項目: 購入した商品は商品一覧画面にて「sold」と表示される
     * ID: 10-2
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

        // Stripeをモックしてテスト環境で動作するようにする
        $this->mockStripe($item->id, $user->id);

        // 購入処理
        $response = $this->post("/create-payment-session", [
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'shipping_address' => 'テスト住所'
        ]);

        $response->assertStatus(200);
        $sessionId = $response->json('session_id');

        // successメソッドを呼び出して購入を完了
        $successResponse = $this->get("/payment/success?session_id={$sessionId}");
        $successResponse->assertRedirect("/purchase/{$item->id}");

        // 商品が購入済みになっていることを確認
        $item->refresh();
        $this->assertEquals($user->id, $item->buyer_id);
        $this->assertNotNull($item->sold_at);

        // 商品一覧画面でSOLDバッジが表示されることを確認
        $indexResponse = $this->get('/');
        $indexResponse->assertSee('SOLD');
    }

    /**
     * テスト項目: 「プロフィール/購入した商品一覧」に追加されている
     * ID: 10-3
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

        // Stripeをモックしてテスト環境で動作するようにする
        $this->mockStripe($item->id, $user->id);

        // 購入処理
        $response = $this->post("/create-payment-session", [
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'shipping_address' => 'テスト住所'
        ]);

        $response->assertStatus(200);
        $sessionId = $response->json('session_id');

        // successメソッドを呼び出して購入を完了
        $successResponse = $this->get("/payment/success?session_id={$sessionId}");
        $successResponse->assertRedirect("/purchase/{$item->id}");

        // 商品が購入済みになっていることを確認
        $item->refresh();
        $this->assertEquals($user->id, $item->buyer_id);
        $this->assertNotNull($item->sold_at);

        // プロフィール画面で購入した商品が表示されることを確認
        $profileResponse = $this->get('/mypage?page=buy');
        $profileResponse->assertSee('購入した商品');
    }
}
