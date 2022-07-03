<?php

class Test_WP_REST_API_Controller_Admin extends WP_UnitTestCase {

	private $admin_instance;

	function setUp(): void {

		parent::setUp();

		wp_set_current_user( self::factory()->user->create( [
			'role' => 'administrator',
		] ) );

		$this->admin_instance = new WP_REST_API_Controller_Admin();

		$_GET = [];

	}

	function tearDown(): void {

		parent::tearDown();

		global $wp_scripts;

		$wp_scripts = null;

		$_GET = [];

	}

	/**
	 * Test WP_REST_API_Controller_Settings class exists.
	 *
	 * @since NEXT
	 */
	function testSettingsClassExists() {

		$this->assertTrue( class_exists( 'WP_REST_API_Controller_Settings' ) );

	}

	/**
	 * Test admin_menu hook is setup as expected.
	 *
	 * @since NEXT
	 */
	function testAdminMenuHook() {

		$this->assertSame(
			10,
			has_action( 'admin_menu', [ $this->admin_instance, 'register_wp_rest_api_controller_submenu_page' ] )
		);

	}

	/**
	 * Test admin_notices hook is setup as expected.
	 *
	 * @since NEXT
	 */
	function testAdminNoticesHook() {

		$this->assertSame(
			10,
			has_action( 'admin_notices', [ $this->admin_instance, 'wp_rest_api_controller_admin_notices' ] )
		);

	}

	/**
	 * Test removable_query_args hook is setup as expected.
	 *
	 * @since NEXT
	 */
	function testRemovableQueryArgsHook() {

		$this->assertSame(
			10,
			has_action( 'removable_query_args', [ $this->admin_instance, 'remove_custom_query_args' ] )
		);

	}

	/**
	 * Test admin_enqueue_scripts hook is setup as expected.
	 *
	 * @since NEXT
	 */
	function testAdminEnqueueScriptsHook() {

		$this->assertSame(
			10,
			has_action( 'admin_enqueue_scripts', [ $this->admin_instance, 'enqueue_scripts' ] )
		);

	}

	/**
	 * Test enqueue_scripts doesn't run on pages that aren't ours.
	 *
	 * @since NEXT
	 */
	function testAdminScriptNotEnqueued() {

		global $wp_scripts;

		set_current_screen( 'admin.php' );

		do_action( 'admin_enqueue_scripts' );

		$this->admin_instance->enqueue_scripts();

		$this->assertFalse( array_key_exists( 'wp-rest-api-controller-admin', $wp_scripts->registered ) );

	}

	/**
	 * Test enqueue_scripts enqueues styles on our settings page.
	 *
	 * @since NEXT
	 */
	function testAdminStylesEnqueued() {

		set_current_screen( 'tools_page_wp-rest-api-controller-settings' );

		$this->admin_instance->enqueue_scripts();

		$this->assertEquals(
			[
				true,
				true,
			],
			[
				wp_style_is( 'tipso.css', 'enqueued' ),
				wp_style_is( 'wp-rest-api-controller-admin', 'enqueued' ),
			]
		);

	}

	/**
	 * Test enqueue_scripts enqueues scripts on our settings page.
	 *
	 * @since NEXT
	 */
	function testAdminScriptsEnqueued() {

		set_current_screen( 'tools_page_wp-rest-api-controller-settings' );

		$this->admin_instance->enqueue_scripts();

		global $wp_scripts;

		$this->assertEquals(
			[
				true,
				true,
				'tipso.js'
			],
			[
				wp_script_is( 'tipso.js', 'enqueued' ),
				wp_script_is( 'wp-rest-api-controller-admin', 'enqueued' ),
				$wp_scripts->registered['wp-rest-api-controller-admin']->deps[0]
			]
		);

	}

	/**
	 * Test wp-rest-api-controller-admin localized data.
	 *
	 * @since NEXT
	 */
	function testAdminScriptsLocalizedData() {

		set_current_screen( 'tools_page_wp-rest-api-controller-settings' );

		$this->admin_instance->enqueue_scripts();

		global $wp_scripts;

		$localized_data = $wp_scripts->registered['wp-rest-api-controller-admin']->extra['data'];

		$this->assertEquals(
			'var rest_api_controller_localized_admin_data = {"disabled_notice":"This post type is disabled. Enable it and save the settings to access this link."};',
			$wp_scripts->registered['wp-rest-api-controller-admin']->extra['data']
		);

	}

	/**
	 * Test that the submenu page is registered.
	 *
	 * @since NEXT
	 */
	function testSubmenuPage() {

		$this->admin_instance->register_wp_rest_api_controller_submenu_page();

		$this->assertNotEmpty( menu_page_url( 'wp-rest-api-controller-settings', false ) );

	}

	/**
	 * Test that the submenu page markup is rendered.
	 *
	 * @since NEXT
	 */
	function testSubmenuPageMarkupExists() {

		$this->expectOutputRegex( '/<h1>WP REST API Controller Settings<\/h1>/' );

		$this->admin_instance->wp_rest_api_controller_submenu_page_callback();

	}

	/**
	 * Test that settings updated admin notice shows when the settings are updated.
	 *
	 * @since NEXT
	 */
	function testSettingsUpdatedAdminNotice() {

		$_GET['page'] = 'wp-rest-api-controller-settings';
		$_GET['settings-updated'] = true;

		$this->expectOutputRegex( '/<div class="notice notice-success"><p>Settings have been successfully updated.<\/p><\/div>/' );

		( new WP_REST_API_Controller_Admin() )->wp_rest_api_controller_admin_notices();

	}

	/**
	 * Test that WP REST API admin notice shows when the cache is cleared.
	 *
	 * @since NEXT
	 */
	function testRESTAPICacheClearedAdminNotice() {

		$_GET['page'] = 'wp-rest-api-controller-settings';
		$_GET['wp-rest-api-cache-cleared'] = true;

		$this->expectOutputRegex( '/<div class="notice notice-success"><p>The WP REST API Controller cache has been cleared, and the post type and meta data lists below have been updated.<\/p><\/div>/' );

		( new WP_REST_API_Controller_Admin() )->wp_rest_api_controller_admin_notices();

	}

	/**
	 * Test that our custom query arg is in remove_custom_query_args.
	 *
	 * @since NEXT
	 */
	function testRemovableQueryArg() {

		$this->assertEquals(
			$this->admin_instance->remove_custom_query_args( [] ),
			[
				'wp-rest-api-cache-cleared',
			]
		);

	}

}
