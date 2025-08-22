<?php

/**
 * プラグインの初期フック登録を WP_Mock で検証。
 */
class PluginInitTest extends BaseWPTestCase {
    public function test_add_action_hooks_are_registered() {
        // WordPress 関数をスタブ。
        \WP_Mock::userFunction( 'plugin_dir_path', [
            'return' => function ( $file ) {
                return dirname( $file ) . '/';
            },
        ] );

        \WP_Mock::userFunction( 'plugin_basename', [
            'return' => function ( $file ) {
                return basename( dirname( $file ) ) . '/' . basename( $file );
            },
        ] );

        // add_action が期待通り呼ばれること。
        \WP_Mock::expectActionAdded( 'plugins_loaded', \WP_Mock\Functions::type( 'callable' ) );
        \WP_Mock::expectActionAdded( 'init', \WP_Mock\Functions::type( 'callable' ) );

        // プラグインファイルを読み込むと、上記フック登録が実行される。
        /** @psalm-suppress UnresolvableInclude */
        require_once dirname( __DIR__, 1 ) . '/../bf-simple-backup.php';
    }
}

