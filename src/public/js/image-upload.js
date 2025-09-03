document.addEventListener('DOMContentLoaded', function() {
    // プロフィール画像用の処理
    const profileFileInput = document.getElementById('profile-image');
    const profileImageDisplay = document.getElementById('profile-image-display');

    if (profileFileInput && profileImageDisplay) {
        profileFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // エラーメッセージをクリア
                const errorElement = document.querySelector('.profile-image-section .form__error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // 既存の画像またはテキストを削除
                    profileImageDisplay.innerHTML = '';

                    // 新しい画像を作成
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'プロフィール画像';
                    img.className = 'profile-image-holder';

                    profileImageDisplay.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }

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
                    // 既存のプレースホルダーを削除
                    imagePreview.innerHTML = '';

                    // 新しい画像を作成
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = '商品画像プレビュー';
                    img.className = 'preview-image';

                    imagePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
