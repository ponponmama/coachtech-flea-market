@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/transaction-chat.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <div class="transaction-chat-container">
            <div class="title-container">
                <h1 class="content-title">その他の取引</h1>
            </div>
            <div class="profile-image-section">
                <div class="profile-image-placeholder" id="profile-image-display">
                    @if ($user->profile && $user->profile->profile_image_path)
                        <img src="{{ asset('storage/' . $user->profile->profile_image_path) }}" alt="プロフィール画像" class="profile-image-holder">
                    @endif
                </div>
                <h2 class="content-title">「ユーザー名」さんとの取引画面</h2>
                <button class="transaction-complete-button">取引を完了する</button>
            </div>
            <div class="transaction-image-container">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="transaction-image">
            </div>
        </div>
    </div>
@endsection
