<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\TransactionMessage;

class TransactionMessageEditTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * テスト項目: 投稿済みのメッセージを編集することができる
     * ID: FN010
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面でメッセージを送信する
     * 3. 送信したメッセージの「編集」ボタンをクリックする
     * 4. メッセージを編集して送信する
     *
     * 期待結果: メッセージが更新される
     */
    public function test_user_can_edit_transaction_message()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // メッセージを作成
        $message = TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
            'message' => '元のメッセージ',
            'is_read' => false,
        ]);

        $this->actingAs($user);

        $updatedMessage = '編集後のメッセージ';

        $response = $this->put("/transaction-message/{$message->id}", [
            'message' => $updatedMessage,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'メッセージを更新しました。');

        // メッセージが更新されていることを確認
        $this->assertDatabaseHas('transaction_messages', [
            'id' => $message->id,
            'message' => $updatedMessage,
        ]);
    }

    /**
     * テスト項目: 投稿済みのメッセージの画像を編集することができる
     * ID: FN010
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面で画像付きメッセージを送信する
     * 3. 送信したメッセージの「編集」ボタンをクリックする
     * 4. 新しい画像を選択して送信する
     *
     * 期待結果: 画像が更新される
     */
    public function test_user_can_edit_message_image()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // 元の画像付きメッセージを作成
        $oldImage = UploadedFile::fake()->create('old-image.jpg', 1024, 'image/jpeg');
        $oldImagePath = $oldImage->store('transaction-messages', 'public');

        $message = TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
            'message' => '画像付きメッセージ',
            'image_path' => $oldImagePath,
            'is_read' => false,
        ]);

        $this->actingAs($user);

        $newImage = UploadedFile::fake()->create('new-image.jpg', 1024, 'image/jpeg');

        $response = $this->put("/transaction-message/{$message->id}", [
            'message' => '画像付きメッセージ',
            'image' => $newImage,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'メッセージを更新しました。');

        // メッセージが更新されていることを確認
        $message->refresh();
        $this->assertNotNull($message->image_path);
        $this->assertNotEquals($oldImagePath, $message->image_path);
        $this->assertTrue(Storage::disk('public')->exists($message->image_path));
    }

    /**
     * テスト項目: 他のユーザーのメッセージを編集できない
     * ID: FN010
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 他のユーザーが送信したメッセージの編集を試みる
     *
     * 期待結果: 403エラーが返される
     */
    public function test_user_cannot_edit_other_users_message()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // 他のユーザーが送信したメッセージを作成
        $message = TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'message' => '他のユーザーのメッセージ',
            'is_read' => false,
        ]);

        $this->actingAs($user);

        $response = $this->put("/transaction-message/{$message->id}", [
            'message' => '編集しようとしたメッセージ',
        ]);

        $response->assertStatus(403);

        // メッセージが更新されていないことを確認
        $this->assertDatabaseHas('transaction_messages', [
            'id' => $message->id,
            'message' => '他のユーザーのメッセージ',
        ]);
    }

    /**
     * テスト項目: 投稿済みのメッセージを削除することができる
     * ID: FN011
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面でメッセージを送信する
     * 3. 送信したメッセージの「削除」ボタンをクリックする
     *
     * 期待結果: メッセージが削除される
     */
    public function test_user_can_delete_transaction_message()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // メッセージを作成
        $message = TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
            'message' => '削除されるメッセージ',
            'is_read' => false,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/transaction-message/{$message->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'メッセージを削除しました。');

        // メッセージが削除されていることを確認
        $this->assertDatabaseMissing('transaction_messages', [
            'id' => $message->id,
        ]);
    }

    /**
     * テスト項目: 画像付きメッセージを削除すると画像も削除される
     * ID: FN011
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面で画像付きメッセージを送信する
     * 3. 送信したメッセージの「削除」ボタンをクリックする
     *
     * 期待結果: メッセージと画像の両方が削除される
     */
    public function test_user_can_delete_message_with_image()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // 画像付きメッセージを作成
        $image = UploadedFile::fake()->create('test-image.jpg', 1024, 'image/jpeg');
        $imagePath = $image->store('transaction-messages', 'public');

        $message = TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
            'message' => '画像付きメッセージ',
            'image_path' => $imagePath,
            'is_read' => false,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/transaction-message/{$message->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success', 'メッセージを削除しました。');

        // メッセージが削除されていることを確認
        $this->assertDatabaseMissing('transaction_messages', [
            'id' => $message->id,
        ]);

        // 画像も削除されていることを確認
        $this->assertFalse(Storage::disk('public')->exists($imagePath));
    }

    /**
     * テスト項目: 他のユーザーのメッセージを削除できない
     * ID: FN011
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 他のユーザーが送信したメッセージの削除を試みる
     *
     * 期待結果: 403エラーが返される
     */
    public function test_user_cannot_delete_other_users_message()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        // 他のユーザーが送信したメッセージを作成
        $message = TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $seller->id,
            'receiver_id' => $user->id,
            'message' => '他のユーザーのメッセージ',
            'is_read' => false,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/transaction-message/{$message->id}");

        $response->assertStatus(403);

        // メッセージが削除されていないことを確認
        $this->assertDatabaseHas('transaction_messages', [
            'id' => $message->id,
        ]);
    }
}
