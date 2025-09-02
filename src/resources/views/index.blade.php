{{-- 商品一覧画面（トップ） - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <nav class="nav-tabs">
            <ul class="nav-tabs__list">
                <li class="nav-tabs__item {{ $tab !== 'mylist' ? 'nav-tabs__item--active' : '' }}">
                    <a href="/"
                        class="nav-tabs__link {{ $tab !== 'mylist' ? 'nav-tabs__link--active' : '' }} link">おすすめ</a>
                </li>
                <li class="nav-tabs__item {{ $tab === 'mylist' ? 'nav-tabs__item--active' : '' }}">
                    <a href="/?tab=mylist"
                        class="nav-tabs__link {{ $tab === 'mylist' ? 'nav-tabs__link--active' : '' }} link">マイリスト</a>
                </li>
            </ul>
        </nav>
        <p class="nav-tabs-border-line"></p>
        <div class="product-list">
            <div class="product-grid">
                @forelse ($items as $item)
                    <div class="product-item">
                        <a href="{{ route('item.detail', $item->id) }}" class="product-link">
                            <div class="product-image">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="product-image__img">
                                @else
                                    <span class="product-image__placeholder">商品画像</span>
                                @endif
                                @if ($item->is_sold)
                                    <div class="sold-badge">SOLD</div>
                                @endif
                            </div>
                            <div class="product-name">
                                <span class="product-name__text">{{ $item->name }}</span>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="no-items">
                        <p>商品が見つかりませんでした。</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
