<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Stripeのシークレットキーを設定
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Stripe決済セッションを作成
     */
    public function createPaymentSession(PurchaseRequest $request)
    {
        try {
            // 認証チェック
            if (!Auth::check()) {
                // JSONリクエストの場合はJSONで返す
                if ($request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['error' => 'ログインが必要です'], 401);
                }
                // 通常のリクエストの場合はログインページにリダイレクト
                return redirect()->route('login');
            }

            // バリデーションはPurchaseRequestで自動実行される
            $item = Item::findOrFail($request->item_id);
            $user = Auth::user();

            // 決済セッションの設定
            $sessionParams = [
                'payment_method_types' => $request->payment_method === 'convenience'
                    ? ['konbini']
                    : ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                            'description' => $item->description,
                            'images' => $item->image_url ? [$item->image_url] : [],
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
                'metadata' => [
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'payment_method' => $request->payment_method,
                ],
            ];

            // コンビニ決済の場合は追加設定
            if ($request->payment_method === 'convenience') {
                $sessionParams['payment_method_options'] = [
                    'konbini' => [
                        'product_description' => $item->name,
                        'expires_after_days' => 3,
                    ],
                ];
            }

            $session = Session::create($sessionParams);

            // JSONリクエストの場合はJSONで返す
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'session_url' => $session->url,
                    'session_id' => $session->id,
                ]);
            }

            // 通常のフォーム送信の場合はStripe決済画面にリダイレクト
            return redirect($session->url);

        } catch (\Exception $e) {
            // JSONリクエストの場合はJSONでエラーを返す
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'error' => '決済セッションの作成に失敗しました。',
                    'details' => $e->getMessage()
                ], 500);
            }

            // 通常のリクエストの場合はエラーメッセージと共にリダイレクト
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => '決済セッションの作成に失敗しました。']);
        }
    }

    /**
     * 決済成功ページ
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        // デバッグ用
        Log::info('=== Payment Success Called ===');
        Log::info('Session ID: ' . $sessionId);

        // Stripeセッションから決済情報を取得
        try {
            $session = Session::retrieve($sessionId);
            Log::info('Payment status: ' . $session->payment_status);

            if ($session->payment_status === 'paid') {
                Log::info('Payment is paid, processing...');

                // 決済完了処理
                $itemId = $session->metadata->item_id;
                $userId = $session->metadata->user_id;

                Log::info('Item ID: ' . $itemId . ', User ID: ' . $userId);

                // 購入履歴を保存
                Log::info('Creating Purchase record...');
                Log::info('Amount total: ' . $session->amount_total);
                Log::info('Payment method: ' . $session->metadata->payment_method);

                Purchase::create([
                    'user_id' => $userId,
                    'item_id' => $itemId,
                    'payment_method' => $session->metadata->payment_method,
                    'amount' => $session->amount_total ?? 0,
                    'status' => 'completed',
                    'purchased_at' => now()
                ]);
                Log::info('Purchase record created successfully');

                // Itemテーブルのbuyer_idを更新（マイページの購入履歴表示用）
                Log::info('Updating Item buyer_id...');
                Item::where('id', $itemId)->update([
                    'buyer_id' => $userId,
                    'sold_at' => now()
                ]);
                Log::info('Item updated successfully');

                Log::info('Payment completed successfully');
                return redirect()->route('purchase', $itemId)->with('success', '決済が完了しました！');
            } else {
                Log::info('Payment not paid, status: ' . $session->payment_status);
            }
        } catch (\Exception $e) {
            Log::error('Payment success error: ' . $e->getMessage());
        }

        Log::info('Redirecting to purchase page with error');
        // セッションからitem_idを取得して購入ページにリダイレクト
        $sessionId = $request->get('session_id');
        try {
            $session = Session::retrieve($sessionId);
            $itemId = $session->metadata->item_id;
            return redirect()->route('purchase', $itemId)->with('error', '決済の確認に失敗しました。');
        } catch (\Exception $e) {
            return redirect()->route('top')->with('error', '決済の確認に失敗しました。');
        }
    }

    /**
     * 決済キャンセルページ
     */
    public function cancel(Request $request)
    {
        return redirect()->route('top')->with('error', '決済がキャンセルされました。');
    }
}
