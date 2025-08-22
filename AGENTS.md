# BF Simple Backup Plugin
これは簡易的なバックアップを提供するWordPressのプラグインです。
データベースの内容をダンプし、WordPressのルートディレクトリに保存、WordPressのディレクトリごとZIPに圧縮し、Google Drive もしくは Dropboxの指定された場所に保存します。WordPressの擬似的Cronで指定した時刻に定期バックアップもできます。
PHP7.4以上で動作します。

# 共通ルール
- 常に日本語で解答してください。
- JavaScriptはjQueryをなるべく使わないでください。
- 名前空間を使ってください。\Breadfish\SimpleBackup

# ディレクトリ構造
- inc/  ...  PHPのクラスはここに保存します。用途に応じてサブディレクトリに分けます。
- inc/Controller ... コントローラ関連のクラス
- inc/Model ... モデル関連のクラス
- inc/View ... ビュー関連のクラス
- language/ ... 翻訳関係のファイルです。
- assets/ ... 画像、外部JS/CSSをここに保存します。
- tests/ ... テスト関連
- tests/phpunit/ ... PHPUnit関連

# コーディングルール
- WordPressのコーディング規約に則ってください。関数の括弧の内側左右にスペースをいれてください。
- 外部の入力を受け付ける「コントローラ」と、ビジネスロジックを担う「モデル」、表示部分を担う「ビュー」を切り分けてください。
- ビュー以外のクラスを追加する時、メソッドを追加する時、対応するPHPUnitテストを必ず書いてください。

