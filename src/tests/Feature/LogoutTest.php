<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目: ログアウトができる
     *
     * テストシナリオ:
     * 1. ユーザーにログインをする
     * 2. ログアウトボタンを押す
     *
     * 期待結果: ログアウト処理が実行される
     */
    public function test_logout_success()
    {
        // テスト用のユーザーを作成
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 1. ユーザーにログインをする
        $this->actingAs($user);

        // ユーザーが認証されていることを確認
        $this->assertAuthenticatedAs($user);

        // 2. ログアウトボタンを押す
        $response = $this->post('/logout');

        // ログアウトが成功し、適切なページにリダイレクトされることを確認
        $response->assertRedirect('/login');

        // ユーザーが認証されていないことを確認
        $this->assertGuest();
    }
}