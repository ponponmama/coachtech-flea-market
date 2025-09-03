{{-- 商品出品画面 /sell --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('js')
    <script src="{{ asset('js/image-upload.js') }}"></script>
@endsection

@section('content')
    <div class="content-container">
        <h1 class="content-title">商品の出品</h1>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data" class="sell-form">
            @csrf
            <div class="form-section">
                <p class="section-title">商品画像</p>
                <div class="image-preview" id="image-preview">
                    <div class="image-placeholder">
                        <button type="button" class="image-upload-button button"
                            onclick="document.getElementById('upload-image').click()">画像を登録する</button>
                        <input type="file" name="image" id="upload-image" class="upload-image-input" accept="image/*"
                            style="display: none;">
                    </div>
                </div>
                <p class="form__error">
                    @error('image')
                        {{ $message }}
                    @enderror
                </p>
            </div>
            <div class="form-section">
                <h2 class="form-section-title">商品の詳細</h2>
                <div class="form-group form-group-category">
                    <label class="form-label form-label-category">カテゴリー</label>
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
                <div class="form-group">
                    <label class="form-label" for="condition">商品の状態</label>
                    <select name="condition" id="condition" class="form-select">
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
            <div class="form-section">
                <h2 class="section-title">商品名と説明</h2>
                <div class="form-group">
                    <label class="form-label" for="name">商品名</label>
                    <input type="text" name="name" id="name" class="form-input">
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
                    <textarea name="description" id="description" class="form-textarea" rows="5"></textarea>
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
                        <input type="number" name="price" id="price" class="form-input price-input" min="0">
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
        // フォーム送信前のバリデーション
        document.querySelector('.sell-form').addEventListener('submit', function(e) {
            const selectedCategories = document.querySelectorAll('.category-checkbox:checked');
            if (selectedCategories.length === 0) {
                e.preventDefault();
                alert('カテゴリーを選択してください。');
                return false;
            }
        });
    </script>
@endsection
