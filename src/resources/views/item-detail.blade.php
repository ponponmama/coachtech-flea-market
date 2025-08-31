{{-- 商品詳細画面 - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <div class="item-detail-section">
            <!-- 左側：商品画像 -->
            <div class="item-image-section">
                <div class="item-image-placeholder">
                    @if ($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="item-image">
                    @else
                        <span class="item-image-text">商品画像</span>
                    @endif
                </div>
            </div>

            <!-- 右側：商品詳細情報 -->
            <div class="item-info-section">
                <!-- 商品概要 -->
                <div class="item-overview">
                    <h1 class="item-name">{{ $item->name }}</h1>
                    <p class="item-brand">{{ $item->brand ?? 'ブランド名' }}</p>
                    <p class="item-price">¥{{ number_format($item->price) }} (税込)</p>

                    <!-- いいね・コメント数 -->
                    <div class="item-engagement">
                        <div class="engagement-item">
                            <span class="engagement-icon">★</span>
                            <span class="engagement-count">{{ $item->likes->count() }}</span>
                        </div>
                        <div class="engagement-item">
                            <span class="engagement-icon">💬</span>
                            <span class="engagement-count">{{ $item->comments->count() }}</span>
                        </div>
                    </div>

                    <!-- 購入ボタン -->
                    <a href="{{ route('purchase', $item->id) }}" class="purchase-button">購入手続きへ</a>
                </div>

                <!-- 商品説明 -->
                <div class="item-description-section">
                    <h2 class="section-title">商品説明</h2>
                    <div class="item-description">
                        <p>カラー:グレー</p>
                        <p>新品</p>
                        <p>{{ $item->description }}</p>
                        <p>購入後、即発送いたします。</p>
                    </div>
                </div>

                <!-- 商品情報 -->
                <div class="item-information-section">
                    <h2 class="section-title">商品の情報</h2>
                    <div class="item-information">
                        <div class="info-item">
                            <span class="info-label">カテゴリー</span>
                            <div class="category-tags">
                                @foreach ($item->categories as $category)
                                    <span class="category-tag">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">商品の状態</span>
                            <span class="info-value">{{ $item->condition }}</span>
                        </div>
                    </div>
                </div>

                <!-- コメントセクション -->
                <div class="comments-section">
                    <h2 class="section-title">コメント({{ $item->comments->count() }})</h2>

                    <!-- 既存コメント -->
                    @foreach ($item->comments as $comment)
                        <div class="comment-item">
                            <div class="comment-user">
                                <div class="user-avatar"></div>
                                <span class="user-name">{{ $comment->user->name }}</span>
                            </div>
                            <div class="comment-content">
                                {{ $comment->content }}
                            </div>
                        </div>
                    @endforeach

                    <!-- コメント投稿フォーム -->
                    <div class="comment-form-section">
                        <h3 class="form-title">商品へのコメント</h3>
                        <form action="{{ route('item.comment', $item->id) }}" method="POST" class="comment-form">
                            @csrf
                            <textarea name="comment" class="comment-input" placeholder="コメントを入力してください"></textarea>
                            <button type="submit" class="comment-submit-button">コメントを送信する</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
