<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;

class ItemController extends Controller
{
    /**
     * 商品一覧画面（トップ）を表示
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab');
        $search = $request->query('search');

        if ($tab === 'mylist') {
            // FN015: マイリスト一覧取得
            if (!$user) {
                // 未認証の場合は何も表示されない
                $items = collect();
            } else {
                // いいねした商品だけが表示されている
                $items = Item::whereHas('likes', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['seller', 'categories'])
                ->when($search, function($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })
                ->latest()
                ->get()
                ->map(function($item) {
                    // 購入済み商品は "sold" と表示される
                    $item->is_sold = !is_null($item->buyer_id);
                    return $item;
                });
            }
        } else {
            // FN014: 商品一覧取得
            $items = Item::with(['seller', 'categories'])
                ->when($search, function($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                })
                ->when($user, function($query, $user) {
                    // 自分が出品した商品は表示されない
                    return $query->where('seller_id', '!=', $user->id);
                })
                ->latest()
                ->get()
                ->map(function($item) {
                    // 購入済み商品は "sold" と表示される
                    $item->is_sold = !is_null($item->buyer_id);
                    return $item;
                });
        }

        return view('index', compact('items', 'tab', 'search'));
    }
}
