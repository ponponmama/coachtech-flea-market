document.addEventListener('DOMContentLoaded', function() {
    // すべてのカスタムセレクトボックスを処理
    const customSelects = document.querySelectorAll('.custom-select');

    customSelects.forEach(customSelect => {
        const trigger = customSelect.querySelector('.custom-select-trigger');
        const options = customSelect.querySelectorAll('.custom-option');
        const valueDisplay = customSelect.querySelector('.custom-select-value');

        // 隠しinputを取得（セレクトボックスの近くにあるinput要素を探す）
        const hiddenInput = customSelect.parentElement.querySelector('input[type="hidden"]');

        // セレクトボックスを開閉
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            customSelect.classList.toggle('open');
        });

        // オプションを選択
        options.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();

                // すべてのオプションからselectedクラスを削除し、チェックマークを隠す
                options.forEach(opt => {
                    opt.classList.remove('selected');
                    const checkmark = opt.querySelector('.check-mark');
                    if (checkmark) {
                        // 画像がある場合は削除、ない場合は空にする
                        const img = checkmark.querySelector('img');
                        if (img) {
                            img.remove();
                        } else {
                            checkmark.textContent = '';
                        }
                    }
                });

                // 選択されたオプションにselectedクラスを追加し、チェックマークを表示
                this.classList.add('selected');
                const checkmark = this.querySelector('.check-mark');
                if (checkmark) {
                    // 画像がない場合のみ追加
                    const img = checkmark.querySelector('img');
                    if (!img) {
                        const imgElement = document.createElement('img');
                        imgElement.src = '/images/stroke.png';
                        imgElement.alt = 'チェック';
                        checkmark.appendChild(imgElement);
                    }
                }

                // 表示テキストを更新
                const optionText = this.querySelector('.option-text');
                valueDisplay.textContent = optionText ? optionText.textContent : this.textContent;

                // 隠しinputの値を更新
                if (hiddenInput) {
                    hiddenInput.value = this.getAttribute('data-value');

                    // changeイベントを手動で発火
                    const changeEvent = new Event('change', { bubbles: true });
                    hiddenInput.dispatchEvent(changeEvent);
                }

                // 0.5秒後にセレクトボックスを閉じる
                setTimeout(() => {
                    customSelect.classList.remove('open');
                }, 500);
            });
        });

        // セレクトボックス外をクリックしたら閉じる
        document.addEventListener('click', function() {
            customSelect.classList.remove('open');
        });
    });
});
