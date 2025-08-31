<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\CustomLoginResponse;
use App\Actions\Fortify\CustomRegisterResponse;
use App\Mail\VerifyEmailCustom;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // デフォルトのLoginRequestをカスタムLoginRequestに置き換え
        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        // ログインビューを設定
        Fortify::loginView(function () {
            return view('login');
        });

        // カスタム登録レスポンスを設定
        app()->singleton(\Laravel\Fortify\Contracts\RegisterResponse::class, CustomRegisterResponse::class);

        // カスタムログインレスポンスを設定
        app()->singleton(\Laravel\Fortify\Contracts\LoginResponse::class, CustomLoginResponse::class);

        // カスタムログアウトレスポンスを設定
        app()->singleton(\Laravel\Fortify\Contracts\LogoutResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\LogoutResponse {
                public function toResponse($request)
                {
                    return redirect('/login');
                }
            };
        });

        // カスタムメール認証メールを設定
        Fortify::verifyEmailView('verify-email');
    }
}