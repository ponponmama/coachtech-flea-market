COACHTECH フリマ

<p align="center">
      <img src="https://img.shields.io/badge/-Laravel-black.svg?logo=laravel&style=plastic"> <img src="https://img.shields.io/badge/-Html5-pink.svg?logo=html5&style=plastic"> <img src="https://img.shields.io/badge/-CSS3-blue.svg?logo=css3&style=plastic"> <img src="https://img.shields.io/badge/-Php-orange.svg?logo=php&style=plastic"> <img src="https://img.shields.io/badge/-Javascript-F7DF1E.svg?logo=javascript&style=plastic"> <img src="https://img.shields.io/badge/-Mysql-green.svg?logo=mysql&style=plastic"> <img src="https://img.shields.io/badge/-Windows-0078D6.svg?logo=windows&style=plastic"> <img src="https://img.shields.io/badge/-Linux-FCC624.svg?logo=linux&style=plastic"> <img src="https://img.shields.io/badge/-Docker-1488C6.svg?logo=docker&style=plastic"> <img src="https://img.shields.io/badge/-Nginx-red.svg?logo=nginx&style=plastic"> <img src="https://img.shields.io/badge/-Github-181717.svg?logo=github&style=plastic">
</p>

### サービス概要

企業が開発した独自のフリマアプリです。アイテムの出品と購入を行うことを目的としています。

### 制作の背景と目的

- シンプルで使いやすい

### 制作の目標

- 初年度でのユーザー数 1000 人達成

### 主な機能一覧

### 認証機能

- ユーザー登録・ログイン
- メール認証
- Laravel Fortify による認証システム

### 機能紹介

- ユーザー登録

<p align="center">
    <img src="readme_md_images/register.png" alt="ユーザー登録画面">
</p>

- メール認証誘導画面

<p align="center">
    <img src="readme_md_images/email_verify.png" alt="メール認証誘導画面">
</p>

- ログイン

<p align="center">
    <img src="readme_md_images/login.png" alt="ユーザーログイン画面">
</p>

- 商品一覧画面（トップ）-未ログイン、ログイン後

<p align="center">
    <img src="readme_md_images/index_etc.png" alt="商品一覧画面">
</p>

- プロフィール設定画面\_初回ログイン時

<p align="center">
    <img src="readme_md_images/profile.png" alt="プロフィール設定画面">
</p>

- 商品詳細画面-ログイン後

<p align="center">
    <img src="readme_md_images/item_view.png" alt="商品詳細画面">
</p>

- 商品出品画面

<p align="center">
    <img src="readme_md_images/sell.png" alt="商品出品画面">
</p>

- プロフィール画面

<p align="center">
    <img src="readme_md_images/mypage.png" alt="プロフィール画面">
</p>

- プロフィール編集画面

<p align="center">
    <img src="readme_md_images/mypage_profile.png" alt="プロフィール画面">
</p>

- 商品購入画面

<p align="center">
    <img src="readme_md_images/purchase.png" alt="プロフィール画面">
</p>

- 送付先住所変更画面

<p align="center">
    <img src="readme_md_images/purchase_address.png" alt="プロフィール画面">
</p>

### 開発言語・フレームワーク

- **開発言語**: PHP
- **フレームワーク**: Laravel 8.x
- **データベース**: MySQL
- **バージョン管理**: GitHub
- **コンテナ化技術**: Docker
- **メールテスト**: MailHog

### 開発プロセス

- 設計 → コーディング → テスト

### ER 図

![Attendance Diagram](fleamarket.drawio.png)

### 環境構築

- **PHP**: 8.3.6 (ホスト) / 8.1.33 (コンテナ)
- **MySQL**: 8.0.26
- **Composer**: 2.8.12
- **Docker**: 28.5.1
- **Laravel Framework**: 8.83.29

- ＊ご使用の PC に合わせて各種必要なファイル(.env や docker-compose.yml 等)は作成、編集してください。

### セットアップ手順

####クローン作製手順

1. Github リポジトリのクローン

```bash
git clone https://github.com/ponponmama/coachtech-flea-market.git
```

```bash
cd coachtech-flea-market
```

2. 必要なパッケージのインストール

```bash
sudo apt-get update
```

Docker コンテナのビルドと起動

```bash
docker-compose up -d --build
```

3. Composer を使用した依存関係のインストール

- github からクローンを作成するといくつかのフォルダが足りません。src に setup.sh を作成してあります。プロジェクトはフレームワーク内にインストールするので、先にフォルダ作成お願いします。

- 3-1. コンテナに入ります。

```bash
docker-compose exec php bash
```

- 3-2. スクリプトに実行権限を付与します。

```bash
chmod +x setup.sh
```

```bash
./setup.sh
```

- 以下のフォルダが作成されます

```
      storage/app/public/profile-images
```

<br>

#### "ディレクトリが正常に作成されました。" ← このメッセージが出ます。<br>

<br>

- 3-3 Docker 環境で PHP コンテナに入り、依存関係をインストールします。<br>

```bash
docker-compose exec php bash
```

```bash
composer install
```

<br>

4. 環境設定ファイルの設定

- .env.example ファイルを .env としてコピーし、必要に応じてデータベースなどの設定を行います。

```bash
cp .env.example .env
```

- 環境設定を更新した後、設定キャッシュをクリアするために以下のコマンドを実行します。これにより、新しい設定がアプリケーションに反映されます。

```bash
docker-compose exec php bash
```

```bash
php artisan config:clear
```

この手順は、特に環境変数が更新された後や、`.env` ファイルに重要な変更を加えた場合に重要です。設定キャッシュをクリアすることで、古い設定が引き続き使用されることを防ぎます。

5.アプリケーションキーの生成

```bash
php artisan key:generate
```

6. データベースのセットアップ

#### データベースのマイグレーション

```bash
php artisan migrate
```

7. データベースシーダーの実行

```bash
php artisan db:seed
```

＊マイグレーションとシーダーを同時に実行する場合

```bash
php artisan migrate --seed
```

## 使用方法

### ユーザーとして使用

1. **ユーザー登録**

   - 新規アカウント作成
   - メールアドレス、パスワード、名前を入力

2. **メール認証**

   - 登録後、認証メールが送信される
   - MailHog（http://localhost:8025）でメールを確認
   - 認証リンクをクリックして認証完了

3. **プロフィール設定**

   - プロフィール画像、郵便番号、住所を登録
   - 初回ログイン時にプロフィール設定画面に遷移

4. **商品の出品**

   - 商品画像、カテゴリ、商品名、ブランド名、説明、価格を入力
   - 出品完了後、商品一覧に表示される

5. **商品の購入**

   - 商品詳細画面から購入
   - 支払い方法と配送先住所を選択
   - 購入完了後、商品は「SOLD」表示になる

6. **マイリスト機能**

   - 気になる商品にいいねを追加
   - マイページでいいねした商品を確認

7. **コメント機能**
   - 商品詳細画面でコメント投稿
   - 他のユーザーとの交流が可能

## ダミーデータログイン情報

### ユーザー

- メールアドレス: test@01.com 〜 test@21.com
- パスワード: user_pass

> **注意**: これらのユーザーは `php artisan db:seed` コマンドで作成されます。

\*test@ 数字（2 桁）.com の形式　例:test@01.com, test@02.com, ..., test@21.com

※ シーダー実行後に上記アカウントが作成されます。

### 取引メッセージ

- 取引中の商品（buyer_id が null の商品）に対して、出品者と購入希望者の間でメッセージが作成されます
- 各取引中の商品に対して 2-10 個のメッセージがランダムに作成されます
- メッセージは時系列順に作成され、出品者と購入希望者が交互に送信します
- 最後のメッセージは未読（is_read=false）の可能性があります
- メッセージの内容は商品に関する質問ややり取りをシミュレートしています

> **注意**: 取引メッセージは `TransactionMessageSeeder` で作成されます。取引中の商品がない場合は作成されません。

## テスト

### テスト実行

```bash
docker-compose exec php bash
```

```bash
php artisan test
```

### テスト項目

- **機能テスト**

  - `CommentTest.php` - コメント機能テスト（4 項目）

    - 認証ユーザーのコメント投稿（ID: 9-1）
    - ゲストユーザーのコメント投稿制限（ID: 9-2）
    - コメント必須バリデーション（ID: 9-3）
    - コメント最大文字数バリデーション（ID: 9-4）

  - `DeliveryAddressTest.php` - 配送先住所変更機能テスト（2 項目）

    - 住所変更画面での住所登録と購入画面への反映（ID: 12-1）
    - 購入商品への配送先住所紐づけ（ID: 12-2）

  - `EmailVerificationTest.php` - メール認証機能テスト（3 項目）

    - 会員登録後の認証メール送信（ID: 16-1）
    - メール認証誘導画面の認証ボタン遷移（ID: 16-2）
    - メール認証完了後のプロフィール画面遷移（ID: 16-3）

  - `ItemDetailTest.php` - 商品詳細情報取得機能テスト（2 項目）

    - 必要な情報の表示（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、カテゴリ、商品の状態、コメントしたユーザー情報、コメント内容）（ID: 7-1）
    - 複数選択されたカテゴリの表示（ID: 7-2）

  - `ItemLikeTest.php` - いいね機能テスト（3 項目）

    - いいねアイコン押下による商品登録（ID: 8-1）
    - いいね済みアイコンの色変化（ID: 8-2）
    - いいねアイコン再押下による解除（ID: 8-3）

  - `ItemListingTest.php` - 商品出品機能テスト（1 項目）

    - 商品出品画面での全項目保存（カテゴリ、商品の状態、商品名、ブランド名、商品の説明、販売価格）（ID: 15-1）

  - `ItemSearchTest.php` - 商品検索機能テスト（2 項目）

    - 商品名での部分一致検索（ID: 6-1）
    - 検索状態のマイリストでの保持（ID: 6-2）

  - `ItemsIndexTest.php` - 商品一覧機能テスト（3 項目）

    - 全商品の取得（ID: 4-1）
    - 購入済み商品の SOLD 表示（ID: 4-2）
    - 自分の出品商品の非表示（ID: 4-3）

  - `LoginValidationTest.php` - ログイン認証機能テスト（4 項目）

    - メールアドレス必須バリデーション（ID: 2-1）
    - パスワード必須バリデーション（ID: 2-2）
    - 無効な認証情報バリデーション（ID: 2-3）
    - ログイン成功（ID: 2-4）

  - `LogoutTest.php` - ログアウト機能テスト（1 項目）

    - ログアウト成功（ID: 3-1）

  - `MylistTest.php` - マイリスト機能テスト（3 項目）

    - いいねした商品のみ表示（ID: 5-1）
    - 購入済み商品の SOLD バッジ表示（ID: 5-2）
    - ゲストユーザーでのマイリスト非表示（ID: 5-3）

  - `PaymentMethodTest.php` - 支払い方法選択機能テスト（1 項目）

    - 支払い方法選択の小計画面への反映（ID: 11-1）

  - `PurchaseTest.php` - 商品購入機能テスト（3 項目）

    - 購入ボタンでの購入完了（ID: 10-1）
    - 購入済み商品の一覧画面での SOLD 表示（ID: 10-2）
    - 購入商品のプロフィール購入履歴への追加（ID: 10-3）

  - `RegisterValidationTest.php` - ユーザー登録機能テスト（6 項目）

    - 名前必須バリデーション（ID: 1-1）
    - メールアドレス必須バリデーション（ID: 1-2）
    - パスワード必須バリデーション（ID: 1-3）
    - パスワード最小文字数バリデーション（ID: 1-4）
    - パスワード確認バリデーション（ID: 1-5）
    - 登録成功時のプロフィール画面遷移（ID: 1-6）

  - `UserProfileTest.php` - ユーザープロフィール機能テスト（2 項目）

    - プロフィール情報表示（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）（ID: 13-1）
    - プロフィール編集フォームの初期値表示（プロフィール画像、ユーザー名、郵便番号、住所）（ID: 14-1）

    全 52 項目

  \*テストで一旦 db がリセットされます。マイグレートリフレッシュとシーダーしてください。

```bash
php artisan migrate:fresh --seed
```

### メール設定

プロジェクトでは開発環境でのメール送信のテストに Mailhog を使用しています。

```
🎯 MailHogの役割
メール送信のテスト: アプリケーションから送信されたメールを実際には送信せずにキャッチ
Web UI: ブラウザでメール内容を確認できる（通常は http://localhost:8025）
開発環境専用: 本番環境では使用しない
```

※　このファイルは開発環境でメール機能をテストするために必要なツールです。本番環境にはデプロイしないでください！

- Laravel のメール設定で MailHog を SMTP サーバーとして設定
- 会員登録時の認証メールが MailHog でキャッチされる
- http://localhost:8025 でメール内容を確認できる
- テストでは Mail::fake()を使用してメール送信をモック

\*\*※　プラットフォーム: Linux 用バイナリなので、Windows/Mac ユーザーは別途ダウンロードが必要な場合があります

**1. docker-compose.yml の設定確認**

`docker-compose.yml`に既に MailHog の設定が含まれています：

```yaml
mailhog:
  image: mailhog/mailhog:latest
  ports:
    - "1025:1025"
    - "8025:8025"
```

**2. .env ファイルへの設定追加**

下の設定を `.env` ファイルに追加してください。これにより、開発中のメール送信を安全にテストすることができます。

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@coachtech-flea-market.com
MAIL_FROM_NAME="${APP_NAME}"
```

**注意**: `MAIL_FROM_ADDRESS`の設定がないとメール送信が正常に動作しない場合があります。

### Stripe 設定

Stripe は、オンライン決済プラットフォームとして広く利用されています。このセクションでは、Stripe を使用して安全に決済を処理するための設定手順を詳しく説明します。
プロジェクトで決済処理を行うために Stripe を使用します。Stripe の API キーを設定することで、安全に決済を処理できます。以下の手順に従って設定を行ってください。

1. **アカウント作成**: Stripe の公式ウェブサイト（[https://stripe.com](https://stripe.com)）にアクセスし、アカウントを作成します。アカウント作成は無料で、メールアドレスと基本的な情報を入力するだけで完了します。
   今すぐ始めるをクリック
   ![alt text](stripe1.png)
   基本情報を入力後アカウントを作成をクリック
   ![alt text](stripe2.png)
   登録したメールアドレスにメールが届くので認証する
   ![alt text](stripe3.png)

2. **ダッシュボード**: アカウント作成後、Stripe のダッシュボードにログインします。ダッシュボードからは、API キーの管理、トランザクションの確認、支払い設定の変更などが行えます。

![alt text](stripe4.png)

3. **Stripe ライブラリのインストール**: Stripe 提供の公式ライブラリを使用すると、API の呼び出しが容易になります。Laravel プロジェクトであれば、Composer を使用して Stripe PHP ライブラリをインストールできます。Docker を使用している場合は、以下のコマンドを実行します。

   ```bash
   docker-compose exec php bash
   ```

   ```bash
   composer require stripe/stripe-php
   ```

4. **API キーの取得**: ダッシュボード内の「Developers」セクションから「API keys」を選択し、必要な API キー（公開キーと秘密キー）をメモします。これらのキーは、アプリケーションから Stripe API を安全に呼び出すために使用します。
   テストするのみなら、テスト環境ボタンをスライドしテスト環境にする

   - `STRIPE_KEY`: Stripe の公開可能キー（Public key）
   - `STRIPE_SECRET`: Stripe の秘密キー（Secret key）

5. `.env` ファイルを開き、以下の環境変数を更新します：

```plaintext
   STRIPE_KEY=ここに公開可能キーを貼り付ける
   STRIPE_SECRET=ここに秘密キーを貼り付ける
```

6. **決済処理の実装**: Laravel アプリケーションで決済処理を行うためには、以下のステップを実行します。

   - **コントローラーの作成**: Stripe の API を呼び出して決済を処理するためのコントローラーを作成します。このコントローラーでは、カード情報を受け取り、Stripe に対して支払いをリクエストする処理を実装します。

   - **ビューページの作成**: ユーザーがカード情報を入力するためのフォームを含むビューページを作成します。このページは、入力された情報をコントローラーに送信するためのものです。

   - **ルーティングの設定**: ビューページとコントローラーを結びつけるためのルーティングを設定します。

   - **バリデーションの追加**: 入力されたカード情報のバリデーションを行い、不正なデータが処理されないようにします。

#### セキュリティ対策

- **API キーの保護**: API キーは秘密情報です。公開リポジトリにはアップロードしないようにし、アクセス制御が適切に設定された環境変数を通じて管理します。
- **HTTPS の使用**: クライアントとサーバー間の通信には HTTPS を使用し、データの暗号化を保証します。これにより、中間者攻撃による情報漏洩のリスクを軽減します。

これらの手順に従うことで、Stripe を使用した決済処理を安全かつ効率的に行うことができます。

### URL

- **開発環境:** [http://localhost/](http://localhost/)
- **phpMyAdmin:** [http://localhost:8080/](http://localhost:8080/)
- **MailHog:** [http://localhost:8025/](http://localhost:8025/)
