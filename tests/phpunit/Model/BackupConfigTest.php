<?php

use PHPUnit\Framework\TestCase;

class BackupConfigTest extends TestCase {

    public function test_default_options_shape() {
        $config  = new BF_SB_Model_BackupConfig();
        $options = $config->get_default_options();

        $this->assertIsArray( $options );
        $this->assertArrayHasKey( 'schedule', $options );
        $this->assertArrayHasKey( 'time', $options );
        $this->assertArrayHasKey( 'storage', $options );
        $this->assertArrayHasKey( 'keep_versions', $options );
    }

    public function test_default_values() {
        $config  = new BF_SB_Model_BackupConfig();
        $options = $config->get_default_options();

        $this->assertSame( 'daily', $options['schedule'] );
        $this->assertSame( '02:00', $options['time'] );
        $this->assertSame( 'local', $options['storage'] );
        $this->assertSame( 5, $options['keep_versions'] );
    }
}

