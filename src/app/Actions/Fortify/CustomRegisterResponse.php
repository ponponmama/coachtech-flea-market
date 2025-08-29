<?php

namespace App\Actions\Fortify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class CustomRegisterResponse implements RegisterResponseContract
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

        // デバッグ情報をログに出力
        \Illuminate\Support\Facades\Log::info('CustomRegisterResponse - User:', [
            'user_id' => $user ? $user->id : null,
            'email_verified_at' => $user ? $user->email_verified_at : null,
            'is_first_login' => $user ? $user->is_first_login : null,
            'wants_json' => $request->wantsJson()
        ]);

        // メール認証が有効で、ユーザーがメール認証していない場合
        if ($user && !$user->email_verified_at) {
            \Illuminate\Support\Facades\Log::info('Redirecting to email verification notice');
            return $request->wantsJson()
                ? new JsonResponse(['redirect' => '/email/verification-notice'], 200)
                : redirect()->intended('/email/verification-notice');
        }

        // 初回ログインの場合はプロフィール設定画面に遷移
        if ($user && $user->is_first_login) {
            \Illuminate\Support\Facades\Log::info('Redirecting to profile setup');
            return $request->wantsJson()
                ? new JsonResponse(['redirect' => '/mypage/profile'], 200)
                : redirect()->intended('/mypage/profile');
        }

        // 通常の場合はホーム画面に遷移
        \Illuminate\Support\Facades\Log::info('Redirecting to home');
        return $request->wantsJson()
            ? new JsonResponse(['redirect' => '/'], 200)
            : redirect()->intended('/');
    }
}