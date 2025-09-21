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
                    @else
                        <span class="profile-image-placeholder">画像</span>
                    @endif
                </div>
                <span class="user-name">{{ $user->name }}</span>
                <a href="{{ route('mypage.profile') }}" class="profile-edit-button button">プロフィールを編集</a>
            </div>
        </div>
        <nav class="nav-tabs">
            <ul class="nav-tabs__list">
                <li class="nav-tabs__item {{ $page !== 'buy' ? 'nav-tabs__item--active' : '' }}">
                    <a href="{{ route('mypage') }}"
                        class="nav-tabs__link {{ $page !== 'buy' ? 'nav-tabs__link--active' : '' }}">出品した商品</a>
                </li>
                <li class="nav-tabs__item {{ $page === 'buy' ? 'nav-tabs__item--active' : '' }}">
                    <a href="{{ route('mypage', ['page' => 'buy']) }}"
                        class="nav-tabs__link {{ $page === 'buy' ? 'nav-tabs__link--active' : '' }}">購入した商品</a>
                </li>
            </ul>
        </nav>
        <p class="nav-tabs-border-line"></p>
        <div class="product-list">
            <div class="product-grid">
                @if ($page === 'buy')
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
                                    @if (!is_null($item->buyer_id))
                                        <div class="sold-badge">SOLD</div>
                                    @endif
                                </div>
                                <div class="product-name">
                                    <span class="product-name-text">{{ $item->name }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>購入した商品はありません。</p>
                    @endif
                @else
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
                                <div class="product-name">
                                    <span class="product-name-text">{{ $item->name }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>出品した商品はありません。</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
