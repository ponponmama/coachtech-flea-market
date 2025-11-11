{{-- 商品一覧画面（トップ） - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="content-container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <nav class="nav-tabs">
            <ul class="nav-tabs__list">
                <li class="nav-tabs__item {{ $tab !== 'mylist' ? 'nav-tabs__item--active' : '' }}">
                    <a href="{{ $search ? '/?search=' . urlencode($search) : '/' }}"
                        class="nav-tabs__link {{ $tab !== 'mylist' ? 'nav-tabs__link--active' : '' }} link">おすすめ</a>
                </li>
                <li class="nav-tabs__item {{ $tab === 'mylist' ? 'nav-tabs__item--active' : '' }}">
                    <a href="{{ '/?tab=mylist' . ($search ? '&search=' . urlencode($search) : '') }}"
                        class="nav-tabs__link {{ $tab === 'mylist' ? 'nav-tabs__link--active' : '' }} link">マイリスト</a>
                </li>
            </ul>
        </nav>
        <p class="nav-tabs-border-line"></p>
        <div class="product-list">
            <div class="product-grid">
                @forelse ($items as $item)
                    <div class="product-item">
                        <a href="{{ route('item.detail', $item->id) }}" class="product-image-link">
                            <div class="product-image">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                                        class="product-image__img">
                                @else
                                    <span class="product-image__placeholder">商品画像</span>
                                @endif
                                @if ($item->is_sold)
                                    <div class="sold-badge">SOLD</div>
                                @endif
                            </div>
                        </a>
                        <div class="product-name">
                            <a href="{{ route('item.detail', $item->id) }}" class="product-name-link">
                                <span class="product-name__text">
                                    @if ($item->name && trim($item->name) !== '')
                                        {{ $item->name }}
                                    @else
                                        <span class="product-name__placeholder">商品名</span>
                                    @endif
                                </span>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="no-items-message">
                        商品がありません。
                    </p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
