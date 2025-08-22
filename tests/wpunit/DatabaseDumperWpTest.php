<?php

class DatabaseDumperWpTest extends WP_UnitTestCase {
    protected $table;
    protected $tmpfile;

    public function setUp(): void {
        parent::setUp();
        global $wpdb;
        $this->table = $wpdb->prefix . 'bf_sb_test';
        $wpdb->query( 'DROP TABLE IF EXISTS `' . $this->table . '`' );
        $wpdb->query( 'CREATE TABLE `' . $this->table . '` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8' );
        $wpdb->insert( $this->table, array( 'name' => 'alpha' ) );
        $wpdb->insert( $this->table, array( 'name' => 'beta' ) );

        // プラグインのオートローダを読み込んでクラスを解決可能に。
        require_once dirname( __DIR__, 2 ) . '/bf-simple-backup.php';

        $this->tmpfile = sys_get_temp_dir() . '/bf_sb_dump_' . uniqid() . '.sql';
    }

    public function tearDown(): void {
        global $wpdb;
        if ( ! empty( $this->table ) ) {
            $wpdb->query( 'DROP TABLE IF EXISTS `' . $this->table . '`' );
        }
        if ( $this->tmpfile && file_exists( $this->tmpfile ) ) {
            @unlink( $this->tmpfile );
        }
        parent::tearDown();
    }

    public function test_dump_and_restore_custom_table() {
        global $wpdb;

        $dumper = new BF_SB_Model_DatabaseDumper();
        $this->assertTrue( $dumper->dump( $this->tmpfile ), $dumper->get_last_error() );
        $this->assertFileExists( $this->tmpfile );

        // ダンプ内容にテーブル名とデータが含まれるか。
        $sql = file_get_contents( $this->tmpfile );
        $this->assertStringContainsString( $this->table, $sql );
        $this->assertStringContainsString( 'alpha', $sql );
        $this->assertStringContainsString( 'beta', $sql );

        // テーブルを削除してからリストア。
        $wpdb->query( 'DROP TABLE `' . $this->table . '`' );
        $this->assertNotContains( $this->table, $wpdb->get_col( 'SHOW TABLES' ) );

        $this->assertTrue( $dumper->restore( $this->tmpfile ), $dumper->get_last_error() );
        $this->assertContains( $this->table, $wpdb->get_col( 'SHOW TABLES' ) );

        $rows = $wpdb->get_results( 'SELECT * FROM `' . $this->table . '` ORDER BY id ASC', ARRAY_A );
        $this->assertCount( 2, $rows );
        $this->assertSame( 'alpha', $rows[0]['name'] );
        $this->assertSame( 'beta', $rows[1]['name'] );
    }
}

