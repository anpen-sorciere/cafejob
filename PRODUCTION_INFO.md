# 本番環境情報

## ブランド情報

- **ブランド名**: カフェコレ（CafeColle）
- **正式名称**: カフェコレ（CafeColle）

## 本番環境のURL

- **独自ドメイン**: https://cafecolle.jp/
- **www付きドメイン**: https://www.cafecolle.jp/
- **サブドメイン**: https://purplelion51.sakura.ne.jp/cafejob/（旧URL）

## サーバー情報

- **サーバー**: さくらインターネット スタンダードプラン
- **SSHアクセス**: 可能
- **プロジェクトパス**: `/home/purplelion51/www/cafecolle`
- **Web公開フォルダー**: `/cafecolle`
- **ドメイン設定**: マルチドメインとして利用

## プロジェクト構造

- **構造**: Laravel標準構造（publicディレクトリあり）
- **マルチドメイン設定**: ドメインルート（cafecolle.jp）が `/cafecolle` フォルダーを指す

## 重要なファイル

### .htaccess
- ルート: `/cafecolle/public/` へのリダイレクト設定
- public: RewriteBase `/` に設定（マルチドメイン用）

### index.php
- パス修正済み（../なし）

### .env
- APP_URL: https://cafecolle.jp に設定が必要
- 設定済み

## 備考

新しいチャットで開発を続ける際は、このファイルを参照してください。
