{{-- 会員登録画面 /register --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <p class="form-title">
            会員登録
        </p>
        <form action="{{ route('register') }}" method="POST" class="register-form">
            @csrf
            <div class="form-group">
                <label class="form-label form-label-tight" for="name">
                    ユーザー名
                </label>
                <input class="form-input" type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name">
            </div>
            <p class="form__error">
                @error('name')
                    {{ $message }}
                @enderror
            </p>
            <div class="form-group form-group-margin">
                <label class="form-label" for="email">
                    メールアドレス
                </label>
                <input class="form-input form-input-tight" type="text" name="email" id="email" value="{{ old('email') }}" autocomplete="email">
            </div>
            <p class="form__error">
                @error('email')
                    {{ $message }}
                @enderror
            </p>
            <div class="form-group">
                <label class="form-label" for="password">
                    パスワード
                </label>
                <input class="form-input" type="password" name="password" id="password" autocomplete="new-password">
            </div>
            <p class="form__error">
                @error('password')
                    {{ $message }}
                @enderror
            </p>
            <div class="form-group form-group-margin">
                <label class="form-label form-label-tight" for="password_confirmation">
                    確認用パスワード
                </label>
                <input class="form-input form-input-tight" type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
            </div>
            <p class="form__error">
                @error('password_confirmation')
                    {{ $message }}
                @enderror
            </p>
            <button type="submit" class="submit-button button">
                登録する
            </button>
        </form>
        <a class="register-link link" href="{{ route('login') }}">
            ログインはこちら
        </a>
    </div>
@endsection
