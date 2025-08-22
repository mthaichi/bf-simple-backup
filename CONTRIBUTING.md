# コントリビューションガイド

## ブランチ運用
- `main`: 常にデプロイ可能な安定ブランチ。直接push禁止（ブランチ保護を推奨）。
- `feature/*`: 機能開発用ブランチ。`main` から作成し、PRでマージ。
- `fix/*`: バグ修正用ブランチ。`main` から作成し、PRでマージ。

## ブランチ保護の推奨設定（GitHubリポジトリ設定 > Branches）
- Require a pull request before merging（少なくとも1名のレビュー）
- Require status checks to pass before merging（CI: PHPUnit）
- Require linear history（任意）
- Dismiss stale pull request approvals when new commits are pushed（任意）

## コーディング規約
- WordPressコーディング規約に準拠。
- 外部入力はコントローラで受け、モデルで処理、ビューで表示。
- ビュー以外のクラス/メソッド追加時は必ず PHPUnit テストを追加。

## 開発の流れ
1. 課題作成（Issue）
2. ブランチ作成（`feature/xxx` など）
3. 実装 + テスト追加
4. `composer install` して `composer test` でテスト実行
5. PR作成（テンプレートに従う）

