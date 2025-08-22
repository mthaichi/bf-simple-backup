<?php

use PHPUnit\Framework\TestCase;

/**
 * WP_Mock を使うテストのベースクラス。
 */
abstract class BaseWPTestCase extends TestCase {
    protected function setUp(): void {
        parent::setUp();
        if ( class_exists( '\\WP_Mock' ) ) {
            \WP_Mock::setUp();
        }
    }

    protected function tearDown(): void {
        if ( class_exists( '\\WP_Mock' ) ) {
            \WP_Mock::assertExpectations();
            \WP_Mock::tearDown();
        }
        parent::tearDown();
    }
}

