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
                @php
                    // 購入者の判定（評価済みの場合は完了ボタンを表示しない）
                    $canComplete = false;
                    if (!$hasRating) {
                        if ($item->buyer_id) {
                            // 購入済みの場合：buyer_idで判定（購入者のみがボタンを表示できる）
                            // ただし、決済処理済みの場合は取引完了ボタンは不要（評価モーダルを表示）
                            $canComplete = false; // 決済処理済みの場合は取引完了ボタンは表示しない
                        } else {
                            // 取引中の場合：出品者ではないユーザーが購入希望者として扱える
                            // つまり、購入者側の取引でも、現在のユーザーが出品者でなければ購入希望者として扱える
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
                                        onclick="editMessage({{ $message->id }}, '{{ addslashes($message->message ?? '') }}', {{ $message->image_path ? "'" . addslashes($message->image_path) . "'" : 'null' }})">編集</button>
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
                                placeholder="取引メッセージを記入してください" value="{{ old('message', '') }}">
                        </div>
                        <div class="image-input-wrapper">
                            <button class="chat-message-image-button button" type="button"
                                onclick="document.getElementById('chat-image-input').click()">画像を追加</button>
                            <input type="file" name="image" id="chat-image-input" class="chat-image-input">
                            <span id="selected-image-name"
                                style="margin-left: 10px; color: rgba(95, 95, 95, 1); font-size: 14px;"></span>
                        </div>
                        <button class="chat-message-send-button button" type="submit">
                            <img src="{{ asset('images/send-button.svg') }}" alt="送信">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // FN009: 入力情報保持機能 - localStorageを使用して入力内容を保持
        const messageInput = document.getElementById('message-input');
        const storageKey = 'transaction-chat-message-{{ $item->id }}';

        // ページ読み込み時に保存された値を復元（old()の値がない場合のみ）
        @if (!old('message'))
            const savedMessage = localStorage.getItem(storageKey);
            if (savedMessage) {
                messageInput.value = savedMessage;
            }
        @endif

        // 入力内容をlocalStorageに保存
        messageInput.addEventListener('input', function() {
            localStorage.setItem(storageKey, this.value);
        });

        // フォーム送信時にlocalStorageをクリア
        document.getElementById('message-form').addEventListener('submit', function() {
            localStorage.removeItem(storageKey);
        });

        function editMessage(messageId, messageText, imagePath) {
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
            messageInput.value = messageText || '';
            // localStorageも更新
            localStorage.setItem(storageKey, messageText || '');

            // 既存の画像がある場合、ファイル名を表示
            const imageNameSpan = document.getElementById('selected-image-name');
            const imageInput = document.getElementById('chat-image-input');
            if (imagePath) {
                // 画像パスからファイル名を取得
                const fileName = imagePath.split('/').pop();
                imageNameSpan.textContent = '現在の画像: ' + fileName;
                imageNameSpan.style.display = 'inline';
                // ファイル入力はクリア（新しい画像を選択可能にする）
                imageInput.value = '';
            } else {
                imageNameSpan.textContent = '';
                imageNameSpan.style.display = 'none';
            }

            // フォームにスクロール
            form.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        // 画像選択時にファイル名を表示
        document.getElementById('chat-image-input').addEventListener('change', function(e) {
            const imageNameSpan = document.getElementById('selected-image-name');
            if (this.files && this.files.length > 0) {
                imageNameSpan.textContent = '選択中: ' + this.files[0].name;
                imageNameSpan.style.display = 'inline';
            } else {
                imageNameSpan.textContent = '';
                imageNameSpan.style.display = 'none';
            }
        });

        // FN012, FN013: 評価モーダルの表示制御
        // 購入済みの商品のみモーダルを表示（取引中の商品では表示しない）
        @php
            $shouldShowModal = false;
            if ($item->buyer_id) {
                // 購入済みの商品の場合のみ、モーダルを表示
                if ((isset($showRatingModal) && $showRatingModal) || session('showRatingModal')) {
                    $shouldShowModal = true;
                }
            }
        @endphp
        @if ($shouldShowModal)
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('rating-modal').style.display = 'flex';
            });
        @endif

        // 評価モーダルを閉じる
        function closeRatingModal() {
            document.getElementById('rating-modal').style.display = 'none';
        }

        // モーダルの外側をクリックした時に閉じる
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('rating-modal');
            if (event.target === modal) {
                closeRatingModal();
            }
        });

        // 星評価の設定
        function setRating(rating) {
            document.getElementById('rating-input').value = rating;
            const stars = document.querySelectorAll('.rating-star');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        // 評価フォーム送信時のバリデーション
        document.querySelector('.rating-form')?.addEventListener('submit', function(e) {
            const rating = document.getElementById('rating-input').value;
            if (!rating || rating === '0') {
                e.preventDefault();
                alert('評価を選択してください');
                return false;
            }
        });
    </script>

    <!-- 評価モーダル -->
    <div id="rating-modal" class="rating-modal" style="display: none;">
        <div class="rating-modal-content">
            <h2 class="rating-modal-title">{{ $otherUser->name }} さんを評価してください</h2>
            <form action="{{ route('transaction.rating.store', ['item_id' => $item->id]) }}" method="POST"
                class="rating-form">
                @csrf
                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', '0') }}" required>

                <div class="rating-stars">
                    <span class="rating-star" onclick="setRating(1)" data-rating="1">★</span>
                    <span class="rating-star" onclick="setRating(2)" data-rating="2">★</span>
                    <span class="rating-star" onclick="setRating(3)" data-rating="3">★</span>
                    <span class="rating-star" onclick="setRating(4)" data-rating="4">★</span>
                    <span class="rating-star" onclick="setRating(5)" data-rating="5">★</span>
                </div>
                <p class="form__error">
                    @error('rating')
                        {{ $message }}
                    @enderror
                </p>

                @if (old('rating'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            setRating({{ old('rating') }});
                        });
                    </script>
                @endif

                <div class="rating-comment">
                    <label for="rating-comment-input">コメント（任意）</label>
                    <textarea id="rating-comment-input" name="comment" class="rating-comment-input" placeholder="コメントを入力してください"
                        maxlength="400">{{ old('comment') }}</textarea>
                    <p class="form__error">
                        @error('comment')
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="rating-modal-buttons">
                    <button type="button" class="rating-cancel-button button"
                        onclick="closeRatingModal()">キャンセル</button>
                    <button type="submit" class="rating-submit-button button">評価を送信</button>
                </div>
            </form>
        </div>
    </div>
@endsection
