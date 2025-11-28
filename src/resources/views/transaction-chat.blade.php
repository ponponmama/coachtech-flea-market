@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/transaction-chat.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <div class="title-container">
            <h1 class="content-title">その他の取引</h1>
        </div>
        <div class="information-container">
            <div class="profile-image-section">
                <div class="profile-image-placeholder" id="profile-image-display">
                    @if ($user->profile && $user->profile->profile_image_path)
                        <img src="{{ asset('storage/' . $user->profile->profile_image_path) }}" alt="プロフィール画像"
                            class="profile-image-holder">
                    @endif
                </div>
                <h2 class="transaction-partner-title">{{ $user->name }} さんとの取引画面</h2>
                <button class="transaction-complete-button button">取引を完了する</button>
            </div>
            <p class="transaction-border-line"></p>
            <div class="transaction-image-section">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="transaction-image">
                <div class="transaction-item-info">
                    <p class="transaction-item-name">{{ $item->name }}</p>
                    <p class="transaction-item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
            <p class="transaction-border-line"></p>
        </div>
    </div>
@endsection
