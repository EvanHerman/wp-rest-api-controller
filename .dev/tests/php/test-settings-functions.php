<?php

class Test_WP_REST_API_Controller_Settings extends WP_UnitTestCase {

	/**
	 * Settings class instance.
	 */
	private $settings_instance;

	/**
	 * Created post ID.
	 */
	private $post_id;

	/**
	 * Test nonce.
	 */
	private $test_nonce;

	function setUp(): void {

		parent::setUp();

		wp_set_current_user( self::factory()->user->create( [
			'role' => 'administrator',
		] ) );

		$this->settings_instance = new WP_REST_API_Controller_Settings();

		$this->post_id = $this->factory->post->create(
			[
				'post_title'  => 'Custom Post',
				'post_type'   => 'post',
				'post_status' => 'publish',
			]
		);

		$this->test_nonce = wp_create_nonce( 'clear_wp_rest_api_controller_cache' );

		add_filter( 'wp_redirect', 'wp_redirect_halt_redirect', 1, 2 );

	}

	function tearDown(): void {

		parent::tearDown();

		wp_delete_post( $this->post_id, true );

		remove_filter( 'wp_redirect', 'wp_redirect_halt_redirect', 1 );

		$_GET = [];

	}

	/**
	 * Test admin_init hook is setup as expected.
	 *
	 * @since NEXT
	 */
	function testInitSettingsAPIInitHook() {

		$this->assertSame(
			10,
			has_action( 'admin_init', [ $this->settings_instance, 'wp_rest_api_controller_settings_api_init' ] )
		);

	}

	/**
	 * Test admin_init hook is setup as expected.
	 *
	 * @since NEXT
	 */
	function testDeleteAPICacheHook() {

		$this->assertSame(
			10,
			has_action( 'admin_init', [ $this->settings_instance, 'wp_rest_api_controller_delete_api_cache' ] )
		);

	}

	/**
	 * Test get_excluded_post_types returns what we expect.
	 *
	 * @since NEXT
	 */
	function testGetExcludedPostTypes() {

		$this->assertEquals(
			[
				'nav_menu_item',
				'wp_template',
				'wp_template_part',
			],
			$this->settings_instance->get_excluded_post_types()
		);

	}

	/**
	 * Test wp_rest_api_controller_excluded_post_type_slugs filter works as intended.
	 *
	 * @since NEXT
	 */
	function testGetExcludedPostTypesFilter() {

		add_filter(
			'wp_rest_api_controller_excluded_post_type_slugs',
			function( $excluded_post_types ) {
				$excluded_post_types[] = 'custom_value';
				return $excluded_post_types;
			}
		);

		$this->assertEquals(
			[
				'nav_menu_item',
				'wp_template',
				'wp_template_part',
				'custom_value'
			],
			$this->settings_instance->get_excluded_post_types()
		);

	}
	/**
	 * Test get_excluded_taxonomies returns what we expect.
	 *
	 * @since NEXT
	 */
	function testGetExcludedTaxonomies() {

		$this->assertEquals(
			[
				'nav_menu',
			],
			$this->settings_instance->get_excluded_taxonomies()
		);

	}

	/**
	 * Test wp_rest_api_controller_excluded_taxonomy_slugs filter works as intended.
	 *
	 * @since NEXT
	 */
	function testGetExcludedTaxonomiesFilter() {

		add_filter(
			'wp_rest_api_controller_excluded_taxonomy_slugs',
			function( $excluded_taxonomies ) {
				$excluded_taxonomies[] = 'custom_value';
				return $excluded_taxonomies;
			}
		);

		$this->assertEquals(
			[
				'nav_menu',
				'custom_value'
			],
			$this->settings_instance->get_excluded_taxonomies()
		);

	}

	/**
	 * Test wp_rest_api_controller_settings_api_init registered the post_type settings.
	 *
	 * @since NEXT
	 */
	function testSettingsAPIInitPostType() {

		$this->settings_instance->wp_rest_api_controller_settings_api_init();

		global $wp_settings_fields;

		$this->assertArrayHasKey( 'wp_rest_api_controller_post_types_page', $wp_settings_fields['wp-rest-api-controller']['wp_rest_api_controller_setting_section'] );

	}

	/**
	 * Test wp_rest_api_controller_settings_api_init registered the post_type settings.
	 *
	 * @since NEXT
	 */
	function testSettingsAPIInitPostTypeOption() {

		$this->settings_instance->wp_rest_api_controller_settings_api_init();

		$this->assertTrue( ! empty( get_option( 'wp_rest_api_controller_post_types', array() ) ) );

	}

	/**
	 * Test wp_rest_api_controller_settings_api_init registered the taxonomies settings.
	 *
	 * @since NEXT
	 */
	function testSettingsAPIInitTaxonomies() {

		$this->settings_instance->wp_rest_api_controller_settings_api_init();

		global $wp_settings_fields;

		$this->assertArrayHasKey( 'wp_rest_api_controller_taxonomies_category', $wp_settings_fields['wp-rest-api-controller']['wp_rest_api_controller_setting_section'] );

	}

	/**
	 * Test wp_rest_api_controller_settings_api_init registered the taxonomies settings.
	 *
	 * @since NEXT
	 */
	function testSettingsAPIInitTaxonomiesOption() {

		$this->settings_instance->wp_rest_api_controller_settings_api_init();

		global $wp_settings_fields;

		$this->assertTrue( ! empty( get_option( 'wp_rest_api_controller_taxonomies', array() ) ) );

	}

	/**
	 * Test get_registered_post_types.
	 *
	 * @since NEXT
	 */
	function testGetRegisteredPostTypes() {

		$this->assertEquals(
			[
				'post',
				'page',
				'attachment',
				'revision',
				'custom_css',
				'customize_changeset',
				'oembed_cache',
				'user_request',
				'wp_block',
				'wp_global_styles',
				'wp_navigation',
			],
			array_keys( $this->settings_instance->get_registered_post_types() )
		);

	}

	/**
	 * Test wp_rest_api_controller_post_types filter works to remove unwanted post types.
	 *
	 * @since NEXT
	 */
	function testGetRegisteredPostTypesFilter() {

		// Test removing the custom_css post type.
		add_filter(
			'wp_rest_api_controller_post_types',
			function( $post_types ) {
				unset( $post_types['custom_css'] );
				return $post_types;
			}
		);

		$this->assertEquals(
			[
				'post',
				'page',
				'attachment',
				'revision',
				'customize_changeset',
				'oembed_cache',
				'user_request',
				'wp_block',
				'wp_global_styles',
				'wp_navigation',
			],
			array_keys( $this->settings_instance->get_registered_post_types() )
		);

	}

	/**
	 * Test get_registered_taxonomies.
	 *
	 * @since NEXT
	 */
	function testGetRegisteredTaxonomies() {

		$this->assertEquals(
			[
				'category',
				'post_tag',
				'link_category',
				'post_format',
				'wp_theme',
				'wp_template_part_area',
			],
			array_keys( $this->settings_instance->get_registered_taxonomies() )
		);

	}

	/**
	 * Test wp_rest_api_controller_taxonomies filter works to remove unwanted taxonomies.
	 *
	 * @since NEXT
	 */
	function testGetRegisteredTaxonomiesFilter() {

		// Test removing the link_category taxonomy.
		add_filter(
			'wp_rest_api_controller_taxonomies',
			function( $taxonomies ) {
				unset( $taxonomies['link_category'] );
				return $taxonomies;
			}
		);

		$this->assertEquals(
			[
				'category',
				'post_tag',
				'post_format',
				'wp_theme',
				'wp_template_part_area',
			],
			array_keys( $this->settings_instance->get_registered_taxonomies() )
		);

	}

	/**
	 * Test the wp_rest_api_controller_setting_section_callback_function callback.
	 *
	 * @since NEXT
	 */
	function testSettingSectionCallback() {

		$this->expectOutputRegex( '/<li class="active rest-controller-tabs-list-item active"><a data-tab="post-types">Post Types<\/a><\/li>/' );

		$this->settings_instance->wp_rest_api_controller_setting_section_callback_function();

	}

	/**
	 * Test the wp_rest_api_controller_setting_section_setting_callback_function callback.
	 *
	 * @since NEXT
	 */
	function testSettingSectionSettingCallback() {

		$this->expectOutputRegex( '/Access the Post post type via the REST API at the following URL./' );

		$this->settings_instance->wp_rest_api_controller_setting_section_setting_callback_function(
			array(
				'option_id'      => 'wp_rest_api_controller_post_types_post',
				'post_type_name' => 'Posts',
				'post_type_slug' => 'post',
			)
		);

	}

	/**
	 * Test the meta rendered by wp_rest_api_controller_setting_section_setting_callback_function callback.
	 *
	 * @since NEXT
	 */
	function testSettingSectionMetaSettingCallback() {

		update_post_meta( $this->post_id, 'custom_meta', 'Custom Value' );

		$this->expectOutputRegex( '/<td>custom_meta<\/td>/' );

		$this->settings_instance->wp_rest_api_controller_setting_section_setting_callback_function(
			array(
				'option_id'      => 'wp_rest_api_controller_post_types_post',
				'post_type_name' => 'Posts',
				'post_type_slug' => 'post',
			)
		);

	}

	/**
	 * Test the wp_rest_api_controller_setting_section_setting_tax_callback_function callback.
	 *
	 * @since NEXT
	 */
	function testSettingSectionSettingTaxCallback() {

		$this->expectOutputRegex( '/Access the Categories taxonomy via the REST API at the following URL./' );

		$this->settings_instance->wp_rest_api_controller_setting_section_setting_tax_callback_function(
			array(
				'option_id' => 'wp_rest_api_controller_taxonomies_category',
				'tax_slug'  => 'category',
			)
		);

	}

	/**
	 * Test the meta rendered by wp_rest_api_controller_setting_section_setting_tax_callback_function callback.
	 *
	 * @since NEXT
	 */
	function testSettingSectionSettingTaxMetaCallback() {

		$category = get_term_by( 'slug', 'uncategorized', 'category' );

		update_term_meta( $category->term_id, 'custom_meta', 'Custom Value' );

		$this->expectOutputRegex( '/<td>custom_meta<\/td>/' );

		$this->settings_instance->wp_rest_api_controller_setting_section_setting_tax_callback_function(
			array(
				'option_id' => 'wp_rest_api_controller_taxonomies_category',
				'tax_slug'  => 'category',
			)
		);

	}

	/**
	 * Test retrieve_post_type_meta_keys returns the proper meta keys for the specified post type.
	 *
	 * @since NEXT
	 */
	function testSettingRetrievePostTypeMetaKeys() {

		update_post_meta( $this->post_id, 'custom_meta', 'Custom Meta Value' );
		update_post_meta( $this->post_id, 'custom_meta_two', 'Custom Meta Value Two' );

		$this->assertEquals(
			[
				'_pingme',
				'_encloseme',
				'custom_meta',
				'custom_meta_two',
			],
			$this->settings_instance->retrieve_post_type_meta_keys( 'post' )
		);

	}

	/**
	 * Test retrieve_taxonomy_meta_keys returns the proper meta keys for the specified post type.
	 *
	 * @since NEXT
	 */
	function testSettingRetrieveTaxonomyMetaKeys() {

		$category = get_term_by( 'slug', 'uncategorized', 'category' );

		update_term_meta( $category->term_id, 'custom_meta', 'Custom Value' );
		update_term_meta( $category->term_id, 'custom_meta_two', 'Custom Value Two' );

		$this->assertEquals(
			[
				'custom_meta',
				'custom_meta_two',
			],
			$this->settings_instance->retrieve_taxonomy_meta_keys( 'category' )
		);

	}

	/**
	 * Test wp_rest_api_controller_delete_api_cache does not redirect when api-cache-cleared is false.
	 *
	 * @since NEXT
	 */
	function testSettingDeleteAPICacheNoRedirect() {

		$_GET['_wpnonce'] = false;

		try {

			$this->settings_instance->wp_rest_api_controller_delete_api_cache();
			$data = [];

		} catch ( Exception $e ) {

			$data = json_decode( $e->getMessage(), true );

		}

		$this->assertEmpty( $data );

	}

	/**
	 * Test wp_rest_api_controller_delete_api_cache redirects when api-cache-cleared is false.
	 *
	 * @since NEXT
	 */
	function testSettingDeleteAPICacheRedirect() {

		$_GET['_wpnonce'] = wp_create_nonce( 'clear-api-cache' );

		try {

			$this->settings_instance->wp_rest_api_controller_delete_api_cache();
			$data = [];

		} catch ( Exception $e ) {

			$data = json_decode( $e->getMessage(), true );

		}

		$this->assertEquals(
			[
				true,
				true,
				true,
			],
			[
				! empty( $data ),
				'http://example.org/wp-admin/tools.php?page=wp-rest-api-controller-settings&wp-rest-api-cache-cleared=true' === $data['location'],
				302 === $data['status']
			]
		);

	}

}
