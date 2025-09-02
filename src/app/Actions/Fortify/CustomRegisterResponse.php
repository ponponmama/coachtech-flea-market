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
        // 登録完了後はメール認証画面にリダイレクト
        // メール認証が完了するまでユーザーは認証されない
        return $request->wantsJson()
            ? new JsonResponse(['redirect' => '/email/verify'], 200)
            : redirect()->intended('/email/verify');
    }
}