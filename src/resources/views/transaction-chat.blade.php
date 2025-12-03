@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/transaction-chat.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/transaction-chat.js') }}"></script>
@endsection

@section('content')
    <div class="content-container">
        <div class="title-container">
            <h1 class="content-title">その他の取引</h1>
            @if (isset($otherTradingItems) && $otherTradingItems->count() > 0)
                <div class="other-transactions-list">
                    @foreach ($otherTradingItems as $otherItem)
                        <a href="{{ route('transaction.chat', ['item_id' => $otherItem->id]) }}"
                            class="other-transaction-item">
                            <p class="other-transaction-name">{{ $otherItem->name }}</p>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="no-other-transactions">他の取引はありません</p>
            @endif
        </div>
        <div class="information-container">
            <div class="profile-image-section">
                <div class="profile-image-placeholder" id="profile-image-display">
                    @if ($user->profile && $user->profile->profile_image_path)
                        <img src="{{ asset('storage/' . $user->profile->profile_image_path) }}" alt="プロフィール画像"
                            class="profile-image-holder">
                    @endif
                </div>
                <h2 class="transaction-partner-title">{{ $otherUser->name }} さんとの取引画面</h2>
                @php
                    $canComplete = false;
                    if (!$hasRating) {
                        if ($item->buyer_id) {
                            $canComplete = $user->id === $item->buyer_id;
                        } else {
                            $canComplete = $user->id !== $item->seller_id;
                        }
                    }
                @endphp
                @if ($canComplete)
                    <form action="{{ route('transaction.complete', ['item_id' => $item->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="transaction-complete-button button">取引を完了する</button>
                    </form>
                @endif
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
            {{-- メッセージ表示エリア --}}
            <div class="message-area">
                @foreach ($messages as $message)
                    <div class="message-item {{ $message->sender_id === $user->id ? 'sent' : 'received' }}">
                        <div class="message-bubble">
                            <div class="user-profile-container">
                                <span class="user-profile-placeholder">
                                    @php
                                        $messageSender = $message->sender;
                                        $messageSenderProfile = $messageSender->profile;
                                    @endphp
                                    @if ($messageSenderProfile && $messageSenderProfile->profile_image_path)
                                        <img src="{{ asset('storage/' . $messageSenderProfile->profile_image_path) }}"
                                            alt="プロフィール画像" class="user-profile-image-holder">
                                    @endif
                                </span>
                                <p class="user-name">{{ $messageSender->name }}</p>
                            </div>
                            @if ($message->message)
                                <p class="message-content">{{ $message->message }}</p>
                            @endif
                            @if ($message->image_path)
                                <img src="{{ asset('storage/' . $message->image_path) }}" alt="メッセージ画像"
                                    class="message-image">
                            @endif
                            @if ($message->sender_id === $user->id)
                                <div class="message-actions">
                                    <button type="button" class="message-edit-button button"
                                        onclick="editMessage({{ $message->id }}, {!! json_encode($message->message ?? '') !!}, {!! json_encode($message->image_path) !!})">編集</button>
                                    <form
                                        action="{{ route('transaction.message.delete', ['message_id' => $message->id]) }}"
                                        method="POST" class="message-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="message-delete-button button">削除</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="message-form-container">
                <form id="message-form" action="{{ route('transaction.chat.send', ['item_id' => $item->id]) }}"
                    method="POST" enctype="multipart/form-data" class="message-form">
                    @csrf
                    <input type="hidden" name="edit_message_id" id="edit-message-id" value="">
                    <input type="hidden" id="storage-key" value="transaction-chat-message-{{ $item->id }}">
                    <input type="hidden" id="should-show-rating-modal"
                        value="{{ ($showRatingModal ?? false) ? '1' : '0' }}">
                    <input type="hidden" id="old-rating" value="{{ old('rating', '0') }}">
                    <div class="form-errors">
                        <p class="form__error">
                            @error('message')
                                {{ $message }}
                            @enderror
                        </p>
                        <p class="form__error">
                            @error('image')
                                {{ $message }}
                            @enderror
                        </p>
                    </div>
                    <div class="form-inputs-row">
                        <div class="message-input-wrapper">
                            <input type="text" class="chat-message-input" id="message-input" name="message"
                                placeholder="取引メッセージを記入してください" value="{{ old('message', '') }}"
                                data-has-old-message="{{ old('message') ? 'true' : 'false' }}">
                        </div>
                        <div class="image-input-wrapper">
                            <button class="chat-message-image-button button" type="button"
                                onclick="document.getElementById('chat-image-input').click()">画像を追加</button>
                            <input type="file" name="image" id="chat-image-input" class="chat-image-input">
                            <span id="selected-image-name"></span>
                        </div>
                        <button class="chat-message-send-button button" type="submit">
                            <img class="chat-message-send-button-image" src="{{ asset('images/send-button.svg') }}"
                                alt="送信">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- 評価モーダル -->
    <div id="rating-modal" class="rating-modal" style="display: none;">
        <div class="rating-modal-content">
            <p class="rating-modal-title">取引が完了しました<span class="rating-modal-title-dot">。</span></p>
            <p class="rating-modal-line-top"></p>
            <p class="rating-modal-subtitle">今回の取引相手はどうでしたか？</p>
            <form action="{{ route('transaction.rating.store', ['item_id' => $item->id]) }}" method="POST"
                class="rating-form">
                @csrf
                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', '0') }}">

                <div class="rating-stars">
                    <span class="rating-star" onclick="setRating(1)" data-rating="1"></span>
                    <span class="rating-star" onclick="setRating(2)" data-rating="2"></span>
                    <span class="rating-star" onclick="setRating(3)" data-rating="3"></span>
                    <span class="rating-star" onclick="setRating(4)" data-rating="4"></span>
                    <span class="rating-star" onclick="setRating(5)" data-rating="5"></span>
                </div>
                <p class="rating-modal-line-bottom"></p>
                @error('rating')
                    <p class="form__error">
                        {{ $message }}
                    </p>
                @enderror


                <div class="rating-modal-buttons">
                    <button type="submit" class="rating-submit-button button">送信する</button>
                </div>
            </form>
        </div>
    </div>
@endsection
