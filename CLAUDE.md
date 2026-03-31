# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 開発コマンド

すべてのコマンドは `ci4transfer/` ディレクトリ内で実行する。

```bash
# 依存インストール
composer install

# マイグレーション（Shield・Settings・アプリ独自を一括）
php spark migrate --all

# ルーティング確認
php spark routes

# テスト実行
vendor/bin/phpunit

# 管理者ユーザー作成
php spark shield:user create
php spark shield:user addgroup   # グループ名: administrator

# 期限切れファイル削除バッチ
php spark files:cleanup
```

Docker 環境では `docker compose up -d` でDB起動後、上記コマンドを使用する。  
cron 例（毎日深夜2時）: `0 2 * * * docker compose exec -T php php spark files:cleanup`

## アーキテクチャ概要

```
ci4transfer/
  app/
    Commands/       # FilesCleanup（spark files:cleanup）
    Config/         # Routes.php, AuthGroups.php
    Controllers/
      Admin/        # AdminController(抽象), Dashboard, Files, Users, Settings
      Upload.php    # POST /upload
      Download.php  # GET|POST /download/{token}
      Mypage.php    # GET /mypage, POST /files/delete/{id}
    Models/         # FileModel, SettingModel
    Views/
      layouts/      # admin.php（Tabler CDN）, main.php（公開面）
      admin/        # dashboard, files, users, settings ビュー群
  public/
    css/themes/     # 公開面テーマCSS（11種）
  writable/
    uploads/        # アップロードファイル保存先（UUID命名）
```

## 認証・権限

認証は **CodeIgniter Shield** に全面委譲（カスタム AuthFilter は存在しない）。

- ルートの `'filter' => 'session'` で未ログインをガード
- `Admin/AdminController::initController()` で `admin.access` パーミッションを検証

| グループ | パーミッション |
|----------|--------------|
| `administrator` | `admin.access`, `admin.settings`, `users.manage`, `files.manage` |
| `user` | なし（デフォルト） |

## コントローラ設計

**BaseController** — `initController()` で `$this->siteSettings`（DB全設定）と `$this->authUser` を初期化。ビュー描画は `$this->render(string $view, array $data)` で共通データを自動注入。

**Admin/AdminController**（抽象）— 認証・`admin.access` チェック。個別権限は `$this->requirePermission('files.manage')` で追加検証。

**Upload** — POST 時に `bin2hex(random_bytes(16))` で UUID ファイル名、`bin2hex(random_bytes(32))` でダウンロードトークンを生成。

**Download** — パスワードなし→GETで即ストリーム。パスワードあり→GETでフォーム表示、POSTで `password_verify()` 後にストリーム。

**Mypage / Admin\Files** — ファイル削除時は `unlink()` で物理ファイルも必ず削除する。

## モデル設計

| モデル | テーブル | 主なメソッド |
|--------|---------|-------------|
| `FileModel` | `files` | `findByToken()`, `incrementDownload()`, `getExpired()` |
| `SettingModel` | `transfer_settings` | `getAll()`, `getValue()`, `setValue()`（upsert） |

`returnType = 'array'` のため `$file['id']` は文字列。`incrementDownload()` 等 `int` 引数には `(int)` キャストが必要。

## テーマ設計

設定キーを管理画面・公開面で分離：

| 設定キー | 対象 | 選択肢 |
|---------|------|-------|
| `admin_theme` | 管理画面（Tabler） | `auto` / `light` / `dark` |
| `public_theme` | 公開面 | `auto`, `light`, `dark`, `github`, `academic`, `onigiri`, `solarized`, `vue`, `monospace`, `night`, `monospace-dark` |

- `auto` → インラインJSで `prefers-color-scheme` を読んで `data-bs-theme` を付与
- ダーク固定テーマ（`dark`, `night`, `monospace-dark`）→ `data-bs-theme="dark"` + テーマCSS常時読み込み
- ダーク自動テーマ（`onigiri`, `solarized`, `vue`, `monospace`）→ `media="(prefers-color-scheme: dark)"` で `-dark.css` を切替

## 実装状況

| 機能 | 状態 |
|------|------|
| DB設計・マイグレーション・Docker | ✅ |
| Shield 認証（登録・ログイン・ログアウト） | ✅ |
| ファイルアップロード（`/upload`） | ✅ |
| ダウンロード・パスワード保護（`/download/{token}`） | ✅ |
| マイページ（`/mypage`） | ✅ |
| 管理画面（Dashboard, Files, Users, Settings） | ✅ |
| 期限切れ自動削除（`spark files:cleanup`） | ✅ |
| アップロード上限サイズの設定化 | ❌ 未対応 |
| ゲストアップロード（`user_id = null`） | ❌ 未対応 |

---

## 基本方針

- 目的は「安全・保守性・差分最小」の改善
- 常に既存設計を尊重し、壊さない

## 出力ルール

- 原則 diff 形式で出力する
- 変更対象ファイル名を必ず明示する
- 修正理由は各変更につき1行まで

## 禁止事項

- ファイル全文の再出力
- 推測での仕様追加
- 破壊的変更は禁止

## 判断基準

- 可読性 > パフォーマンス
- 明示性 > 魔法
- 静的解析・型安全を優先
