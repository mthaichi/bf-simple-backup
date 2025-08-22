<?php

// WordPress 非依存のシンプルなユニットテストのためのブートストラップ。

// プロジェクトルート。
$root = dirname( dirname( __DIR__ ) );

// inc ディレクトリを読み込み。
require_once $root . '/inc/Model/BackupConfig.php';

