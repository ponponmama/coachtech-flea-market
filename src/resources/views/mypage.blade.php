{{-- 商品一覧画面（トップ） - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <div class="profile-image-section">
            <div class="profile-image-container">
                <span class="profile-image-placeholder"></span>
                <span class="user-name">{{ $user->name }}</span>
                <a href="{{ route('mypage.profile') }}" class="profile-edit-button button">プロフィールを編集</a>
            </div>
        </div>
        <nav class="nav-tabs">
            <ul class="nav-tabs__list">
                <li class="nav-tabs__item link">
                    <a href="#" class="nav-tabs__link">出品した商品</a>
                </li>
                <li class="nav-tabs__item nav-tabs__item--active">
                    <a href="#" class="nav-tabs__link nav-tabs__link--active link">購入した商品</a>
                </li>
            </ul>
        </nav>
        <p class="nav-tabs-border-line"></p>
        <div class="product-list">
            <div class="product-grid">
                @if ($soldItems->count() > 0)
                    @foreach ($soldItems as $item)
                        <div class="product-item">
                            <div class="product-image">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                                        class="product-image__img">
                                @else
                                    <span class="product-image__placeholder"></span>
                                @endif
                            </div>
                            <div class="product-name">
                                <span class="product-name__text">{{ $item->name }}</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
