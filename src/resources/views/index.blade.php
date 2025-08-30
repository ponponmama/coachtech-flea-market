{{-- 商品一覧画面（トップ） - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <nav class="nav-tabs">
            <ul class="nav-tabs__list">
                <li class="nav-tabs__item link">
                    <a href="#" class="nav-tabs__link">おすすめ</a>
                </li>
                <li class="nav-tabs__item nav-tabs__item--active">
                    <a href="#" class="nav-tabs__link nav-tabs__link--active link">マイリスト</a>
                </li>
            </ul>
        </nav>
        <p class="nav-tabs-border-line"></p>
        <div class="product-list">
            <div class="product-grid">
                <div class="product-item">
                    <div class="product-image">
                        <span class="product-image__placeholder">商品画像</span>
                    </div>
                    <div class="product-name">
                        <span class="product-name__text">商品名</span>
                    </div>
                </div>
                <div class="product-item">
                    <div class="product-image">
                        <span class="product-image__placeholder">商品画像</span>
                    </div>
                    <div class="product-name">
                        <span class="product-name__text">商品名</span>
                    </div>
                </div>
                <div class="product-item">
                    <div class="product-image">
                        <span class="product-image__placeholder">商品画像</span>
                    </div>
                    <div class="product-name">
                        <span class="product-name__text">商品名</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
