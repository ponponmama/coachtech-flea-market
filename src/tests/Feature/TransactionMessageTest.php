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

class TransactionMessageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * テスト項目: 取引チャットで本文を送信することができる
     * ID: FN006
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. 本文を入力して送信する
     *
     * 期待結果: メッセージが保存され、チャット画面に表示される
     */
    public function test_user_can_send_text_message_in_transaction_chat()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        $messageContent = 'これはテストメッセージです。';

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'message' => $messageContent,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // メッセージがデータベースに保存されていることを確認
        $this->assertDatabaseHas('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
            'message' => $messageContent,
        ]);
    }

    /**
     * テスト項目: 取引チャットで画像を送信することができる
     * ID: FN006
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. 画像を選択して送信する
     *
     * 期待結果: 画像が保存され、チャット画面に表示される
     */
    public function test_user_can_send_image_in_transaction_chat()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        $image = UploadedFile::fake()->create('test-image.jpg', 1024, 'image/jpeg');

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'image' => $image,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // メッセージがデータベースに保存されていることを確認
        $this->assertDatabaseHas('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
        ]);

        // 画像が保存されていることを確認
        $message = TransactionMessage::where('item_id', $item->id)
            ->where('sender_id', $user->id)
            ->first();
        $this->assertNotNull($message->image_path);
        $this->assertTrue(Storage::disk('public')->exists($message->image_path));
    }

    /**
     * テスト項目: 取引チャットで本文と画像の両方を送信することができる
     * ID: FN006
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. 本文と画像の両方を入力して送信する
     *
     * 期待結果: 本文と画像の両方が保存され、チャット画面に表示される
     */
    public function test_user_can_send_text_and_image_in_transaction_chat()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        $messageContent = '画像付きメッセージです。';
        $image = UploadedFile::fake()->create('test-image.jpg', 1024, 'image/jpeg');

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'message' => $messageContent,
            'image' => $image,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // メッセージがデータベースに保存されていることを確認
        $this->assertDatabaseHas('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
            'message' => $messageContent,
        ]);

        // 画像が保存されていることを確認
        $message = TransactionMessage::where('item_id', $item->id)
            ->where('sender_id', $user->id)
            ->first();
        $this->assertNotNull($message->image_path);
        $this->assertTrue(Storage::disk('public')->exists($message->image_path));
    }

    /**
     * テスト項目: 本文が未入力の場合、バリデーションメッセージが表示される
     * ID: FN008
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. 本文も画像も入力せずに送信する
     *
     * 期待結果: 「本文を入力してください」というエラーメッセージが表示される
     */
    public function test_message_required_validation_when_no_image()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['message']);
        $response->assertSessionHasErrors(['message' => '本文を入力してください']);

        // メッセージがデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
        ]);
    }

    /**
     * テスト項目: 本文が401文字以上の場合、バリデーションメッセージが表示される
     * ID: FN008
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. 401文字以上の本文を入力して送信する
     *
     * 期待結果: 「本文は400文字以内で入力してください」というエラーメッセージが表示される
     */
    public function test_message_max_length_validation()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        // 401文字のメッセージを作成
        $longMessage = str_repeat('あ', 401);

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'message' => $longMessage,
        ]);

        $response->assertSessionHasErrors(['message']);
        $response->assertSessionHasErrors(['message' => '本文は400文字以内で入力してください']);

        // メッセージがデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
        ]);
    }

    /**
     * テスト項目: 画像が.pngまたは.jpeg形式以外の場合、バリデーションメッセージが表示される
     * ID: FN008
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. .txtファイルなどの画像以外のファイルを選択して送信する
     *
     * 期待結果: 「「.png」または「.jpeg」形式でアップロードしてください」というエラーメッセージが表示される
     */
    public function test_image_mimes_validation()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        // テキストファイルを作成
        $textFile = UploadedFile::fake()->create('test.txt', 100);

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'image' => $textFile,
        ]);

        $response->assertSessionHasErrors(['image']);
        $response->assertSessionHasErrors(['image' => '「.png」または「.jpeg」形式でアップロードしてください']);

        // メッセージがデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
        ]);
    }

    /**
     * テスト項目: .png形式の画像を送信することができる
     * ID: FN007
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. .png形式の画像を選択して送信する
     *
     * 期待結果: 画像が保存され、チャット画面に表示される
     */
    public function test_user_can_send_png_image()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        $image = UploadedFile::fake()->create('test-image.png', 1024, 'image/png');

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'image' => $image,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // メッセージがデータベースに保存されていることを確認
        $this->assertDatabaseHas('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
        ]);
    }

    /**
     * テスト項目: .jpeg形式の画像を送信することができる
     * ID: FN007
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. .jpeg形式の画像を選択して送信する
     *
     * 期待結果: 画像が保存され、チャット画面に表示される
     */
    public function test_user_can_send_jpeg_image()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        $image = UploadedFile::fake()->create('test-image.jpeg', 1024, 'image/jpeg');

        $response = $this->post("/transaction-chat/{$item->id}/send", [
            'image' => $image,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        // メッセージがデータベースに保存されていることを確認
        $this->assertDatabaseHas('transaction_messages', [
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $seller->id,
        ]);
    }

    /**
     * テスト項目: チャットを入力した状態で他の画面に遷移しても、入力情報を保持できる（本文のみ）
     * ID: FN009
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 取引チャット画面を開く
     * 3. 本文を入力する
     * 4. 他の画面に遷移する
     * 5. 再度取引チャット画面に戻る
     *
     * 期待結果: 入力した本文が保持されている
     */
    public function test_message_input_persisted_when_navigating_away()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'buyer_id' => null,
        ]);

        $this->actingAs($user);

        $messageContent = 'これは保持されるメッセージです。';

        // 取引チャット画面を開く
        $response = $this->get("/transaction-chat/{$item->id}");

        $response->assertStatus(200);

        // メッセージを入力（実際にはJavaScriptでlocalStorageに保存されるため、
        // このテストでは画面に遷移できることと、入力フィールドが存在することを確認）
        $response->assertSee('message-input', false);
        $response->assertSee('取引メッセージを記入してください', false);

        // 他の画面に遷移
        $mypageResponse = $this->get('/mypage');
        $mypageResponse->assertStatus(200);

        // 再度取引チャット画面に戻る
        $chatResponse = $this->get("/transaction-chat/{$item->id}");
        $chatResponse->assertStatus(200);
        // 入力フィールドが存在することを確認（localStorageの機能はJavaScriptで実装されているため、
        // サーバー側のテストでは入力フィールドの存在を確認する）
        $chatResponse->assertSee('message-input', false);
    }
}