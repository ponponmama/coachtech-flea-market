# GitHubからクローン時に必要なディレクトリを作成
mkdir -p /var/www/storage
mkdir -p /var/www/storage/app/public/profile-images

# 必要な権限を設定
chmod -R 775 /var/www/storage

# オーナーを設定（Docker環境用）
chown -R www-data:www-data /var/www/storage

echo "ディレクトリが正常に作成されました。"
