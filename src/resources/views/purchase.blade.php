{{-- 商品購入画面 - ログイン後 --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
    <div class="content-container">
        <h1 class="content-title">商品購入画面</h1>

        <div class="purchase-section">
            <!-- 左側：商品情報・支払い・配送先 -->
            <div class="purchase-main-section">
                <!-- 商品情報 -->
                <div class="product-info-section">
                    <div class="product-image-container">
                        @if ($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}"
                                class="product-image">
                        @else
                            <div class="product-image-placeholder">商品画像</div>
                        @endif
                    </div>
                    <div class="product-details">
                        <p class="product-name">{{ $item->name }}</p>
                        <p class="product-price">¥{{ number_format($item->price) }}</p>
                    </div>
                </div>

                <!-- 支払い方法 -->
                <div class="payment-section">
                    <p class="section-title">支払い方法</p>
                    <div class="form-group">
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="">選択してください</option>
                            <option value="convenience">コンビニ支払い</option>
                            <option value="credit">カード支払い</option>
                        </select>
                    </div>
                </div>

                <!-- 配送先 -->
                <div class="delivery-section">
                    <div class="delivery-header">
                        <p class="section-title">配送先</p>
                        <a href="#" class="change-link">変更する</a>
                    </div>
                    <div class="delivery-address">
                        <p class="postal-code">〒 XXX-YYYY</p>
                        <p class="address-text">ここには住所と建物が入ります</p>
                    </div>
                </div>
            </div>

            <!-- 右側：注文サマリー -->
            <div class="order-summary-section">
                <div class="order-summary-box">
                    <div class="summary-row">
                        <span class="summary-label">商品代金</span>
                        <span class="summary-value">¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">支払い方法</span>
                        <span class="summary-value" id="payment_method_display">選択してください</span>
                    </div>
                </div>
                <button type="button" id="purchase_button" class="purchase-button">購入する</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('payment_method');
            const paymentMethodDisplay = document.getElementById('payment_method_display');
            const purchaseButton = document.getElementById('purchase_button');

            // 支払い方法の変更を監視
            paymentMethodSelect.addEventListener('change', function() {
                const selectedValue = this.value;
                const selectedText = this.options[this.selectedIndex].text;

                // 小計画面の支払い方法表示を更新
                paymentMethodDisplay.textContent = selectedText;

                // 購入ボタンの有効/無効を制御
                if (selectedValue) {
                    purchaseButton.disabled = false;
                    purchaseButton.style.opacity = '1';
                } else {
                    purchaseButton.disabled = true;
                    purchaseButton.style.opacity = '0.5';
                }
            });

            // 購入ボタンのクリックイベント
            purchaseButton.addEventListener('click', function() {
                const selectedPaymentMethod = paymentMethodSelect.value;

                if (!selectedPaymentMethod) {
                    alert('支払い方法を選択してください。');
                    return;
                }

                // Stripe決済画面への接続
                if (selectedPaymentMethod === 'convenience' || selectedPaymentMethod === 'credit') {
                    // ここでStripe決済画面に接続
                    // 実際の実装では、Stripeの決済セッションを作成してリダイレクト
                    console.log('Stripe決済画面に接続します。支払い方法:', selectedPaymentMethod);

                    // 例：Stripe決済セッション作成のAPIコール
                    // fetch('/create-payment-session', {
                    //     method: 'POST',
                    //     headers: {
                    //         'Content-Type': 'application/json',
                    //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    //     },
                    //     body: JSON.stringify({
                    //         payment_method: selectedPaymentMethod,
                    //         item_id: {{ $item->id }}
                    //     })
                    // })
                    // .then(response => response.json())
                    // .then(data => {
                    //     if (data.session_url) {
                    //         window.location.href = data.session_url;
                    //     }
                    // })
                    // .catch(error => {
                    //     console.error('決済セッション作成エラー:', error);
                    //     alert('決済処理でエラーが発生しました。');
                    // });

                    alert('Stripe決済画面に接続します（実装予定）');
                }
            });

            // 初期状態で購入ボタンを無効化
            purchaseButton.disabled = true;
            purchaseButton.style.opacity = '0.5';
        });
    </script>
@endsection
