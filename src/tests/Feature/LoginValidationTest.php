<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目1: メールアドレスが入力されていない場合のバリデーションテスト
     *
     * テストシナリオ:
     * 1. ログインページを開く
     * 2. メールアドレスを入力せずに他の必要項目を入力する
     * 3. ログインボタンを押す
     *
     * 期待結果: 「メールアドレスを入力してください」というバリデーションメッセージが表示される
     */
    public function test_login_email_required_validation()
    {
        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('login');

        // 2. メールアドレスを入力せずに他の必要項目を入力する
        $formData = [
            'email' => '', // メールアドレスは空
            'password' => 'password123',
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['email']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect('/login');
    }

    /**
     * テスト項目2: パスワードが入力されていない場合のバリデーションテスト
     *
     * テストシナリオ:
     * 1. ログインページを開く
     * 2. パスワードを入力せずに他の必要項目を入力する
     * 3. ログインボタンを押す
     *
     * 期待結果: 「パスワードを入力してください」というバリデーションメッセージが表示される
     */
    public function test_login_password_required_validation()
    {
        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('login');

        // 2. パスワードを入力せずに他の必要項目を入力する
        $formData = [
            'email' => 'test@example.com',
            'password' => '', // パスワードは空
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['password']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect('/login');
    }

    /**
     * テスト項目3: 入力情報が間違っている場合のバリデーションテスト
     *
     * テストシナリオ:
     * 1. ログインページを開く
     * 2. 必要項目を登録されていない情報を入力する
     * 3. ログインボタンを押す
     *
     * 期待結果: 「ログイン情報が登録されていません」というバリデーションメッセージが表示される
     */
    public function test_login_invalid_credentials_validation()
    {
        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('login');

        // 2. 必要項目を登録されていない情報を入力する
        $formData = [
            'email' => 'nonexistent@example.com', // 存在しないメールアドレス
            'password' => 'wrongpassword', // 間違ったパスワード
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['failed']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'failed' => 'ログイン情報が登録されていません'
        ]);

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect('/login');
    }

    /**
     * テスト項目4: 正しい情報が入力された場合、ログイン処理が実行される
     *
     * テストシナリオ:
     * 1. ログインページを開く
     * 2. 全ての必要項目を正しく入力する
     * 3. ログインボタンを押す
     *
     * 期待結果: ログイン処理が実行される
     */
    public function test_login_success()
    {
        // テスト用のユーザーを作成
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 1. ログインページを開く
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('login');

        // 2. 全ての必要項目を正しく入力する
        $formData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // バリデーションエラーがないことを確認
        $response->assertSessionHasNoErrors();

        // ログインが成功し、適切なページにリダイレクトされることを確認
        $response->assertRedirect('/');

        // ユーザーが認証されていることを確認
        $this->assertAuthenticatedAs($user);
    }
}