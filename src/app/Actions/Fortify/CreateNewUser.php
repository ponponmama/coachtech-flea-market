<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
        public function create(array $input): User
    {
        // RegisterRequestのバリデーションルールを使用
        $request = new RegisterRequest();
        $request->merge($input);

        $request->validate($request->rules(), $request->messages());

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'is_first_login' => true,
        ]);
    }
}