<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: ログイン済みのユーザーはコメントを送信できる
     * ID: 9-1
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. コメントを入力する
     * 3. コメントボタンを押す
     *
     * 期待結果: コメントが保存され、コメント数が増加する
     */
    public function test_authenticated_user_can_post_comment()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        $commentContent = 'これはテストコメントです。';

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => $commentContent
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'コメントを投稿しました。');

        // コメントがデータベースに保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => $commentContent
        ]);

        // コメント数が増加していることを確認
        $this->assertEquals(1, $item->comments()->count());
    }

    /**
     * テスト項目: ログイン前のユーザーはコメントを送信できない
     * ID: 9-2
     *
     * テストシナリオ:
     * 1. コメントを入力する
     * 2. コメントボタンを押す
     *
     * 期待結果: コメントが送信されない
     */
    public function test_guest_user_cannot_post_comment()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);

        $commentContent = 'これはテストコメントです。';

        // 未認証でコメント送信を試行
        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => $commentContent
        ]);

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect('/login');

        // コメントがデータベースに保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => $commentContent
        ]);
    }

    /**
     * テスト項目: コメントが入力されていない場合、バリデーションメッセージが表示される
     * ID: 9-3
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. コメントボタンを押す
     *
     * 期待結果: バリデーションメッセージが表示される
     */
    public function test_comment_required_validation()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => ''
        ]);

        $response->assertSessionHasErrors(['comment']);
        $response->assertSessionHasErrors(['comment' => 'コメントは必須です。']);

        // コメントがデータベースに保存されていないことを確認
        $this->assertEquals(0, $item->comments()->count());
    }

    /**
     * テスト項目: コメントが255字以上の場合、バリデーションメッセージが表示される
     * ID: 9-4
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 255文字以上のコメントを入力する
     * 3. コメントボタンを押す
     *
     * 期待結果: バリデーションメッセージが表示される
     */
    public function test_comment_max_length_validation()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'seller_id' => $user->id,
            'buyer_id' => null
        ]);

        $this->actingAs($user);

        // 256文字のコメントを作成
        $longComment = str_repeat('にゃんこ', 256);

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => $longComment
        ]);

        $response->assertSessionHasErrors(['comment']);
        $response->assertSessionHasErrors(['comment' => 'コメントは255文字以内で入力してください。']);

        // コメントがデータベースに保存されていないことを確認
        $this->assertEquals(0, $item->comments()->count());
    }
}
