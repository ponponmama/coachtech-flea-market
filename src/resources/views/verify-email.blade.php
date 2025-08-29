{{-- メール認証誘導画面（一般ユーザー） /email/verify --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify_email.css') }}">
@endsection

@section('title', 'メール認証')

@section('content')
    <div class="auth-container">
        <div class="auth-content">
            <p class="auth-message">
                登録していただいたメールアドレスに認証メールを送付しました。<br>
                メール認証を完了してください。
            </p>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
                <div class="form-group">
                    <a href="https://mailtrap.io" target="_blank" class="verify-email-button button">
                        認証はこちらから
                    </a>
                </div>
                <form method="POST" action="{{ route('verification.send') }}" class="resend-email-form">
                    @csrf
                    <button type="submit" class="resend-email-button button">
                        認証メールを再送する
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
