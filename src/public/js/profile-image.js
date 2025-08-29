document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile-image');
    const placeholder = document.querySelector('.profile-image-placeholder');

    if (fileInput && placeholder) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    placeholder.style.backgroundImage = `url(${e.target.result})`;
                    placeholder.style.backgroundSize = 'cover';
                    placeholder.style.backgroundPosition = 'center';
                    placeholder.style.backgroundRepeat = 'no-repeat';
                    placeholder.textContent = ''; // テキストを削除
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
