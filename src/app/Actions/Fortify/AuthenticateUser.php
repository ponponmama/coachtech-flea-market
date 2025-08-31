<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class AuthenticateUser
{
    /**
     * Validate and authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function authenticate(Request $request)
    {
        // LoginRequestのバリデーションルールを使用
        $loginRequest = new LoginRequest();
        $loginRequest->merge($request->all());

        $loginRequest->validate($loginRequest->rules(), $loginRequest->messages());

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }

        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }
}
