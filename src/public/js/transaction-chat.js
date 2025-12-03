// FN009: 入力情報保持機能 - localStorageを使用して入力内容を保持
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('message-input');
    const messageForm = document.getElementById('message-form');
    const storageKeyElement = document.getElementById('storage-key');

    if (!messageInput || !messageForm || !storageKeyElement) {
        return;
    }

    const storageKey = storageKeyElement.value;
    const hasOldMessage = messageInput.dataset.hasOldMessage === 'true';

    // ページ読み込み時に保存された値を復元（old()の値がない場合のみ）
    if (!hasOldMessage) {
        const savedMessage = localStorage.getItem(storageKey);
        if (savedMessage) {
            messageInput.value = savedMessage;
        }
    }

    // 入力内容をlocalStorageに保存
    messageInput.addEventListener('input', function() {
        localStorage.setItem(storageKey, this.value);
    });

    // フォーム送信時にlocalStorageをクリア
    messageForm.addEventListener('submit', function() {
        localStorage.removeItem(storageKey);
    });

    // 画像選択時にファイル名を表示
    const imageInput = document.getElementById('chat-image-input');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const imageNameSpan = document.getElementById('selected-image-name');
            if (this.files && this.files.length > 0) {
                imageNameSpan.textContent = '選択中: ' + this.files[0].name;
                imageNameSpan.style.display = 'inline';
            } else {
                imageNameSpan.textContent = '';
                imageNameSpan.style.display = 'none';
            }
        });
    }
});

function editMessage(messageId, messageText, imagePath) {
    // フォームの送信先を編集用に変更
    const form = document.getElementById('message-form');
    const storageKeyElement = document.getElementById('storage-key');

    if (!form || !storageKeyElement) {
        return;
    }

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
    const editMessageIdInput = document.getElementById('edit-message-id');
    if (editMessageIdInput) {
        editMessageIdInput.value = messageId;
    }

    // メッセージ内容をフォームに表示
    const messageInput = document.getElementById('message-input');
    if (messageInput) {
        messageInput.value = messageText || '';
        // localStorageも更新
        const storageKey = storageKeyElement.value;
        localStorage.setItem(storageKey, messageText || '');
    }

    // 既存の画像がある場合、ファイル名を表示
    const imageNameSpan = document.getElementById('selected-image-name');
    const imageInput = document.getElementById('chat-image-input');
    if (imagePath) {
        // 画像パスからファイル名を取得
        const fileName = imagePath.split('/').pop();
        if (imageNameSpan) {
            imageNameSpan.textContent = '現在の画像: ' + fileName;
            imageNameSpan.style.display = 'inline';
        }
        // ファイル入力はクリア（新しい画像を選択可能にする）
        if (imageInput) {
            imageInput.value = '';
        }
    } else {
        if (imageNameSpan) {
            imageNameSpan.textContent = '';
            imageNameSpan.style.display = 'none';
        }
    }

    // フォームにスクロール
    form.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
    });
}

// FN012, FN013: 評価モーダルの表示制御
document.addEventListener('DOMContentLoaded', function() {
    const shouldShowModalElement = document.getElementById('should-show-rating-modal');
    const oldRatingElement = document.getElementById('old-rating');

    if (shouldShowModalElement && shouldShowModalElement.value === '1') {
        const ratingModal = document.getElementById('rating-modal');
        if (ratingModal) {
            ratingModal.style.display = 'flex';
        }
    }

    // old('rating')がある場合、星評価を設定
    if (oldRatingElement && oldRatingElement.value && oldRatingElement.value !== '0') {
        const rating = parseInt(oldRatingElement.value);
        if (rating > 0 && rating <= 5) {
            setRating(rating);
        }
    }
});

// 評価モーダルを閉じる
function closeRatingModal() {
    const modal = document.getElementById('rating-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// モーダルの外側をクリックした時に閉じる
document.addEventListener('click', function(event) {
    const modal = document.getElementById('rating-modal');
    if (modal && event.target === modal) {
        closeRatingModal();
    }
});

// 星評価の設定
function setRating(rating) {
    const ratingInput = document.getElementById('rating-input');
    if (ratingInput) {
        ratingInput.value = rating;
    }

    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

