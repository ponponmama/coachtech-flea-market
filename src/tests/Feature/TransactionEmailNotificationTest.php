<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Item;
use App\Models\TransactionMessage;
use App\Mail\TransactionCompleteNotification;

class TransactionEmailNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: 商品購入者が取引を完了すると、商品出品者宛に自動で通知メールが送信される
     * ID: FN016
     *
     * テストシナリオ:
     * 1. 出品者と購入者を作成する
     * 2. 取引中の商品を作成する
     * 3. 購入者が「取引を完了する」ボタンをクリックする
     *
     * 期待結果: 出品者宛にメール通知が送信される
     */
    public function test_seller_receives_email_when_buyer_completes_transaction()
    {
        Mail::fake();

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
        $response = $this->post("/transaction-chat/{$item->id}/complete");

        $response->assertRedirect();

        // 出品者宛にメールが送信されたことを確認
        Mail::assertSent(TransactionCompleteNotification::class, function ($mail) use ($seller, $buyer, $item) {
            return $mail->hasTo($seller->email) &&
                   $mail->seller->id === $seller->id &&
                   $mail->buyer->id === $buyer->id &&
                   $mail->item->id === $item->id;
        });
    }

    /**
     * テスト項目: 既に取引が完了している場合、メールは再送信されない
     * ID: FN016
     *
     * テストシナリオ:
     * 1. 出品者と購入者を作成する
     * 2. 既に取引完了済みの商品を作成する
     * 3. 購入者が再度「取引を完了する」ボタンをクリックしようとする
     *
     * 期待結果: メールは再送信されない
     */
    public function test_email_not_sent_when_transaction_already_completed()
    {
        Mail::fake();

        /** @var User $buyer */
        $buyer = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id, // 既に取引完了済み
            'sold_at' => now()->subDays(1),
        ]);

        $this->actingAs($buyer);

        // 取引完了処理を実行（既に完了済み）
        $response = $this->post("/transaction-chat/{$item->id}/complete");

        $response->assertRedirect();

        // メールが送信されていないことを確認
        Mail::assertNothingSent();
    }

    /**
     * テスト項目: 購入者以外が取引完了を試みた場合、メールは送信されない
     * ID: FN016
     *
     * テストシナリオ:
     * 1. 出品者と購入者、他のユーザーを作成する
     * 2. 取引中の商品を作成する
     * 3. 出品者が「取引を完了する」ボタンをクリックしようとする
     *
     * 期待結果: 403エラーが返され、メールは送信されない
     */
    public function test_email_not_sent_when_seller_tries_to_complete_transaction()
    {
        Mail::fake();

        /** @var User $buyer */
        $buyer = User::factory()->create();
        /** @var User $seller */
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null, // 取引中
        ]);

        $this->actingAs($seller);

        // 出品者が取引完了を試みる
        $response = $this->post("/transaction-chat/{$item->id}/complete");

        $response->assertStatus(403);

        // メールが送信されていないことを確認
        Mail::assertNothingSent();
    }

    /**
     * テスト項目: メール通知の内容が正しい
     * ID: FN015, FN016
     *
     * テストシナリオ:
     * 1. 出品者と購入者を作成する
     * 2. 取引中の商品を作成する
     * 3. 購入者が「取引を完了する」ボタンをクリックする
     *
     * 期待結果: メールの内容に出品者、購入者、商品情報が含まれている
     */
    public function test_email_contains_correct_information()
    {
        Mail::fake();

        /** @var User $buyer */
        $buyer = User::factory()->create([
            'name' => '購入者太郎',
            'email' => 'buyer@example.com',
        ]);
        $seller = User::factory()->create([
            'name' => '出品者花子',
            'email' => 'seller@example.com',
        ]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
            'name' => 'テスト商品',
            'price' => 10000,
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
        $response = $this->post("/transaction-chat/{$item->id}/complete");

        $response->assertRedirect();

        // メールの内容を確認
        Mail::assertSent(TransactionCompleteNotification::class, function ($mail) use ($seller, $buyer, $item) {
            /** @var TransactionCompleteNotification $mail */
            return $mail->hasTo($seller->email) &&
                   $mail->seller->id === $seller->id &&
                   $mail->buyer->id === $buyer->id &&
                   $mail->item->id === $item->id &&
                   $mail->seller->name === '出品者花子' &&
                   $mail->buyer->name === '購入者太郎' &&
                   $mail->item->name === 'テスト商品';
        });
    }
}