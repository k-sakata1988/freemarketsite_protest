# フリマサイト（模擬案件）

## 環境構築
Dockerビルド
1. git clone git@github.com:k-sakata1988/freemarketsite_mogianken.git
2. DockerDesktopアプリを起動
3. docker-compose up -d --build

Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. composer require laravel/cashier
4. .env.exampleファイルから.envを作成し、環境変数を変更(mysql,laravel_db,laravel_user,laravel_passに設定)
5. php artisan key:generate
6. .envにstripe_key,stripe_secretを設定し.envから.env.testingを作成しAPP_ENV=の項目にtesting(テスト用)を設定する
7. php artisan migrate
8. php artisan db:seed
9. php artisan storage:link


## 使用技術
- PHP 8.1.33
- Laravel 8.83.8
- MySQL 8.0.26
- Nginx 1.21
- Docker 28.3.2 /Docker Compose 3.8

## ER図
![ER図](./er.drawio.png)

## URL
- 開発環境: http://localhost/
- phpMyAdmin: http://localhost:8080/