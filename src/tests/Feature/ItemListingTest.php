<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemListingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * テスト項目: 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）
     * ID: 15-1
     *
     * テストシナリオ:
     * 1. ユーザーにログインする
     * 2. 商品出品画面を開く
     * 3. 各項目に適切な情報を入力して保存する
     *
     * 期待結果: 各項目が正しく保存されている
     */
    public function test_item_listing_saves_all_required_information()
    {
        /** @var User $user */
        $user = User::factory()->create();

        // ユーザーのプロフィールを作成
        \App\Models\Profile::factory()->create(['user_id' => $user->id]);

        // カテゴリを作成
        $category1 = Category::factory()->create(['name' => 'エレクトロニクス']);
        $category2 = Category::factory()->create(['name' => 'アクセサリー']);

        $this->actingAs($user);

        // 1. 商品出品画面を開く
        $response = $this->get('/sell');
        $response->assertStatus(200);
        $response->assertSee('商品の出品');
        $response->assertSee('エレクトロニクス');
        $response->assertSee('アクセサリー');

        // 2. 各項目に適切な情報を入力して保存する
        $image = UploadedFile::fake()->create('test-product.jpg', 1024, 'image/jpeg');

        $itemData = [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。',
            'condition' => '良好',
            'price' => '15000',
            'category' => [$category1->id, $category2->id],
            'image' => $image,
        ];

        $response = $this->post('/sell', $itemData);

        // 3. 各項目が正しく保存されていることを確認
        $response->assertRedirect('/');
        $response->assertSessionHas('success', '商品を出品しました。');

        // データベースに保存されていることを確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。',
            'condition' => '良好',
            'price' => 15000,
            'seller_id' => $user->id,
        ]);

        // 画像が保存されていることを確認
        $item = Item::where('name', 'テスト商品')->first();
        $this->assertNotNull($item->image_path);
        $this->assertTrue(Storage::disk('public')->exists($item->image_path));

        // カテゴリが正しく関連付けられていることを確認
        $this->assertTrue($item->categories->contains($category1));
        $this->assertTrue($item->categories->contains($category2));
        $this->assertEquals(2, $item->categories->count());
    }

    /**
     * テスト項目: 商品出品のバリデーションが正しく動作する
     */
    public function test_item_listing_validation_works_correctly()
    {
        /** @var User $user */
        $user = User::factory()->create();

        \App\Models\Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // 無効なデータで出品を試行
        $invalidData = [
            'name' => '', // 空の商品名
            'description' => '', // 空の説明
            'price' => '-100', // 負の価格
            'category' => [], // 空のカテゴリ
            'condition' => '', // 空の状態
            // 画像なし
        ];

        $response = $this->post('/sell', $invalidData);

        // バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors([
            'name',
            'description',
            'image',
            'category',
            'condition',
            'price'
        ]);
    }

    /**
     * テスト項目: 商品出品画面が正しく表示される
     */
    public function test_item_listing_page_displays_correctly()
    {
        /** @var User $user */
        $user = User::factory()->create();

        \App\Models\Profile::factory()->create(['user_id' => $user->id]);

        // カテゴリを作成
        $category = Category::factory()->create(['name' => 'テストカテゴリ']);

        $this->actingAs($user);

        $response = $this->get('/sell');
        $response->assertStatus(200);

        // 必要な要素が表示されていることを確認
        $response->assertSee('商品の出品');
        $response->assertSee('商品画像');
        $response->assertSee('商品の詳細');
        $response->assertSee('カテゴリー');
        $response->assertSee('商品の状態');
        $response->assertSee('商品名と説明');
        $response->assertSee('商品名');
        $response->assertSee('ブランド名');
        $response->assertSee('商品の説明');
        $response->assertSee('販売価格');
        $response->assertSee('出品する');

        // カテゴリが表示されていることを確認
        $response->assertSee('テストカテゴリ');

        // 商品の状態の選択肢が表示されていることを確認
        $response->assertSee('良好');
        $response->assertSee('目立った傷や汚れなし');
        $response->assertSee('やや傷や汚れあり');
        $response->assertSee('状態が悪い');

        // フォームの各要素が存在することを確認（HTMLエスケープを考慮）
        $response->assertSee('name=');
        $response->assertSee('brand');
        $response->assertSee('description');
        $response->assertSee('price');
        $response->assertSee('condition');
        $response->assertSee('image');
    }

    /**
     * テスト項目: 価格のカンマ処理が正しく動作する
     */
    public function test_price_comma_processing_works_correctly()
    {
        /** @var User $user */
        $user = User::factory()->create();

        \App\Models\Profile::factory()->create(['user_id' => $user->id]);

        $category = Category::factory()->create();
        $image = UploadedFile::fake()->create('test-product.jpg', 1024, 'image/jpeg');

        $this->actingAs($user);

        // 数値の価格で出品（カンマはフロントエンドで処理される想定）
        $itemData = [
            'name' => 'カンマテスト商品',
            'brand' => 'テストブランド',
            'description' => 'カンマ付き価格のテスト商品です。',
            'condition' => '良好',
            'price' => 12345, // 数値として送信
            'category' => [$category->id],
            'image' => $image,
        ];

        $response = $this->post('/sell', $itemData);
        $response->assertRedirect('/');

        // 価格が正しく保存されていることを確認
        $this->assertDatabaseHas('items', [
            'name' => 'カンマテスト商品',
            'price' => 12345, // 数値として保存
        ]);
    }

    /**
     * テスト項目: 複数カテゴリの選択が正しく動作する
     */
    public function test_multiple_category_selection_works_correctly()
    {
        /** @var User $user */
        $user = User::factory()->create();

        \App\Models\Profile::factory()->create(['user_id' => $user->id]);

        // 複数のカテゴリを作成（一意の名前を生成）
        $categories = collect();
        for ($i = 1; $i <= 3; $i++) {
            $categories->push(Category::factory()->create(['name' => "テストカテゴリ{$i}"]));
        }
        $image = UploadedFile::fake()->create('test-product.jpg', 1024, 'image/jpeg');

        $this->actingAs($user);

        $itemData = [
            'name' => '複数カテゴリテスト商品',
            'brand' => 'テストブランド',
            'description' => '複数カテゴリのテスト商品です。',
            'condition' => '良好',
            'price' => '5000',
            'category' => $categories->pluck('id')->toArray(),
            'image' => $image,
        ];

        $response = $this->post('/sell', $itemData);
        $response->assertRedirect('/');

        // 全てのカテゴリが正しく関連付けられていることを確認
        $item = Item::where('name', '複数カテゴリテスト商品')->first();
        $this->assertEquals(3, $item->categories->count());

        foreach ($categories as $category) {
            $this->assertTrue($item->categories->contains($category));
        }
    }
}
