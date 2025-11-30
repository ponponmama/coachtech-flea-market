{{-- 商品一覧画面（トップ） - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <div class="profile-image-section">
            <div class="profile-image-container">
                <div class="profile-image-placeholder">
                    @if ($user->profile && $user->profile->profile_image_path)
                        <img src="{{ asset('storage/' . $user->profile->profile_image_path) }}" alt="プロフィール画像"
                            class="profile-image-holder">
                    @endif
                </div>
                <div class="user-info">
                    <span class="user-name">{{ $user->name }}</span>
                    <div class="user-star-rating">
                        @php
                            $rating = $rating ?? 0; // コントローラーから渡された評価値（0-5）
                            // 評価がない場合は表示しない（$ratingが0の場合は何も表示しない）
                        @endphp
                        @if ($rating > 0)
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="user-star-icon {{ $i <= $rating ? 'has-rating' : '' }}">
                                    <img src="{{ asset('images/star.svg') }}" alt="star">
                                </span>
                            @endfor
                        @endif
                    </div>
                </div>
                <a href="{{ route('mypage.profile') }}" class="profile-edit-button button">プロフィールを編集</a>
            </div>
        </div>
        <nav class="nav-tabs">
            <ul class="nav-tabs__list">
                <li class="nav-tabs__item {{ $page !== 'buy' && $page !== 'trading' ? 'nav-tabs__item--active' : '' }}">
                    <a href="{{ route('mypage', ['page' => 'sell']) }}"
                        class="nav-tabs__link {{ $page !== 'buy' && $page !== 'trading' ? 'nav-tabs__link--active' : '' }}">出品した商品</a>
                </li>
                <li class="nav-tabs__item {{ $page === 'buy' ? 'nav-tabs__item--active' : '' }}">
                    <a href="{{ route('mypage', ['page' => 'buy']) }}"
                        class="nav-tabs__link {{ $page === 'buy' ? 'nav-tabs__link--active' : '' }}">購入した商品</a>
                </li>
                <li class="nav-tabs__item {{ $page === 'trading' ? 'nav-tabs__item--active' : '' }}">
                    <a href="{{ route('mypage', ['page' => 'trading']) }}"
                        class="nav-tabs__link {{ $page === 'trading' ? 'nav-tabs__link--active' : '' }}">
                        取引中の商品
                    </a>
                    @if ($tradingCount > 0)
                        <span class="trading-badge">{{ $tradingCount }}</span>
                    @endif
                </li>
            </ul>
        </nav>
        <p class="nav-tabs-border-line"></p>
        <div class="product-list">
            <div class="product-grid">
                @if ($page !== 'buy' && $page !== 'trading')
                    {{-- 出品した商品一覧（デフォルト） --}}
                    @if ($soldItems->count() > 0)
                        @foreach ($soldItems as $item)
                            <div class="product-item">
                                <div class="product-image">
                                    @if ($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                                            class="product-image-holder">
                                    @else
                                        <span class="product-image-placeholder">商品画像</span>
                                    @endif
                                    @if (!is_null($item->buyer_id))
                                        <div class="sold-badge">SOLD</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>出品した商品はありません。</p>
                    @endif
                @elseif ($page === 'buy')
                    {{-- 購入した商品一覧 --}}
                    @if ($purchasedItems->count() > 0)
                        @foreach ($purchasedItems as $item)
                            <div class="product-item">
                                <div class="product-image">
                                    @if ($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                                            class="product-image-holder">
                                    @else
                                        <span class="product-image-placeholder">商品画像</span>
                                    @endif
                                    {{-- 購入した商品にはSOLDバッジを表示しない --}}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>購入した商品はありません。</p>
                    @endif
                @elseif ($page === 'trading')
                    {{-- 取引中の商品一覧 --}}
                    @if ($tradingItems->count() > 0)
                        @foreach ($tradingItems as $item)
                            <div class="product-item">
                                <a href="{{ route('transaction.chat', ['item_id' => $item->id]) }}"
                                    class="product-image-link">
                                    <div class="product-image">
                                        @if ($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                                alt="{{ $item->name }}"
                                                class="product-image-holder trading-product-image-holder">
                                        @else
                                            <span class="product-image-placeholder">商品画像</span>
                                        @endif
                                        {{-- 取引中の商品にはSOLDバッジを表示しない --}}
                                        {{-- FN005: 取引商品新規通知確認機能 - 未読メッセージ数を表示 --}}
                                        @php
                                            $unreadCount = $item->unread_count ?? 0;
                                        @endphp
                                        @if ($unreadCount > 0)
                                            <div class="trading-notification-badge">{{ $unreadCount }}</div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <p>取引中の商品はありません。</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
