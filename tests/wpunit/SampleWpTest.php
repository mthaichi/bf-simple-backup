<?php

class SampleWpTest extends WP_UnitTestCase {
    public function test_wp_is_loaded() {
        $this->assertTrue( function_exists( 'add_action' ) );
        $this->assertTrue( did_action( 'muplugins_loaded' ) >= 0 );
    }

    public function test_option_crud() {
        $key = '_bf_sb_sample_option';
        $val = 'hello';
        $this->assertTrue( add_option( $key, $val ) );
        $this->assertSame( $val, get_option( $key ) );
        $this->assertTrue( delete_option( $key ) );
    }
}

