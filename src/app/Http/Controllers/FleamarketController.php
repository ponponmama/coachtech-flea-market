<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\TransactionMessageRequest;
use App\Http\Requests\RatingRequest;
use App\Models\User;
use App\Models\Item;
use App\Models\TransactionMessage;
use App\Models\Rating;
use App\Mail\TransactionCompleteNotification;

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
        $search = $request->query('search');

        // プロフィール情報を取得（存在しない場合はnull）
        $profile = $user->profile;

        // 取引中の商品数（出品した商品で、まだ売れていないもの または 購入した商品 または 取引メッセージが存在する商品）
        // ただし、両方が評価した商品は除外する
        // 決済処理が完了していても、評価が完了していない場合は取引中として扱う
        $tradingItemsForCount = Item::where(function ($query) use ($user) {
                // 出品者が自分の商品
                $query->where('seller_id', $user->id)
                    // または、購入した商品（buyer_idが自分のID）
                    ->orWhere('buyer_id', $user->id)
                    // または、取引メッセージが存在する商品（購入者も見られるように）
                    ->orWhereHas('transactionMessages', function ($q) use ($user) {
                        $q->where(function ($subQuery) use ($user) {
                            $subQuery->where('sender_id', $user->id)
                                ->orWhere('receiver_id', $user->id);
                        });
                    });
            })
            ->get()
            ->filter(function ($item) {
                // 両方が評価した商品を除外
                // 決済処理が完了していても、評価が完了していない場合は取引中として扱う
                $ratingCount = Rating::where('item_id', $item->id)
                    ->select('rater_id')
                    ->distinct()
                    ->count();
                return $ratingCount < 2; // 評価が2つ未満の商品のみ
            });
        $tradingCount = $tradingItemsForCount->count();

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
            $tradingItems = Item::where(function ($query) use ($user) {
                    // 出品者が自分の商品
                    $query->where('seller_id', $user->id)
                        // または、購入した商品（buyer_idが自分のID）
                        ->orWhere('buyer_id', $user->id)
                        // または、取引メッセージが存在する商品（購入者も見られるように）
                        ->orWhereHas('transactionMessages', function ($q) use ($user) {
                            $q->where(function ($subQuery) use ($user) {
                                $subQuery->where('sender_id', $user->id)
                                    ->orWhere('receiver_id', $user->id);
                            });
                        });
                })
                ->select('id', 'name', 'image_path', 'sold_at', 'buyer_id', 'seller_id')
                ->with(['transactionMessages' => function ($query) use ($user) {
                    // 最新メッセージを取得（ソート用）
                    $query->orderBy('created_at', 'desc');
                }])
                ->get()
                ->filter(function ($item) {
                    // 両方が評価した商品を除外
                    // 決済処理が完了していても、評価が完了していない場合は取引中として扱う
                    $ratingCount = Rating::where('item_id', $item->id)
                        ->select('rater_id')
                        ->distinct()
                        ->count();
                    return $ratingCount < 2; // 評価が2つ未満の商品のみ
                })
                ->map(function ($item) use ($user) {
                    // FN001, FN005: 未読メッセージ数を取得
                    // 自分が受信者で、未読のメッセージ数を取得
                    $item->unread_count = TransactionMessage::where('item_id', $item->id)
                        ->where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();

                    // 最新メッセージの日時（ソート用）
                    $latestMessage = $item->transactionMessages->first();
                    if ($latestMessage) {
                        $item->latest_message_at = $latestMessage->created_at;
                    } else {
                        $item->latest_message_at = $item->created_at; // メッセージがない場合は商品作成日時
                    }
                    return $item;
                })
                ->sortByDesc('latest_message_at') // 最新メッセージが来た順にソート
                ->values(); // インデックスを再設定
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

        // 他ユーザーからの取引評価の平均値を取得
        $ratings = Rating::where('rated_user_id', $user->id)->get();

        $rating = 0;
        if ($ratings->isNotEmpty()) {
            // 評価の平均値を計算（四捨五入）
            $averageRating = $ratings->avg('rating');
            $rating = round($averageRating);
        }

        return view('mypage', compact('user', 'profile', 'soldItems', 'purchasedItems', 'tradingItems', 'tradingCount', 'page', 'rating', 'search'));
    }

    /**
     * 取引チャット画面を表示
     */
    public function showTransactionChat($item_id)
    {
        $user = Auth::user();
        $item = Item::with(['seller', 'buyer'])->findOrFail($item_id);

        // 取引メッセージを取得（時系列順）
        $messages = TransactionMessage::where('item_id', $item_id)
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 取引相手を取得
        if ($item->buyer_id) {
            // 購入済みの場合：購入者の場合は出品者、出品者の場合は購入者
            $otherUser = ($user->id === $item->seller_id) ? $item->buyer : $item->seller;
        } else {
            // 取引中の場合：メッセージから取引相手を特定
            $latestMessage = $messages->first();
            if ($latestMessage) {
                $otherUser = ($latestMessage->sender_id === $user->id)
                    ? $latestMessage->receiver
                    : $latestMessage->sender;
            } else {
                // メッセージがない場合：出品者以外のユーザーを取得（仮の取引相手）
                // 実際にはメッセージがない場合は取引相手が確定していないので、出品者を表示
                $otherUser = $item->seller;
            }
        }

        // 取引相手が見つからない場合はエラー
        if (!$otherUser) {
            abort(400, '取引相手が特定できません。');
        }

        // FN003: 別取引遷移機能 - サイドバーに表示する他の取引中の商品一覧を取得
        // 決済処理が完了していても、評価が完了していない場合は取引中として扱う
        $otherTradingItems = Item::where('seller_id', $user->id)
            ->where('id', '!=', $item_id) // 現在の商品を除外
            ->select('id', 'name', 'image_path', 'sold_at', 'buyer_id')
            ->with(['transactionMessages' => function ($query) use ($user) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->filter(function ($item) {
                // 両方が評価した商品を除外
                // 決済処理が完了していても、評価が完了していない場合は取引中として扱う
                $ratingCount = Rating::where('item_id', $item->id)
                    ->select('rater_id')
                    ->distinct()
                    ->count();
                return $ratingCount < 2; // 評価が2つ未満の商品のみ
            })
            ->map(function ($otherItem) use ($user) {
                // 未読メッセージ数を取得
                $latestMessage = $otherItem->transactionMessages->first();
                if ($latestMessage) {
                    $partnerId = ($latestMessage->sender_id === $user->id)
                        ? $latestMessage->receiver_id
                        : $latestMessage->sender_id;

                    $otherItem->unread_count = TransactionMessage::where('item_id', $otherItem->id)
                        ->where('receiver_id', $user->id)
                        ->where('sender_id', $partnerId)
                        ->where('is_read', false)
                        ->count();

                    $otherItem->latest_message_at = $latestMessage->created_at;
                } else {
                    $otherItem->unread_count = 0;
                    $otherItem->latest_message_at = $otherItem->created_at;
                }
                return $otherItem;
            })
            ->sortByDesc('latest_message_at')
            ->values();

        // FN012, FN013: 評価モーダル表示判定
        // 購入者: 取引完了ボタンをクリックした時にモーダルを表示
        // 出品者: 購入者が取引を完了した後に、取引チャット画面を開くと自動的にモーダルを表示
        $showRatingModal = false;

        // バリデーションエラーがある場合、モーダルを表示
        if (session()->has('errors') && session('errors')->has('rating')) {
            $showRatingModal = true;
        }

        if ($item->buyer_id) {
            // 購入済みの場合
            // 購入者の場合：セッションからshowRatingModalフラグが来た場合のみモーダルを表示（取引完了ボタンをクリックした時）
            if ($user->id === $item->buyer_id) {
                if (session('showRatingModal')) {
                    $showRatingModal = true;
                }
            }
            // 出品者の場合：購入者が取引を完了した後に、取引チャット画面を開くと自動的にモーダルを表示
            elseif ($user->id === $item->seller_id) {
                $existingRating = Rating::where('item_id', $item_id)
                    ->where('rater_id', $user->id)
                    ->exists();
                // まだ評価していない場合、自動的にモーダルを表示
                if (!$existingRating) {
                    $showRatingModal = true;
                }
            }
        } else {
            // 取引中の商品（buyer_idがnull）の場合は、モーダルを表示しない
            // セッションのshowRatingModalはそのまま（他のページで使用される可能性があるため）
        }

        // 評価済みかどうかを確認
        $hasRating = Rating::where('item_id', $item_id)
            ->where('rater_id', $user->id)
            ->exists();

        return view('transaction-chat', compact('item', 'user', 'otherUser', 'messages', 'otherTradingItems', 'showRatingModal', 'hasRating'));
    }

    /**
     * 取引メッセージを送信
     */
    public function sendTransactionMessage(TransactionMessageRequest $request, $item_id)
    {
        $user = Auth::user();
        $item = Item::with(['seller', 'buyer'])->findOrFail($item_id);

        // 取引相手を取得
        if ($item->buyer_id) {
            // 購入済みの場合：購入者の場合は出品者、出品者の場合は購入者
            $otherUser = ($user->id === $item->seller_id) ? $item->buyer : $item->seller;
        } else {
            // 取引中の場合：メッセージから取引相手を特定
            $messages = TransactionMessage::where('item_id', $item_id)
                ->where(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->orWhere('receiver_id', $user->id);
                })
                ->get();

            if ($messages->isNotEmpty()) {
                // メッセージから取引相手を特定
                $latestMessage = $messages->first();
                $otherUserId = ($latestMessage->sender_id === $user->id)
                    ? $latestMessage->receiver_id
                    : $latestMessage->sender_id;
                $otherUser = User::find($otherUserId);
            } else {
                // メッセージがない場合
                if ($user->id === $item->seller_id) {
                    // 出品者の場合：購入希望者を取得（最初のメッセージなので、ランダムなユーザーを選択）
                    $otherUser = User::where('id', '!=', $user->id)->first();
                } else {
                    // 購入希望者の場合：出品者を取得
                    $otherUser = $item->seller;
                }
            }
        }

        // 取引相手が見つからない場合はエラー
        if (!$otherUser) {
            abort(400, '取引相手が特定できません。');
        }

        // 画像のアップロード処理（あれば）
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('transaction-messages', 'public');
        }

        // メッセージを作成（空文字列の場合はnullに変換）
        TransactionMessage::create([
            'item_id' => $item->id,
            'sender_id' => $user->id,
            'receiver_id' => $otherUser->id,
            'message' => !empty(trim($request->message)) ? $request->message : null,
            'image_path' => $imagePath,
            'is_read' => false,
        ]);

        return redirect()->route('transaction.chat', ['item_id' => $item_id])->with('success', 'メッセージを送信しました。');
    }

    /**
     * 取引メッセージを更新（FN010）
     */
    public function updateTransactionMessage(TransactionMessageRequest $request, $message_id)
    {
        $user = Auth::user();
        $message = TransactionMessage::findOrFail($message_id);

        // 自分のメッセージのみ編集可能
        if ($message->sender_id !== $user->id) {
            abort(403, 'このメッセージを編集する権限がありません。');
        }

        // 画像のアップロード処理（あれば）
        $imagePath = $message->image_path; // 既存の画像パスを保持
        if ($request->hasFile('image')) {
            // 既存の画像を削除
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('transaction-messages', 'public');
        }

        // メッセージを更新（空文字列の場合はnullに変換）
        $message->update([
            'message' => !empty(trim($request->message)) ? $request->message : null,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('transaction.chat', ['item_id' => $message->item_id])->with('success', 'メッセージを更新しました。');
    }

    /**
     * 取引メッセージを削除（FN011）
     */
    public function deleteTransactionMessage($message_id)
    {
        $user = Auth::user();
        $message = TransactionMessage::findOrFail($message_id);

        // 自分のメッセージのみ削除可能
        if ($message->sender_id !== $user->id) {
            abort(403, 'このメッセージを削除する権限がありません。');
        }

        $item_id = $message->item_id;

        // 画像を削除
        if ($message->image_path && Storage::disk('public')->exists($message->image_path)) {
            Storage::disk('public')->delete($message->image_path);
        }

        // メッセージを削除
        $message->delete();

        return redirect()->route('transaction.chat', ['item_id' => $item_id])->with('success', 'メッセージを削除しました。');
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

    /**
     * 取引完了処理（FN012）
     * 購入者が取引完了ボタンをクリックした時に評価モーダルを表示
     */
    public function completeTransaction($item_id)
    {
        $user = Auth::user();
        $item = Item::with(['seller', 'buyer'])->findOrFail($item_id);

        // 購入者の判定
        $isBuyer = false;
        if ($item->buyer_id) {
            // 購入済みの場合：buyer_idで判定
            $isBuyer = ($user->id === $item->buyer_id);
        } else {
            // 取引中の場合：出品者ではないユーザーが購入希望者として扱える
            $isBuyer = ($user->id !== $item->seller_id);
        }

        // 購入者のみが取引完了できる（FN012）
        if (!$isBuyer) {
            abort(403, 'この取引を完了する権限がありません。');
        }

        // 取引中の商品の場合、buyer_idを設定して取引を完了する
        $isNewTransaction = false;
        if (!$item->buyer_id) {
            $item->update([
                'buyer_id' => $user->id,
                'sold_at' => now(),
            ]);
            $item->refresh(); // リレーションを更新
            $isNewTransaction = true;
        }

        // FN016: 取引完了時に出品者にメール通知を送信
        if ($isNewTransaction) {
            $seller = $item->seller;
            $buyer = $item->buyer ?? $user;
            Mail::to($seller->email)->send(new TransactionCompleteNotification($seller, $buyer, $item));
        }

        // チャット画面にリダイレクト（評価モーダルを表示）
        return redirect()->route('transaction.chat', ['item_id' => $item_id])
            ->with('showRatingModal', true);
    }

    /**
     * 評価を送信（FN012, FN013, FN014）
     */
    public function storeRating(RatingRequest $request, $item_id)
    {
        // バリデーションはRatingRequestで自動的に実行される
        // バリデーションが失敗した場合、自動的にリダイレクトされる

        $user = Auth::user();
        $item = Item::with(['seller', 'buyer'])->findOrFail($item_id);

        // 決済処理後（buyer_idが設定されている）の場合のみ評価できる
        if (!$item->buyer_id) {
            abort(400, '決済処理が完了していないため、評価できません。');
        }

        // 購入者または出品者のみが評価できる
        $isBuyer = ($user->id === $item->buyer_id);
        $isSeller = ($user->id === $item->seller_id);

        if (!$isBuyer && !$isSeller) {
            abort(403, 'この取引を評価する権限がありません。');
        }

        // 取引相手を取得（決済処理後なので、buyer_idが設定されている）
        $ratedUserId = ($user->id === $item->seller_id) ? $item->buyer_id : $item->seller_id;

        if (!$ratedUserId) {
            abort(400, '評価対象のユーザーが特定できません。');
        }

        // 既に評価済みかチェック
        $existingRating = Rating::where('item_id', $item_id)
            ->where('rater_id', $user->id)
            ->first();

        if ($existingRating) {
            // 既存の評価を更新
            $existingRating->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        } else {
            // 新しい評価を作成
            Rating::create([
                'item_id' => $item_id,
                'rater_id' => $user->id,
                'rated_user_id' => $ratedUserId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);
        }

        // 決済処理後（buyer_idが設定されている）の場合のみ評価できるため、
        // buyer_idを設定する処理は不要（completeTransactionで既に設定されている）

        // FN014: 評価を送信した後は、商品一覧画面に遷移する
        return redirect()->route('top')->with('success', '評価を送信しました。');
    }
}
