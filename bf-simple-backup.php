<?php
/**
 * Plugin Name: BF Simple Backup
 * Plugin URI:  https://github.com/mthaichi/bf-simple-backup
 * Description: データベースをダンプし、WordPressディレクトリをZIP化して、Google Drive または Dropbox に保存する簡易バックアッププラグイン。擬似Cronで定期バックアップにも対応。
 * Version:     0.1.0
 * Author:      BF Simple Backup
 * Text Domain: bf-simple-backup
 * Domain Path: /language
 */

// 直接アクセスの防止。
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 定数定義。
if ( ! defined( 'BF_SB_VERSION' ) ) {
    define( 'BF_SB_VERSION', '0.1.0' );
}
if ( ! defined( 'BF_SB_PLUGIN_FILE' ) ) {
    define( 'BF_SB_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BF_SB_PLUGIN_DIR' ) ) {
    define( 'BF_SB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// シンプルなオートローダー（名前空間は使わず、プレフィックスで解決）。
spl_autoload_register( function ( $class ) {
    if ( 0 !== strpos( $class, 'BF_SB_' ) ) {
        return;
    }

    $path = BF_SB_PLUGIN_DIR . 'inc/' . str_replace( 'BF_SB_', '', $class ) . '.php';
    $path = str_replace( '\\', '/', $path );

    if ( file_exists( $path ) ) {
        /** @psalm-suppress UnresolvableInclude */
        require_once $path;
    }
} );

// テキストドメイン読み込み。
add_action( 'plugins_loaded', function () {
    load_plugin_textdomain( 'bf-simple-backup', false, dirname( plugin_basename( __FILE__ ) ) . '/language' );
} );

// プラグイン起動のエントリ。最低限の初期化のみ実施。
add_action( 'init', function () {
    // ここで将来のルーティングやスケジュール初期化を行う。
} );

