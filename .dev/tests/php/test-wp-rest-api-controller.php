<?php

class Test_Simple_Taxonomy_Ordering extends WP_UnitTestCase {

	public $instance;

	function setUp(): void {

		parent::setUp();

		require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-rest-api-controller.php';

	}

	function tearDown(): void {

		parent::tearDown();

	}

	/**
	 * Test the constants are set.
	 *
	 * @since NEXT
	 */
	function testConstants() {

		$this->assertTrue( true );

	}

}
