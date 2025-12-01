document.addEventListener('DOMContentLoaded', function() {
    // 商品画像用の処理
    const uploadFileInput = document.getElementById('upload-image');
    const imagePreview = document.getElementById('image-preview');

    if (uploadFileInput && imagePreview) {
        uploadFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // エラーメッセージをクリア
                const errorElement = document.querySelector('.form-section .form__error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // 既存のプレースホルダーを削除（元のinput要素は保持）
                    const placeholder = imagePreview.querySelector('.image-placeholder');
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }

                    // 既存のプレビュー画像を削除
                    const existingPreview = imagePreview.querySelector('.preview-image');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    // 画像プレビューを表示
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = '商品画像プレビュー';
                    img.className = 'preview-image';
                    imagePreview.appendChild(img);

                    // 元のinput要素はフォーム内に保持される（ファイルデータは元のinput要素に保持される）
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 出品画面の処理
    const sellForm = document.querySelector('.sell-form');
    if (sellForm) {

        // 価格入力の処理（数値のみ）
        const priceInput = document.getElementById('price');
        if (priceInput) {
            priceInput.addEventListener('input', function(e) {
                // 負の値を防ぐ
                if (e.target.value < 0) {
                    e.target.value = 0;
                }
            });
        }
    }
});
