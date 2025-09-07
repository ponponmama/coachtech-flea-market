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
                    <div class="item-header">
                        <p class="item-name">
                            {{ $item->name }}
                        </p>
                        <p class="item-brand">
                            {{ $item->brand ?? 'ブランド名' }}
                        </p>
                        <p class="item-price-section">
                            <span class="item-price">
                                ¥{{ number_format($item->price) }}
                            </span>
                            <span class="item-price-tax">(税込)</span>
                            </span>
                        </p>
                        <!-- いいね・コメント数 -->
                        <div class="item-engagement">
                            <div class="engagement-item">
                                <button class="like-button" data-item-id="{{ $item->id }}"
                                    data-liked="{{ Auth::check() && $item->likes->where('user_id', Auth::id())->count() > 0 ? 'true' : 'false' }}">
                                    <img src="{{ asset('images/star-icon.png') }}" alt="いいね" class="engagement-icon">
                                    <span class="engagement-count"> {{ $item->likes->count() }}</span>
                                </button>
                            </div>
                            <div class="engagement-item">
                                <img src="{{ asset('images/comment-icon.png') }}" alt="コメント" class="engagement-icon">
                                <span class="engagement-count">{{ $item->comments->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="item-purchase">
                        <!-- 購入ボタン -->
                        <a href="{{ route('purchase', $item->id) }}" class="purchase-link">購入手続きへ</a>
                    </div>
                </div>
                <!-- 商品説明 -->
                <div class="item-description-section">
                    <p class="description-section-title">商品説明</p>
                    <p class="item-description-text">
                        {{ $item->description }}
                    </p>
                </div>

                <!-- 商品情報 -->
                <div class="item-information-section">
                    <p class="information-section-title">商品の情報</p>
                    <div class="item-information">
                        <div class="category-info-item">
                            <span class="category-info-label">カテゴリー</span>
                            <div class="category-tags">
                                @foreach ($item->categories as $category)
                                    <span class="category-tag">{{ $category->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="condition-info-item">
                            <span class="condition-info-label">商品の状態</span>
                            <span class="info-value">
                                {{ $item->condition }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- コメントセクション -->
                <div class="comments-section">
                    <p class="comments-section-title">コメント({{ $item->comments->count() }})</p>
                    @foreach ($item->comments as $comment)
                        <div class="comment-item">
                            <span class="profile-image">
                                @if ($comment->user->profile_image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($comment->user->profile_image) }}"
                                        alt="プロフィール画像">
                                @endif
                            </span>
                            <span class="user-name">{{ $comment->user->name }}</span>
                            <div class="comment-content">
                                {{ $comment->content }}
                            </div>
                        </div>
                </div>
                @endforeach
                <!-- コメント投稿フォーム -->
                <div class="comment-form-section">
                    <p class="form-title">商品へのコメント</p>
                    <form action="{{ route('item.comment', $item->id) }}" method="POST" class="comment-form">
                        @csrf
                        <textarea name="comment" class="comment-input"></textarea>
                        <button type="submit" class="comment-submit-button">コメントを送信する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const likeButtons = document.querySelectorAll('.like-button');

            likeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const isLiked = this.dataset.liked === 'true';

                    // CSRFトークンを取得
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content');

                    fetch(`/item/${itemId}/like`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }

                            // いいね状態を更新
                            this.dataset.liked = data.isLiked;
                            this.querySelector('.engagement-count').textContent = data
                                .likeCount;

                            // ボタンのスタイルを更新
                            if (data.isLiked) {
                                this.classList.add('liked');
                            } else {
                                this.classList.remove('liked');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('エラーが発生しました');
                        });
                });
            });
        });
    </script>
@endsection
