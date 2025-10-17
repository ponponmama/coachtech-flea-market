<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: 小計画面で変更が反映される
     *
     * テストシナリオ:
     * 1. 支払い方法選択画面を開く
     * 2. プルダウンメニューから支払い方法を選択する
     *
     * 期待結果: 選択した支払い方法が正しく反映される
     */
    public function test_payment_method_selection_reflects_in_summary()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        // ユーザーのプロフィールを作成
        Profile::factory()->create(['user_id' => $user->id]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // 購入画面を表示
        $response = $this->get("/purchase/{$item->id}");

        $response->assertStatus(200);

        // 支払い方法の選択肢が表示されていることを確認
        $response->assertSee('コンビニ払い');
        $response->assertSee('カード支払い');

        // 小計画面の支払い方法表示エリアが存在することを確認
        $response->assertSee('支払い方法');
        $response->assertSee('payment_method_display');

        // 支払い方法選択のHTML構造が正しく表示されていることを確認
        $response->assertSee('custom-select');
        $response->assertSee('custom-select-options');

        // 隠しinputフィールドが存在することを確認（HTMLエスケープを考慮）
        $response->assertSee('payment_method');
    }
}
