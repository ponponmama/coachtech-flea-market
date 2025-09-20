{{-- ログイン画面 /login --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <p class="form-title">ログイン</p>
        <p class="auth-error-message">
            @error('failed')
                {{ $message }}
            @enderror
        </p>
        <form action="{{ route('login') }}" method="POST" class="login-form">
            @csrf
            <div class="form-group  form-group-margin">
                <label class="form-label form-label-tight" for="email">
                    メールアドレス
                </label>
                <input class="form-input" type="text" name="email" id="email" value="{{ old('email') }}" autocomplete="email">
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
                <input class="form-input form-input-tight" type="password" name="password" id="password" autocomplete="current-password">
            </div>
            <p class="form__error">
                @error('password')
                    {{ $message }}
                @enderror
            </p>
            <div class="form-group">
                <button type="submit" class="submit-button button">
                    ログインする
                </button>
            </div>
        </form>
        <a class="login-link link" href="{{ route('register') }}">
            会員登録はこちら
        </a>
    </div>
@endsection
