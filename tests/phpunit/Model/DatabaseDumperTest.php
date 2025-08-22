<?php

class DatabaseDumperTest extends BaseWPTestCase {
    public function test_get_wp_tables_filters_by_prefix() {
        // グローバル $wpdb のスタブを用意。
        $fake_wpdb = new class() {
            public $prefix = 'wp_';
            public function get_col( $query ) {
                return array( 'wp_posts', 'wp_users', 'other_table' );
            }
        };
        $GLOBALS['wpdb'] = $fake_wpdb;

        $dumper = new BF_SB_Model_DatabaseDumper();
        $tables = $dumper->get_wp_tables();

        $this->assertContains( 'wp_posts', $tables );
        $this->assertContains( 'wp_users', $tables );
        $this->assertNotContains( 'other_table', $tables );
    }
}

