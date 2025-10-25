/**
 * 購入画面のJavaScript処理
 *
 * 機能：
 * 1. 支払い方法選択時の表示更新
 * 2. 購入ボタンの有効/無効制御
 * 3. Stripe決済セッション作成
 * 4. Stripe決済画面へのリダイレクト
 */

document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentMethodDisplay = document.getElementById('payment_method_display');
    const purchaseButton = document.getElementById('purchase_button');

    // 購入ボタンが存在しない場合（売り切れ商品など）は処理を終了
    if (!purchaseButton) {
        console.log('購入ボタンが見つかりません（売り切れ商品の可能性）');
        return;
    }

    // 支払い方法の変更を監視
    paymentMethodSelect.addEventListener('change', function() {
        const selectedValue = this.value;

        // カスタムセレクトボックスから選択されたテキストを取得
        const customSelect = document.getElementById('custom_payment_select');
        const selectedOption = customSelect.querySelector('.custom-option.selected .option-text');
        const selectedText = selectedOption ? selectedOption.textContent : '';

        console.log('支払い方法変更:', selectedValue, selectedText);

        // 小計画面の支払い方法表示を更新
        paymentMethodDisplay.textContent = selectedText;

        // 購入ボタンの有効/無効を制御
        if (selectedValue) {
            purchaseButton.disabled = false;
        } else {
            purchaseButton.disabled = true;
        }
    });

    // 購入ボタンのクリックイベント
    purchaseButton.addEventListener('click', function() {
        console.log('購入ボタンがクリックされました');
        const selectedPaymentMethod = paymentMethodSelect.value;
        console.log('選択された支払い方法:', selectedPaymentMethod);

        // バリデーションチェック
        if (!selectedPaymentMethod) {
            // フォーム送信でサーバー側バリデーションを実行
            document.querySelector('.purchase-form').submit();
            return;
        }

        // Stripe決済画面への接続
        if (selectedPaymentMethod === 'convenience' || selectedPaymentMethod === 'credit') {
            const itemId = getItemIdFromUrl();
            console.log('Stripe決済セッション作成中...支払い方法:', selectedPaymentMethod, '商品ID:', itemId);

            if (!itemId) {
                alert('商品IDを取得できませんでした。');
                return;
            }

            // Stripe決済セッション作成のAPIコール
            const requestData = {
                payment_method: selectedPaymentMethod,
                item_id: itemId
            };

            console.log('送信データ:', requestData);
            console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

            // 直接決済セッションを作成
            console.log('create-payment-sessionを呼び出します');
            console.log('リクエストデータ:', requestData);

            console.log('fetch開始');
            const fetchPromise = fetch('/create-payment-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            });
            console.log('fetch完了:', fetchPromise);

            fetchPromise
            .then(response => response.json())
            .then(data => {
                if (data.session_url) {
                    // Stripe決済画面にリダイレクト
                    window.location.href = data.session_url;
                } else if (data.error) {
                    alert('決済セッションの作成に失敗しました: ' + data.error);
                }
            })
            .catch(error => {
                console.error('決済セッション作成エラー:', error);
                alert('決済処理でエラーが発生しました。');
            });
        }
    });

    // 初期状態の設定
    // デフォルトで「選択してください」が表示されているので、購入ボタンを無効化
    const initialSelectedValue = paymentMethodSelect.value;

    if (initialSelectedValue) {
        // カスタムセレクトボックスから選択されたテキストを取得
        const customSelect = document.getElementById('custom_payment_select');
        const selectedOption = customSelect.querySelector('.custom-option.selected .option-text');
        const initialSelectedText = selectedOption ? selectedOption.textContent : '';

        paymentMethodDisplay.textContent = initialSelectedText;
        purchaseButton.disabled = false;
    } else {
        purchaseButton.disabled = true;
    }
});

/**
 * URLから商品IDを取得する関数
 * 例: /purchase/123 → 123
 */
function getItemIdFromUrl() {
    const path = window.location.pathname;
    const segments = path.split('/');
    const itemIdIndex = segments.indexOf('purchase') + 1;

    console.log('URL path:', path);
    console.log('URL segments:', segments);
    console.log('itemIdIndex:', itemIdIndex);

    if (itemIdIndex > 0 && itemIdIndex < segments.length) {
        const itemId = segments[itemIdIndex];
        console.log('URLから商品IDを取得:', itemId);
        return itemId;
    }

    // フォールバック: ページ内の要素から取得
    const itemIdElement = document.querySelector('[data-item-id]');
    if (itemIdElement) {
        const itemId = itemIdElement.getAttribute('data-item-id');
        console.log('data-item-idから商品IDを取得:', itemId);
        return itemId;
    }

    console.error('商品IDを取得できませんでした');
    return null;
}
