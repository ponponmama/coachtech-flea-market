<?php

namespace App\Providers;

use App\Actions\Fortify\AuthenticateUser;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\CustomLoginResponse;
use App\Actions\Fortify\CustomRegisterResponse;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        // カスタム認証アクションを設定
        Fortify::authenticateUsing([AuthenticateUser::class, 'authenticate']);



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


    }
}
