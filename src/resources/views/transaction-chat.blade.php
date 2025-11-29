@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/transaction-chat.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <div class="title-container">
            <h1 class="content-title">その他の取引</h1>
            {{-- FN003: 別取引遷移機能 - サイドバーに他の取引中の商品一覧を表示 --}}
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
                            <p class="message-content">{{ $message->message }}</p>
                            @if ($message->image_path)
                                <img src="{{ asset('storage/' . $message->image_path) }}" alt="メッセージ画像"
                                    class="message-image">
                            @endif
                            @if ($message->sender_id === $user->id)
                                <div class="message-actions">
                                    <button type="button" class="message-edit-button button"
                                        onclick="editMessage({{ $message->id }}, '{{ addslashes($message->message) }}')">編集</button>
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
                <form id="message-form" action="{{ route('transaction.chat.send', ['item_id' => $item->id]) }}" method="POST" enctype="multipart/form-data" class="message-form">
                    @csrf
                    <input type="hidden" name="edit_message_id" id="edit-message-id" value="">
                    <input type="text" class="chat-message-input" id="message-input" name="message" placeholder="取引メッセージを記入してください" value="{{ old('message') }}">
                    <p class="form__error">
                        @error('message')
                            {{ $message }}
                        @enderror
                    </p>
                    <button class="chat-message-image-button button" type="button"
                        onclick="document.getElementById('chat-image-input').click()">画像を追加</button>
                    <input type="file" name="image" id="chat-image-input" class="chat-image-input"
                        accept="image/jpeg,image/png">
                    <p class="form__error">
                        @error('image')
                            {{ $message }}
                        @enderror
                    </p>
                    <button class="chat-message-send-button button" type="submit">
                        <img src="{{ asset('images/send-button.svg') }}" alt="送信">
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editMessage(messageId, messageText) {
            // フォームの送信先を編集用に変更
            const form = document.getElementById('message-form');
            // ルートURLを動的に構築
            form.action = '/transaction-message/' + messageId;

            // メソッドをPUTに変更
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            // メッセージIDを設定
            document.getElementById('edit-message-id').value = messageId;

            // メッセージ内容をフォームに表示
            document.getElementById('message-input').value = messageText;

            // フォームにスクロール
            form.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    </script>
@endsection
