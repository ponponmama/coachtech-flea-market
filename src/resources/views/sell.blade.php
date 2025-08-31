{{-- 商品出品画面 /sell --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <h1 class="page-title">商品の出品</h1>

        <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
            @csrf

            <!-- 商品画像 -->
            <div class="form-section">
                <h2 class="section-title">商品画像</h2>
                <div class="image-upload-section">
                    <div class="image-preview" id="image-preview">
                        <div class="image-placeholder">
                            <span class="placeholder-text">画像を選択してください</span>
                        </div>
                    </div>
                    <label for="image" class="image-upload-button">画像を登録する</label>
                    <input type="file" name="image" id="image" class="image-input" accept="image/*" required>
                </div>
                @error('image')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <!-- 商品の詳細 -->
            <div class="form-section">
                <h2 class="section-title">商品の詳細</h2>

                <!-- カテゴリー -->
                <div class="form-group">
                    <label class="form-label">カテゴリー</label>
                    <div class="category-buttons">
                        @foreach ($categories as $category)
                            <label class="category-button">
                                <input type="checkbox" name="category[]" value="{{ $category->id }}"
                                    class="category-checkbox">
                                <span class="category-text">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('category')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 商品の状態 -->
                <div class="form-group">
                    <label class="form-label" for="condition">商品の状態</label>
                    <select name="condition" id="condition" class="form-select" required>
                        <option value="">選択してください</option>
                        @foreach ($conditions as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('condition')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 商品名と説明 -->
            <div class="form-section">
                <h2 class="section-title">商品名と説明</h2>

                <!-- 商品名 -->
                <div class="form-group">
                    <label class="form-label" for="name">商品名</label>
                    <input type="text" name="name" id="name" class="form-input" required>
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ブランド名 -->
                <div class="form-group">
                    <label class="form-label" for="brand">ブランド名</label>
                    <input type="text" name="brand" id="brand" class="form-input">
                    @error('brand')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 商品の説明 -->
                <div class="form-group">
                    <label class="form-label" for="description">商品の説明</label>
                    <textarea name="description" id="description" class="form-textarea" rows="5" required></textarea>
                    @error('description')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 販売価格 -->
            <div class="form-section">
                <h2 class="section-title">販売価格</h2>
                <div class="form-group">
                    <label class="form-label" for="price">価格</label>
                    <div class="price-input-group">
                        <span class="price-symbol">¥</span>
                        <input type="number" name="price" id="price" class="form-input price-input" min="0"
                            required>
                    </div>
                    @error('price')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- 出品ボタン -->
            <div class="form-section">
                <button type="submit" class="submit-button">出品する</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        // 画像プレビュー機能
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="プレビュー" class="preview-image">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML =
                    '<div class="image-placeholder"><span class="placeholder-text">画像を選択してください</span></div>';
            }
        });

        // カテゴリーボタンの選択状態管理
        document.querySelectorAll('.category-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const button = this.closest('.category-button');
                if (this.checked) {
                    button.classList.add('selected');
                } else {
                    button.classList.remove('selected');
                }
            });
        });
    </script>
@endsection
