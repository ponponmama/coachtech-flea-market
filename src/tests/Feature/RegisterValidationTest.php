<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * テスト項目1: 名前が入力されていない場合のバリデーションテスト
     * ID: 1-1
     *
     * テストシナリオ:
     * 1. 会員登録ページを開く
     * 2. 名前を入力せずに他の必要項目を入力する
     * 3. 登録ボタンを押す
     *
     * 期待結果: 「お名前を入力してください」というバリデーションメッセージが表示される
     */
    public function test_register_name_required_validation()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');

        // 2. 名前を入力せずに他の必要項目を入力する
        $formData = [
            'name' => '', // 名前は空
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // 3. 登録ボタンを押す
        $response = $this->post('/register', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['name']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください'
        ]);

        // 登録ページにリダイレクトされることを確認
        $response->assertRedirect('/register');

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }

    /**
     * テスト項目2: メールアドレスが入力されていない場合のバリデーションテスト
     * ID: 1-2
     *
     * テストシナリオ:
     * 1. 会員登録ページを開く
     * 2. メールアドレスを入力せずに他の必要項目を入力する
     * 3. 登録ボタンを押す
     *
     * 期待結果: 「メールアドレスを入力してください」というバリデーションメッセージが表示される
     */
    public function test_register_email_required_validation()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');

        // 2. メールアドレスを入力せずに他の必要項目を入力する
        $formData = [
            'name' => 'テストユーザー',
            'email' => '', // メールアドレスは空
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // 3. 登録ボタンを押す
        $response = $this->post('/register', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['email']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);

        // 登録ページにリダイレクトされることを確認
        $response->assertRedirect('/register');

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'name' => 'テストユーザー'
        ]);
    }

    /**
     * テスト項目3: パスワードが入力されていない場合のバリデーションテスト
     * ID: 1-3
     *
     * テストシナリオ:
     * 1. 会員登録ページを開く
     * 2. パスワードを入力せずに他の必要項目を入力する
     * 3. 登録ボタンを押す
     *
     * 期待結果: 「パスワードを入力してください」というバリデーションメッセージが表示される
     */
    public function test_register_password_required_validation()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');

        // 2. パスワードを入力せずに他の必要項目を入力する
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '', // パスワードは空
            'password_confirmation' => 'password123',
        ];

        // 3. 登録ボタンを押す
        $response = $this->post('/register', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['password']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);

        // 登録ページにリダイレクトされることを確認
        $response->assertRedirect('/register');

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }

    /**
     * テスト項目4: パスワードが7文字以下の場合のバリデーションテスト
     * ID: 1-4
     *
     * テストシナリオ:
     * 1. 会員登録ページを開く
     * 2. 7文字以下のパスワードと他の必要項目を入力する
     * 3. 登録ボタンを押す
     *
     * 期待結果: 「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示される
     */
    public function test_register_password_min_length_validation()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');

        // 2. 7文字以下のパスワードと他の必要項目を入力する
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567', // 7文字（制限を下回る）
            'password_confirmation' => '1234567',
        ];

        // 3. 登録ボタンを押す
        $response = $this->post('/register', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['password']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください'
        ]);

        // 登録ページにリダイレクトされることを確認
        $response->assertRedirect('/register');

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }

    /**
     * テスト項目5: パスワードが確認用パスワードと一致しない場合のバリデーションテスト
     * ID: 1-5
     *
     * テストシナリオ:
     * 1. 会員登録ページを開く
     * 2. 確認用パスワードと異なるパスワードを入力し、他の必要項目も入力する
     * 3. 登録ボタンを押す
     *
     * 期待結果: 「パスワードと一致しません」というバリデーションメッセージが表示される
     */
    public function test_register_password_confirmation_validation()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');

        // 2. 確認用パスワードと異なるパスワードを入力し、他の必要項目も入力する
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123', // 異なるパスワード
        ];

        // 3. 登録ボタンを押す
        $response = $this->post('/register', $formData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['password']);

        // 特定のエラーメッセージが表示されることを確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません'
        ]);

        // 登録ページにリダイレクトされることを確認
        $response->assertRedirect('/register');

        // データベースにユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }

    /**
     * テスト項目6: 全ての項目が入力されている場合、会員情報が登録され、プロフィール設定画面に遷移される
     * ID: 1-6
     *
     * テストシナリオ:
     * 1. 会員登録ページを開く
     * 2. 全ての必要項目を正しく入力する
     * 3. 登録ボタンを押す
     *
     * 期待結果: 会員情報が登録され、プロフィール設定画面に遷移する
     */
    public function test_register_success_redirect_to_profile()
    {
        // 1. 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('register');

        // 2. 全ての必要項目を正しく入力する
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // 3. 登録ボタンを押す
        $response = $this->post('/register', $formData);

        // バリデーションエラーがないことを確認
        $response->assertSessionHasNoErrors();

        // データベースにユーザーが作成されることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'is_first_login' => true,
        ]);

        // メール認証ページにリダイレクトされることを確認
        $response->assertRedirect('/email/verify');
    }
}
