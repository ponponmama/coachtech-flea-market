document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile-image');
    const imageDisplay = document.getElementById('profile-image-display');

    if (fileInput && imageDisplay) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // 既存の画像またはテキストを削除
                    imageDisplay.innerHTML = '';

                    // 新しい画像を作成
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'プロフィール画像';
                    img.className = 'profile-image-holder';

                    imageDisplay.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
