# Job CRM

転職活動を効率化するための CRM アプリケーション。
Laravel 12 + Inertia.js + Vue 3 + Tailwind CSS 構成で構築。

---

## 📦 技術スタック

- Laravel 12 (PHP 8.3)
- Inertia.js + Vue 3 (Composition API)
- Tailwind CSS
- MySQL 8.x
- Docker (php-fpm, nginx, node, mysql)

---

## 🚀 セットアップ

```bash
# コンテナ起動
docker compose up -d

# Laravel 初期化
docker compose exec app composer install
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate

# フロントエンド
docker compose exec node npm install
docker compose exec node npm run dev
````

---

## 🗄️ ER 図

```
companies      - 企業
interactions   - 面談ややりとり履歴
tags           - タグ（エンジニア、上場企業、リモートなど）
company_tag    - 企業とタグの中間テーブル
```

* **companies**

  * name, status（未応募、面接中、内定、辞退...）
  * hope\_level（1〜5）
  * tags（json形式）
  * contact\_person, email, phone, website\_url, memo

* **interactions**

  * company\_id（FK）
  * interaction\_date, type（電話/面談/メール）
  * memo, summary（GPT要約）

* **tags**

  * name（一意）

* **company\_tag**

  * company\_id, tag\_id

---

## 🌱 シーディング

* `UserSeeder` : ログイン用ユーザー
* `CompanySeeder` : ダミー企業
* `InteractionSeeder` : ダミー履歴
* `TagsSeeder` : 技術・企業属性・条件などのタグ
* `CompanyTagSeeder` : 各企業にランダムで 1〜3 個のタグを付与

実行方法:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

## 🔍 検索・フィルタ機能

* 企業一覧で以下の条件検索が可能:

  * 企業名（部分一致）
  * ステータス
  * 希望度
  * タグ（複数選択時は **AND 検索**）

実装例（`CompanyController@index`）:

```php
if ($request->filled('tags')) {
    $tagNames = $request->input('tags');
    $query->whereHas(
        'tags',
        fn ($q) => $q->whereIn('tags.name', $tagNames),
        '=',
        count($tagNames)
    );
}
```

---

## 📊 ダッシュボード

* 登録企業数、面接中、内定獲得 のサマリーカード
* 最近の更新履歴（タイムライン形式）

### カードクリック時の遷移

* **登録企業数** → `/companies`
* **面接中** → `/companies?status=面接中`
* **内定獲得** → `/companies?status=内定`

---

## 👤 ユーザー管理

* プロフィール編集画面を共通レイアウト（`AppLayout`）に統一
* トップバー右上のユーザーアイコンメニューでユーザー名を表示

  * `usePage().props.auth.user.name` を利用

---

## 🤖 AI 連携

* 企業詳細ページから「AIアクション提案」を生成
* OpenAI API を利用
* 直近の履歴、メモ、ステータスをもとに提案文を返却

---

## ✅ テスト

* Feature / Unit テストはすべて **PHPUnit 12 対応済み（属性ベース）**
* Company / Interaction / AI 提案機能などを網羅

実行方法:

```bash
docker compose exec app php artisan test
```

---

## 今後の改善 TODO

* axios interceptor によるエラーハンドリング統一（429/503 UI）
* E2E テスト（Cypress 導入）
* 企業タグの UI 改善（選択型入力）
* AI 提案の履歴保存と活用
