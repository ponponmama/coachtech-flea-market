<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\FleamarketController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PaymentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 認証関連はFortifyが自動生成するため、ここでは設定不要

// メール認証関連(user)
Route::get('/email/verify', function () {
    return view('verify-email');
})->middleware(['auth'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/mypage/profile')->with('success', 'メール認証が完了しました。');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
    // ユーザー情報を取得（ログイン前でも可能）
    if (request()->user()) {
        request()->user()->sendEmailVerificationNotification();
        return back()->with('status', '認証メールを再送しました。');
    }

    return back()->with('error', '認証の有効時間（30分）が過ぎました。再度登録してください。');
})->middleware(['throttle:6,1'])->name('verification.send');

// プロフィール画面
Route::get('/mypage', [FleamarketController::class, 'showMypage'])->middleware(['auth'])->name('mypage');

// プロフィール設定画面
Route::get('/mypage/profile', [FleamarketController::class, 'showProfile'])->middleware(['auth'])->name('mypage.profile');

// プロフィール更新
Route::post('/mypage/profile', [FleamarketController::class, 'updateProfile'])->middleware(['auth'])->name('mypage.profile.update');

// 取引チャット画面
Route::get('/transaction-chat/{item_id}', [FleamarketController::class, 'showTransactionChat'])->middleware(['auth'])->name('transaction.chat');

// 取引メッセージ送信
Route::post('/transaction-chat/{item_id}/send', [FleamarketController::class, 'sendTransactionMessage'])->middleware(['auth'])->name('transaction.chat.send');
// 取引メッセージ更新（FN010）
Route::put('/transaction-message/{message_id}', [FleamarketController::class, 'updateTransactionMessage'])->middleware(['auth'])->name('transaction.message.update');
// 取引メッセージ削除（FN011）
Route::delete('/transaction-message/{message_id}', [FleamarketController::class, 'deleteTransactionMessage'])->middleware(['auth'])->name('transaction.message.delete');

// 商品一覧画面（トップ）
Route::get('/', [ItemController::class, 'index'])->name('top');

// 商品詳細画面
Route::get('/item/{item_id}', [FleamarketController::class, 'showItem'])->name('item.detail');

// 商品詳細画面でのコメント投稿
Route::post('/item/{item_id}/comment', [FleamarketController::class, 'storeComment'])->middleware(['auth'])->name('item.comment');

// いいね機能
Route::post('/item/{item_id}/like', [ItemController::class, 'toggleLike'])->middleware(['auth'])->name('item.like');

// 商品出品画面
Route::get('/sell', [FleamarketController::class, 'showSell'])->middleware(['auth'])->name('sell');
Route::post('/sell', [FleamarketController::class, 'storeItem'])->middleware(['auth'])->name('sell.store');

// 商品購入画面
Route::get('/purchase/{item_id}', [FleamarketController::class, 'showPurchase'])->middleware(['auth'])->name('purchase');

// 送付先住所変更画面
Route::get('/purchase/address/{item_id}', [FleamarketController::class, 'showAddress'])->middleware(['auth'])->name('purchase.address');
Route::post('/purchase/address/{item_id}', [FleamarketController::class, 'updateAddress'])->middleware(['auth'])->name('purchase.address.update');

// Stripe決済セッション作成（purchase.blade.phpのJavaScriptから呼び出し）
Route::post('/create-payment-session', [PaymentController::class, 'createPaymentSession'])->name('payment.create-session');

// 決済成功ページ
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');

// 決済キャンセルページ
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->middleware(['auth'])->name('payment.cancel');

// Stripe Webhook（CSRF除外が必要）
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');
