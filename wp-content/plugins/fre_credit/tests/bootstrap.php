<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = 'D:/xampp/htdocs/unittest/tests/phpunit/';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	switch_theme('freelanceengine');
	require dirname( dirname( __FILE__ ) ) . '/fre-credit.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
