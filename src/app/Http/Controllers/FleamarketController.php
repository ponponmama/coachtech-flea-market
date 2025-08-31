<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Item;

class FleamarketController extends Controller
{


    /**
     * プロフィール設定画面を表示
     */
    public function showProfile()
    {
        $user = Auth::user();

        // プロフィール情報を取得（存在しない場合はnull）
        $profile = $user->profile;

        // 郵便番号をハイフン付きの形式に変換
        if ($profile && $profile->postal_code) {
            $profile->postal_code_display = substr($profile->postal_code, 0, 3) . '-' . substr($profile->postal_code, 3);
        }

        return view('mypage.profile', compact('user', 'profile'));
    }

    /**
     * プロフィールを更新
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->with('error', 'ログインが必要です。');
        }

        // プロフィール画像の処理
        $profileImagePath = null;
        // プロフィール画像の処理
        $profileImagePath = null;
        if ($request->hasFile('profile-image')) {
            // 古い画像を削除（存在する場合）
            if ($user->profile && $user->profile->profile_image_path) {
                Storage::disk('public')->delete($user->profile->profile_image_path);
            }

            // 新しい画像を保存
            $profileImagePath = $request->file('profile-image')->store('profile-images', 'public');
        }

        // ユーザー情報を更新
        $user->update([
            'name' => $request->name,
            'is_first_login' => false,
        ]);

        // 郵便番号からハイフンを除去
        $postalCode = $request->postal_code ? str_replace('-', '', $request->postal_code) : null;

        // プロフィール情報を更新または作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'profile_image_path' => $profileImagePath ?? ($user->profile->profile_image_path ?? null),
                'postal_code' => $postalCode,
                'address' => $request->address,
                'building_name' => $request->building_name,
            ]
        );

        return redirect('/')->with('success', 'プロフィールを更新しました。');
    }

    /**
     * マイページを表示
     */
    public function showMypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page');

        // プロフィール情報を取得（存在しない場合はnull）
        $profile = $user->profile;

        if ($page === 'buy') {
            // PG11: プロフィール画面_購入した商品一覧
            $purchasedItems = Item::where('buyer_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at')
                ->latest()
                ->get();
            $soldItems = collect();
        } elseif ($page === 'sell') {
            // PG12: プロフィール画面_出品した商品一覧
            $soldItems = Item::where('seller_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at')
                ->latest()
                ->get();
            $purchasedItems = collect();
        } else {
            // デフォルト表示（両方表示）
            $soldItems = Item::where('seller_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at')
                ->latest()
                ->get();
            $purchasedItems = Item::where('buyer_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at')
                ->latest()
                ->get();
        }

        return view('mypage', compact('user', 'profile', 'soldItems', 'purchasedItems', 'page'));
    }

    /**
     * 商品詳細を表示
     */
    public function showItem($item_id)
    {
        $item = Item::with(['seller', 'categories'])->findOrFail($item_id);
        return view('item-detail', compact('item'));
    }

    /**
     * 商品詳細画面でのコメント投稿
     */
    public function storeComment(Request $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // CommentRequestのバリデーションを使用
        $commentRequest = new \App\Http\Requests\CommentRequest();
        $commentRequest->merge($request->all());
        $commentRequest->validate($commentRequest->rules(), $commentRequest->messages());

        // コメントを作成
        $item->comments()->create([
            'user_id' => $user->id,
            'content' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'コメントを投稿しました。');
    }

    /**
     * 出品画面を表示
     */
    public function showSell()
    {
        // カテゴリ一覧を取得
        $categories = \App\Models\Category::all();

        // 商品状態の選択肢
        $conditions = [
            'excellent' => '良好',
            'good' => '目立った傷や汚れなし',
            'fair' => 'やや傷や汚れあり',
            'poor' => '状態が悪い',
        ];

        return view('sell', compact('categories', 'conditions'));
    }

    /**
     * 商品を出品
     */
    public function storeItem(Request $request)
    {
        // ExhibitionRequestのバリデーションを使用
        $exhibitionRequest = new \App\Http\Requests\ExhibitionRequest();
        $exhibitionRequest->merge($request->all());
        $exhibitionRequest->validate($exhibitionRequest->rules(), $exhibitionRequest->messages());

        $user = Auth::user();

        // 画像を保存
        $imagePath = $request->file('image')->store('product-images', 'public');

        // 商品を作成
        $item = Item::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'image_path' => $imagePath,
            'condition' => $request->condition,
            'price' => $request->price,
            'seller_id' => $user->id,
        ]);

        // カテゴリーを複数関連付け
        if ($request->category && is_array($request->category)) {
            $item->categories()->attach($request->category);
        }

        return redirect('/')->with('success', '商品を出品しました。');
    }

    /**
     * 購入画面を表示（FN021: 購入前商品情報取得機能）
     */
    public function showPurchase($item_id)
    {
        $user = Auth::user();
        $item = Item::with(['seller', 'categories'])->findOrFail($item_id);

        // 初期値はプロフィール画面にて登録済みの住所
        $defaultAddress = $user->profile ? [
            'postal_code' => $user->profile->postal_code,
            'address' => $user->profile->address,
        ] : null;

        return view('purchase', compact('item', 'defaultAddress'));
    }

    /**
     * 購入処理（FN022: 商品購入機能、FN023: 支払い方法選択機能）
     */
    public function processPurchase(Request $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // PurchaseRequestのバリデーションを使用
        $purchaseRequest = new \App\Http\Requests\PurchaseRequest();
        $purchaseRequest->merge($request->all());
        $purchaseRequest->validate($purchaseRequest->rules(), $purchaseRequest->messages());

        // 購入処理のロジック
        $item->update([
            'buyer_id' => $user->id,
            'sold_at' => now(),
        ]);

        // 支払い方法に応じた処理
        $paymentMethod = $request->payment_method;
        if ($paymentMethod === 'credit_card' || $paymentMethod === 'convenience_store') {
            // Stripeの決済画面に接続（実装予定）
            // return redirect()->route('stripe.checkout', ['item_id' => $item_id]);
        }

        // FN022: 商品を購入した後の遷移先は商品一覧画面
        return redirect('/')->with('success', '購入が完了しました。');
    }

    /**
     * 送付先住所変更画面を表示（FN024: 配送先変更機能）
     */
    public function showAddress($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // 現在の配送先住所を取得（実装予定）
        $currentAddress = null; // アイテムに紐づく配送先住所

        return view('purchase.address', compact('item', 'currentAddress'));
    }

    /**
     * 送付先住所を更新（FN024: 配送先変更機能）
     */
    public function updateAddress(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // AddressRequestのバリデーションを使用
        $addressRequest = new \App\Http\Requests\AddressRequest();
        $addressRequest->merge($request->all());
        $addressRequest->validate($addressRequest->rules(), $addressRequest->messages());

        // アイテムに配送先住所を紐づける処理（実装予定）
        // テーブル内では各アイテムに送付先住所が紐づいている

        return redirect('/purchase/' . $item_id)->with('success', '送付先住所を更新しました。');
    }
}
