<?php

// PHPUnit ブートストラップ（WP_Mock 対応）

// プロジェクトルート。
$root = dirname( dirname( __DIR__ ) );

// Composer オートローダ。
$autoload = $root . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
    /** @psalm-suppress UnresolvableInclude */
    require_once $autoload;
}

// WordPress の定数を最低限定義。
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', $root . '/' );
}

// 直接読み込む必要のあるクラス。
/** @psalm-suppress UnresolvableInclude */
require_once $root . '/inc/Model/BackupConfig.php';
/** @psalm-suppress UnresolvableInclude */
require_once $root . '/inc/Model/DatabaseDumper.php';
