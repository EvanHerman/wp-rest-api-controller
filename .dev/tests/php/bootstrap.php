<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {

	$_tests_dir = '/tmp/wordpress-tests-lib';

}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {

	require dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-rest-api-controller.php';

}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

function wp_redirect_halt_redirect( $location, $status ) {

	throw new \Exception(
		json_encode(
			[
				'location' => $location,
				'status'   => $status,
			]
		)
	);

}

require $_tests_dir . '/includes/bootstrap.php';
