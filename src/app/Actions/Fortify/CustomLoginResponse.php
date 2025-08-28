<?php

namespace App\Actions\Fortify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = auth()->user();

        // メール認証が有効で、ユーザーがメール認証していない場合
        if ($user && !$user->email_verified_at) {
            return $request->wantsJson()
                ? new JsonResponse(['redirect' => '/email/verification-notice'], 200)
                : redirect()->intended('/email/verification-notice');
        }

        // 初回ログインの場合はプロフィール設定画面に遷移
        if ($user && $user->is_first_login) {
            return $request->wantsJson()
                ? new JsonResponse(['redirect' => '/mypage/profile'], 200)
                : redirect()->intended('/mypage/profile');
        }

        // 通常の場合はホーム画面に遷移
        return $request->wantsJson()
            ? new JsonResponse(['redirect' => '/'], 200)
            : redirect()->intended('/');
    }
}
