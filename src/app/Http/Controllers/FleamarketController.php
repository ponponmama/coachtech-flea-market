<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FleamarketController extends Controller
{
    /**
     * 商品一覧画面（トップ）を表示
     */
    public function index()
    {
        return view('index');
    }

    /**
     * プロフィール設定画面を表示
     */
    public function showProfile()
    {
        return view('mypage.profile');
    }

    /**
     * プロフィールを更新
     */
    public function updateProfile(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'building_name' => 'nullable|string|max:255',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->with('error', 'ログインが必要です。');
        }

        // ユーザー名を更新
        $user->update([
            'name' => $request->name,
            'is_first_login' => false,
        ]);

        // プロフィール情報を更新または作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building_name' => $request->building_name,
            ]
        );

        return redirect('/')->with('success', 'プロフィールを更新しました。');
    }

    /**
     * マイページを表示
     */
    public function showMypage()
    {
        return view('mypage.index');
    }

    /**
     * 商品詳細を表示
     */
    public function showItem($id)
    {
        return view('item.show', compact('id'));
    }

    /**
     * 出品画面を表示
     */
    public function showSell()
    {
        return view('sell.index');
    }

    /**
     * 購入画面を表示
     */
    public function showPurchase($id)
    {
        return view('purchase.show', compact('id'));
    }
}