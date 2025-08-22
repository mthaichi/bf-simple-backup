<?php

// 直接アクセスの防止。
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * バックアップ設定のモデル。
 */
class BF_SB_Model_BackupConfig {

    /**
     * 既定の設定値を返します。
     *
     * @return array
     */
    public function get_default_options() {
        return array(
            'schedule'      => 'daily',   // daily|weekly|monthly
            'time'          => '02:00',   // 24時間表記 HH:MM
            'storage'       => 'local',   // local|gdrive|dropbox
            'keep_versions' => 5,
        );
    }
}

