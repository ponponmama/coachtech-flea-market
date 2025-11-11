<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\CustomAuthenticateUser;
use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;

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
        Fortify::loginView(function (Request $request) {
            return view('login', [
                'redirectUrl' => $request->query('redirect_url'),
            ]);
        });

        Fortify::registerView(function () {
            return view('register');
        });

        // FortifyのデフォルトLoginRequestをカスタムのものに置き換え
        app()->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );

        // カスタム認証アクションを設定
        Fortify::authenticateUsing(function (Request $request) {
            // LoginRequestのバリデーションを実行
            $loginRequest = new LoginRequest();
            $loginRequest->merge($request->all());

            $validator = Validator::make($request->all(), $loginRequest->rules(), $loginRequest->messages());

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            // ログイン失敗時
        throw ValidationException::withMessages([
        'failed' => 'ログイン情報が登録されていません'
        ]);
    });

        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録後のリダイレクト処理をカスタマイズ
        app()->singleton(\Laravel\Fortify\Contracts\RegisterResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\RegisterResponse {
                public function toResponse($request)
                {
                    // メール認証誘導画面に遷移
                    return redirect('/email/verify');
                }
            };
        });

        // カスタムログアウトレスポンスを設定
        app()->singleton(\Laravel\Fortify\Contracts\LogoutResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\LogoutResponse {
                public function toResponse($request)
                {
                    return redirect('/login');
                }
            };
        });

        // ログイン後のリダイレクト処理をカスタマイズ
        app()->singleton(\Laravel\Fortify\Contracts\LoginResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                public function toResponse($request)
                {
                    $user = auth()->user();

                    // 初回ログイン（メール認証後）の場合はprofileに遷移
                    if ($user && $user->is_first_login) {
                        // 初回ログインフラグを更新
                        User::where('id', $user->id)->update(['is_first_login' => false]);
                        return redirect('/mypage/profile');
                    }

                    $redirectUrl = $request->input('redirect_url');
                    if ($this->isSafeRedirectUrl($redirectUrl, $request)) {
                        return redirect($redirectUrl);
                    }

                    // 通常のログインはindex.blade.php（トップページ）に遷移
                    return redirect('/');
                }

                /**
                 * リダイレクトURLがアプリケーション内かを判定
                 */
                private function isSafeRedirectUrl(?string $url, $request): bool
                {
                    if (empty($url)) {
                        return false;
                    }

                    $parsed = parse_url($url);

                    if ($parsed === false) {
                        return false;
                    }

                    if (isset($parsed['host'])) {
                        $currentHost = $request->getHost();

                        if ($parsed['host'] !== $currentHost) {
                            return false;
                        }
                    }

                    if (!isset($parsed['path']) || strpos($parsed['path'], '/') !== 0) {
                        return false;
                    }

                    return true;
                }
            };
        });
    }
}
