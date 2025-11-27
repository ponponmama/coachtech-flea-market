<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ExhibitionRequest;
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

        // 初回ログインの場合
        if ($user->is_first_login) {
            // 初回ログインのメッセージを表示
            session()->flash('first_login', true);
        }

        // プロフィール情報を取得（存在しない場合はnull）
        $profile = $user->profile;

        // 郵便番号をハイフン付きの形式に変換
        if ($profile && $profile->postal_code) {
            // ハイフンがない場合のみ追加
            $profile->postal_code_display = strpos($profile->postal_code, '-') === false
                ? substr($profile->postal_code, 0, 3) . '-' . substr($profile->postal_code, 3)
                : $profile->postal_code;
        }

        return view('mypage.profile', compact('user', 'profile'));
    }

    /**
     * プロフィールを更新
     */
    public function updateProfile(ProfileRequest $request)
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
        if ($request->hasFile('profile_image')) {
            // 古い画像を削除（存在する場合）
            if ($user->profile && $user->profile->profile_image_path) {
                Storage::disk('public')->delete($user->profile->profile_image_path);
            }

            // 新しい画像を保存
            $profileImagePath = $request->file('profile_image')->store('profile-images', 'public');
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

        // 取引中の商品数（出品した商品で、まだ売れていないもの）
        $tradingCount = Item::where('seller_id', $user->id)
            ->whereNull('buyer_id')
            ->count();

        if ($page === 'buy') {
            // PG11: プロフィール画面_購入した商品一覧
            $purchasedItems = Item::where('buyer_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at', 'buyer_id')
                ->latest()
                ->get();
            $soldItems = collect();
            $tradingItems = collect();
        } elseif ($page === 'sell') {
            // PG12: プロフィール画面_出品した商品一覧
            $soldItems = Item::where('seller_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at', 'buyer_id')
                ->latest()
                ->get();
            $purchasedItems = collect();
            $tradingItems = collect();
        } elseif ($page === 'trading') {
            // 取引中の商品一覧
            $tradingItems = Item::where('seller_id', $user->id)
                ->whereNull('buyer_id')
                ->select('id', 'name', 'image_path', 'sold_at', 'buyer_id')
                ->latest()
                ->get();
            $soldItems = collect();
            $purchasedItems = collect();
        } else {
            // デフォルト表示（両方表示）
            $soldItems = Item::where('seller_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at', 'buyer_id')
                ->latest()
                ->get();
            $purchasedItems = Item::where('buyer_id', $user->id)
                ->select('id', 'name', 'image_path', 'sold_at', 'buyer_id')
                ->latest()
                ->get();
            $tradingItems = collect();
        }

        // 評価があるかどうかの判定（仮の値、後でDBから読み込む形に変更）
        // 例: $hasRating = ($user->average_rating ?? 0) > 0;
        $hasRating = false; // 仮の値、評価がある場合はtrue
        $ratingClass = $hasRating ? 'has-rating' : '';

        return view('mypage', compact('user', 'profile', 'soldItems', 'purchasedItems', 'tradingItems', 'tradingCount', 'page', 'ratingClass'));
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

        return view('sell', compact('categories'));
    }

    /**
     * 商品を出品
     */
    public function storeItem(ExhibitionRequest $request)
    {
        try {
            $user = Auth::user();

            // 価格のカンマを除去して数値に変換（バリデーション済み）
            $price = (int) str_replace(',', '', $request->price);

            // 画像を保存
            $imagePath = $request->file('image')->store('product-images', 'public');

            // 商品を作成
            $item = Item::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'description' => $request->description,
                'image_path' => $imagePath,
                'condition' => $request->condition,
                'price' => $price,
                'seller_id' => $user->id,
            ]);

            // カテゴリーを複数関連付け
            if ($request->category && is_array($request->category)) {
                // カテゴリーIDを整数に変換
                $categoryIds = array_map('intval', $request->category);
                $item->categories()->attach($categoryIds);
            }

            return redirect('/')->with('success', '商品を出品しました。');
        } catch (\Exception $e) {
            Log::error('商品出品エラー: ' . $e->getMessage());
            return back()->withInput()->with('error', '商品の出品に失敗しました。もう一度お試しください。');
        }
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
            'building' => $user->profile->building_name,
        ] : null;

        return view('purchase', compact('item', 'defaultAddress'));
    }


    /**
     * 送付先住所変更画面を表示（FN024: 配送先変更機能）
     */
    public function showAddress($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // 現在の配送先住所を取得（プロフィールは必ず存在する）
        $profile = $user->profile;

        // 郵便番号をハイフン付きの形式に変換
        $postalCodeDisplay = $profile->postal_code;
        if ($postalCodeDisplay && strpos($postalCodeDisplay, '-') === false) {
            $postalCodeDisplay = substr($postalCodeDisplay, 0, 3) . '-' . substr($postalCodeDisplay, 3);
        }

        $currentAddress = [
            'postal_code' => $postalCodeDisplay,
            'address' => $profile->address,
            'building' => $profile->building_name,
        ];

        return view('purchase.address', compact('item', 'currentAddress'));
    }

    /**
     * 送付先住所を更新（FN024: 配送先変更機能）
     */
    public function updateAddress(\App\Http\Requests\AddressRequest $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // 郵便番号からハイフンを除去（データベースにはハイフンなしで保存）
        $postalCode = str_replace('-', '', $request->postal_code);

        // ユーザーのプロフィール住所を更新
        $user->profile->update([
            'postal_code' => $postalCode,
            'address' => $request->address,
            'building_name' => $request->building,
        ]);

        return redirect('/purchase/' . $item_id)->with('address_updated', '送付先住所を更新しました。');
    }
}