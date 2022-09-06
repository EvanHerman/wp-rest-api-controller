<?php

class Test_WP_REST_API_Controller extends WP_UnitTestCase {

	public $post_id;

	public $taxonomy_id;

	/**
	 * Test REST Server
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

	function setUp(): void {

		parent::setUp();

		require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-rest-api-controller.php';

		$this->create_custom_post_type();

	}

	function tearDown(): void {

		parent::tearDown();

		global $wp_rest_server;

		$wp_rest_server = null;

		wp_delete_post( $this->post_id, true );

	}

	/**
	 * Setup the rest API.
	 */
	private function setup_rest_api() {

		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;

		do_action( 'rest_api_init' );

	}

	/**
	 * Update the wp_rest_api_controller_post_types option array and wp_rest_api_controller_post_types_${post_type}
	 *
	 * @param array $post_type_data. Expected format:
	 * array(
	 *   'post_type' => 'custom_post_type', // required.
	 *   'active'    => true, // optional. default: true
	 *   'rest_base' => 'test-custom_post_type', // optional. default: ${post_type_value}.
	 *   'meta_data' => [], // optional. default: [].
	 * )
	 */
	private function update_post_types_option( $post_type_data = array() ) {
		update_option( 'wp_rest_api_controller_post_types', array( $post_type_data['post_type'] ) );
		update_option(
			"wp_rest_api_controller_post_types_{$post_type_data['post_type']}",
			[
				'active'    => $post_type_data['enabled'] ?? true,
				'rest_base' => $post_type_data['rest_base'] ?? $post_type_data['post_type'],
				'meta_data' => $post_type_data['meta_data'] ?? [],
			]
		);
	}

	/**
	 * Update the wp_rest_api_controller_taxonomies option array and wp_rest_api_controller_taxonomies_${taxonomy}
	 *
	 * @param array $taxonomy_data. Expected format:
	 * array(
	 *   'slug'      => 'custom_taxonomy', // required.
	 *   'meta_data' => array( // optional
	 *     'meta_key' => array(
	 *       'active'            => true,
	 *       'original_meta_key' => 'custom_taxonomy',
	 *       'custom_key'        => 'something_custom',
	 *     )
	 *   ),
	 * )
	 */
	private function update_taxonomies_option( $taxonomy_data = array() ) {
		update_option( 'wp_rest_api_controller_taxonomies', array( $taxonomy_data['slug'] ) );
		update_option(
			"wp_rest_api_controller_taxonomies_{$taxonomy_data['slug']}",
			[
				'active'    => $taxonomy_data['active'] ?? true,
				'rest_base' => $taxonomy_data['rest_base'] ?? $taxonomy_data['slug'],
				'meta_data' => $taxonomy_data['meta_data'] ?? [],
			]
		);
	}

	/**
	 * Create a custom post type to test the endpoints with.
	 */
	private function create_custom_post_type() {

		$labels = array(
			'name'                  => _x( 'Custom Post Types', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'Custom Post Type', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'Custom Post Types', 'text_domain' ),
			'name_admin_bar'        => __( 'Custom Post Type', 'text_domain' ),
			'archives'              => __( 'Custom Post Type Archives', 'text_domain' ),
			'attributes'            => __( 'Custom Post Type Attributes', 'text_domain' ),
			'parent_item_colon'     => __( 'Parent Custom Post Type:', 'text_domain' ),
			'all_items'             => __( 'All Custom Post Types', 'text_domain' ),
			'add_new_item'          => __( 'Add New Custom Post Type', 'text_domain' ),
			'add_new'               => __( 'Add New', 'text_domain' ),
			'new_item'              => __( 'New Item', 'text_domain' ),
			'edit_item'             => __( 'Edit Item', 'text_domain' ),
			'update_item'           => __( 'Update Custom Post Type', 'text_domain' ),
			'view_item'             => __( 'View Custom Post Type', 'text_domain' ),
			'view_items'            => __( 'View Custom Post Types', 'text_domain' ),
			'search_items'          => __( 'Search Custom Post Type', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'featured_image'        => __( 'Featured Image', 'text_domain' ),
			'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
			'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
			'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
			'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
			'items_list'            => __( 'Items list', 'text_domain' ),
			'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
		);

		$args = array(
			'label'               => __( 'Custom Post Type', 'text_domain' ),
			'description'         => __( 'Custom Post Type', 'text_domain' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array( 'category', 'post_tag' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);

		register_post_type( 'custom_post_type', $args );

		register_taxonomy(
			'custom_taxonomy',
			'custom_post_type',
			array(
				'hierarchical' => true,
				'label'        => 'Custom Taxonomy',
				'query_var'    => true,
				'show_in_rest' => true,
				'rest_base'    => 'custom_taxonomy',
			)
		);

		$this->post_id = $this->factory->post->create(
			[
				'post_title'  => 'Custom Title',
				'post_type'   => 'custom_post_type',
				'post_status' => 'publish',
			]
		);

		wp_set_object_terms( $this->post_id, 'Custom Taxonomy Value', 'custom_taxonomy' );

	}

	/**
	 * Test that the admin class is included.
	 */
	function testAdminClassExists() {

		new WP_REST_API_Controller();

		$this->assertTrue(
			class_exists( 'WP_REST_API_Controller_Admin' ),
			'WP_REST_API_Controller_Admin class is not available.'
		);

	}

	/**
	 * Test that the expose_post_type_api_endpoints hook exists and is on the expected priority.
	 */
	function testExposePostTypesHook() {

		$this->assertSame( PHP_INT_MAX, has_action( 'init', [ ( new WP_REST_API_Controller() ), 'expose_post_type_api_endpoints' ] ) );

	}

	/**
	 * Test that the expose_taxonomy_api_endpoints hook exists and is on the expected priority.
	 */
	function testExposeTaxonomiesHook() {

		$this->assertSame( PHP_INT_MAX, has_action( 'init', [ ( new WP_REST_API_Controller() ), 'expose_taxonomy_api_endpoints' ] ) );

	}

	/**
	 * Test that the append_post_type_meta_data_to_api hook exists and is on the expected priority.
	 */
	function testExposePostTypesMetaDataHook() {

		$this->assertSame( PHP_INT_MAX, has_action( 'rest_api_init', [ ( new WP_REST_API_Controller() ), 'append_post_type_meta_data_to_api' ] ) );

	}

	/**
	 * Test that the append_taxonomy_meta_data_to_api hook exists and is on the expected priority.
	 */
	function testExposeTaxonomiesMetaDataHook() {

		$this->assertSame( PHP_INT_MAX, has_action( 'rest_api_init', [ ( new WP_REST_API_Controller() ), 'append_taxonomy_meta_data_to_api' ] ) );

	}

	/**
	 * Test that the load_plugin_textdomain hook exists and is on the expected priority.
	 */
	function testLoadTextDomainHook() {

		$this->assertSame( 10, has_action( 'plugins_loaded', [ ( new WP_REST_API_Controller() ), 'load_plugin_textdomain' ] ) );

	}

	/**
	 * Test the post endpoint returns a 404 when 'post' is not in the stored post types option.
	 *
	 * @since NEXT
	 */
	function testPostEndPointNoStoredPostTypes() {

		( new WP_REST_API_Controller() )->expose_post_type_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/post' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );

	}

	/**
	 * Test the post endpoint returns a 404 when 'post' is not in the global $wp_post_types array.
	 *
	 * @since NEXT
	 */
	function testPostEndPointNoGlobalPostType() {

		$this->update_post_types_option(
			array(
				'post_type' => 'post',
				'rest_base' => 'posts',
			)
		);

		global $wp_post_types;

		unset( $wp_post_types['post'] );

		( new WP_REST_API_Controller() )->expose_post_type_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/post' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );

		// Resets the global $wp_post_types.
		do_action( 'init' );

	}

	/**
	 * Test the post endpoint returns a 404 when 'post' is not active.
	 *
	 * @since NEXT
	 */
	function testPostEndPointNotActive() {

		$this->update_post_types_option(
			array(
				'post_type' => 'post',
				'enabled'   => false,
				'rest_base' => 'posts',
			)
		);

		( new WP_REST_API_Controller() )->expose_post_type_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/post' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );

	}

	/**
	 * Test the core endpoint works as expected for default custom post type.
	 *
	 * @since NEXT
	 */
	function testCustomPostTypeEndpointCore() {

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_post_type/' . $this->post_id );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals(
			[
				200,
				'Custom Title',
			],
			[
				$response->get_status(),
				$data['title']['rendered']
			]
		);

	}

	/**
	 * Test the constants are set.
	 *
	 * @since NEXT
	 */
	function testCustomPostTypeEndpointCustom() {

		$this->update_post_types_option(
			array(
				'post_type' => 'custom_post_type',
				'rest_base' => 'test-custom_post_type',
			)
		);

		( new WP_REST_API_Controller() )->expose_post_type_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/test-custom_post_type/' . $this->post_id );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals(
			[
				200,
				'Custom Title',
			],
			[
				$response->get_status(),
				$data['title']['rendered']
			]
		);

	}

	/**
	 * Test the core category endpoint still returns a 200 when there are no stored taxonomies in the wp_rest_api_controller_post_types option.
	 *
	 * @since NEXT
	 */
	function testCategoryEndPointNoStoredTaxonomies() {

		( new WP_REST_API_Controller() )->expose_taxonomy_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/categories' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

	}

	/**
	 * Test the core custom_taxonomy endpoint returns a 404 when 'custom_taxonomy' is not in the global $wp_taxonomies.
	 *
	 * @since NEXT
	 */
	function testCustomTaxonomyEndPointGlobalTaxonomy() {

		$this->update_taxonomies_option( [ 'slug' => 'custom_taxonomy' ] );

		global $wp_taxonomies;

		unset( $wp_taxonomies['custom_taxonomy'] );

		( new WP_REST_API_Controller() )->expose_taxonomy_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_taxonomy' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );

		// Resets the global $wp_taxonomies.
		do_action( 'init' );

	}

	/**
	 * Test custom_taxonomy endpoint returns a 404 when the disabled.
	 *
	 * @since NEXT
	 */
	function testCustomTaxonomyEndPointDisabled() {

		$this->update_taxonomies_option(
			[
				'slug'   => 'custom_taxonomy',
				'active' => false,
			]
		);

		( new WP_REST_API_Controller() )->expose_taxonomy_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_taxonomy' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 404, $response->get_status() );

	}

	/**
	 * Test custom_taxonomy endpoint returns a 404 when the disabled.
	 *
	 * @since NEXT
	 */
	function testCustomTaxonomyEndPoint() {

		$this->update_taxonomies_option(
			[
				'slug'   => 'custom_taxonomy',
				'active' => true,
			]
		);

		( new WP_REST_API_Controller() )->expose_taxonomy_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_taxonomy' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

	}

	/**
	 * Test that our custom post type isnt' present in the array when the wp_rest_api_controller_post_types_post_{$custom_post_type} option is missing.
	 */
	function testPostTypeNotPresentWhenNoOption() {

		update_option( 'wp_rest_api_controller_post_types', array( 'custom_post_type' ) );
		delete_option( 'wp_rest_api_controller_post_types_custom_post_type' );

		$stored_post_types = ( new WP_REST_API_Controller() )->get_stored_post_types();

		$this->assertFalse( array_key_exists( 'custom_post_type', $stored_post_types ) );

	}

	/**
	 * Test custom_taxonomy endpoint returns a 200 when the wp_rest_api_controller_taxonomies_custom_taxonomy option is not present.
	 *
	 * @since NEXT
	 */
	function testCustomTaxonomyEndPointMissingOption() {

		$this->update_taxonomies_option( [ 'slug' => 'custom_taxonomy' ] );

		delete_option( 'wp_rest_api_controller_taxonomies_custom_taxonomy' );

		( new WP_REST_API_Controller() )->expose_taxonomy_api_endpoints();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_taxonomy' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );

	}

	/**
	 * Test get_stored_taxonomies returns the expected value.
	 *
	 * @since NEXT
	 */
	function testGetStoredTaxonomiesReturnsExpectedValue() {

		$this->update_taxonomies_option( [ 'slug' => 'custom_taxonomy' ] );

		$stored_taxonomies = ( new WP_REST_API_Controller() )->get_stored_taxonomies();

		$this->assertEquals(
			[
				'custom_taxonomy' => [
					'enabled'   => true,
					'rest_base' => 'custom_taxonomy',
					'meta_data' => [],
				],
			],
			$stored_taxonomies
		);

	}

	/**
	 * Test that custom_meta_data_callback returns the expected post meta.
	 *
	 * @since NEXT
	 */
	function testCustomMetaDataCallback() {

		$this->update_taxonomies_option(
			[
				'slug'      => 'custom_taxonomy',
				'meta_data' => [
					'custom_meta' => [
						'original_meta_key' => 'custom_meta',
						'custom_key'        => '',
					]
				]
			]
		);

		$taxonomy_data = (array) $this->factory->term->create_and_get(
			array(
				'taxonomy' => 'custom_taxonomy',
				'name'     => 'Custom Taxonomy'
			)
		);

		$taxonomy_data['id'] = $taxonomy_data['term_id'];

		update_term_meta( $taxonomy_data['term_id'], 'custom_meta', 'Custom Meta Value' );

		$custom_meta_data = ( new WP_REST_API_Controller() )->custom_meta_data_callback(
			$taxonomy_data,
			'custom_meta'
		);

		$this->assertEquals( 'Custom Meta Value', $custom_meta_data );

	}

	/**
	 * Test that custom_meta_data_callback returns null when meta_data value is set on the taxonomy option.
	 *
	 * @since NEXT
	 */
	function testCustomMetaDataCallbackNullNoMetaData() {

		$this->update_taxonomies_option(
			[
				'slug'      => 'custom_taxonomy',
				'meta_data' => []
			]
		);

		$taxonomy_data = (array) $this->factory->term->create_and_get(
			array(
				'taxonomy' => 'custom_taxonomy',
				'name'     => 'Custom Taxonomy'
			)
		);

		$taxonomy_data['id'] = $taxonomy_data['term_id'];

		update_term_meta( $taxonomy_data['term_id'], 'custom_meta', 'Custom Meta Value' );

		$custom_meta_data = ( new WP_REST_API_Controller() )->custom_meta_data_callback(
			$taxonomy_data,
			'custom_meta'
		);

		$this->assertNull( $custom_meta_data );

	}

	/**
	 * Test get_post_type_rest_base() returns expected values.
	 *
	 * @since NEXT
	 */
	function testGetPostTypeRestBase() {

		register_post_type(
			'movies',
			array(
				'labels'       => array(
					'name'          => 'Movies',
					'singular_name' => 'Movie',
				),
				'public'       => true,
				'show_in_rest' => true,
			)
		);

		$this->assertEquals(
			[
				'post'             => 'posts',
				'page'             => 'pages',
				'custom_post_type' => 'movies'
			],
			[
				'post'             => get_post_type_object( 'post' )->rest_base,
				'page'             => get_post_type_object( 'page' )->rest_base,
				'custom_post_type' => get_post_type_object( 'movies' )->rest_base,
			]
		);

	}

	/**
	 * Test get_original_meta_key_name() returns the custom rest_base value from the wp_rest_api_controller_taxonomies_{$taxonomy_slug} option.
	 *
	 * @since NEXT
	 */
	function testGetOriginalMetaKeyNameCustomValue() {

		$this->update_taxonomies_option(
			[
				'slug'      => 'custom_taxonomy',
				'meta_data' => [
					'custom_meta' => [
						'original_meta_key' => 'custom_meta',
						'custom_key'        => 'custom-meta-key',
					]
				]
			]
		);

		$taxonomy_data = (array) $this->factory->term->create_and_get(
			array(
				'taxonomy' => 'custom_taxonomy',
				'name'     => 'Custom Taxonomy'
			)
		);

		$taxonomy_data['id'] = $taxonomy_data['term_id'];

		update_term_meta( $taxonomy_data['term_id'], 'custom_meta', 'Custom Meta Value' );

		$custom_meta_data = ( new WP_REST_API_Controller() )->get_original_meta_key_name( 'custom_taxonomy', 'custom-meta-key', true );

		$this->assertEquals( 'custom_meta', $custom_meta_data );

	}

	/**
	 * Test post type meta data is added to the API as intended.
	 *
	 * @since NEXT
	 */
	function testPostTypeMetaDataVisibleInAPI() {

		$this->update_post_types_option(
			array(
				'post_type' => 'custom_post_type',
				'meta_data' => [
					'custom_meta' => [
						'active'            => true,
						'original_meta_key' => 'custom_meta',
						'custom_key'        => '',
					]
				]
			)
		);

		update_post_meta( $this->post_id, 'custom_meta', 'Hello World!' );

		( new WP_REST_API_Controller() )->append_post_type_meta_data_to_api();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_post_type/' . $this->post_id );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals(
			[
				$response->get_status(),
				$data['custom_meta']
			],
			[
				200,
				'Hello World!'
			]
		);

	}

	/**
	 * Test post type meta data is not added to the API when it is disabled.
	 *
	 * @since NEXT
	 */
	function testPostTypeMetaDataNotVisibleInAPIWhenDisabled() {

		$this->update_post_types_option(
			array(
				'post_type' => 'custom_post_type',
				'meta_data' => [
					'test_meta' => [
						'active'            => false,
						'original_meta_key' => 'test_meta',
						'custom_key'        => '',
					]
				]
			)
		);

		flush_rewrite_rules();

		update_post_meta( $this->post_id, 'test_meta', 'Hello World!' );

		( new WP_REST_API_Controller() )->append_post_type_meta_data_to_api();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_post_type/' . $this->post_id );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals(
			[
				$response->get_status(),
				array_key_exists( 'test_meta', $data )
			],
			[
				200,
				false
			]
		);

	}

	/**
	 * Test taxonomy meta data is added to the API as intended.
	 *
	 * @since NEXT
	 */
	function testTaxonomyMetaDataVisibleInAPI() {

		$this->update_taxonomies_option(
			[
				'active'    => true,
				'slug'      => 'custom_taxonomy',
				'meta_data' => [
					'term_meta_taxonomy' => [
						'active'            => true,
						'original_meta_key' => 'term_meta_taxonomy',
						'custom_key'        => '',
					]
				]
			]
		);

		$taxonomy_data = (array) $this->factory->term->create_and_get(
			array(
				'taxonomy' => 'custom_taxonomy',
				'name'     => 'Custom Taxonomy Value!'
			)
		);

		update_term_meta( $taxonomy_data['term_id'], 'tax_position', '100' );

		( new WP_REST_API_Controller() )->append_taxonomy_meta_data_to_api();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_taxonomy' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$values = wp_list_pluck( $data, 'name' );

		$this->assertTrue( in_array( 'Custom Taxonomy Value!', $values, true ) );

	}

	/**
	 * Test taxonomy meta data is not added to the API when disabled.
	 *
	 * @since NEXT
	 */
	function testTaxonomyMetaDataNotVisibleInAPIWhenDisabled() {

		$this->update_taxonomies_option(
			[
				'active'    => true,
				'slug'      => 'custom_taxonomy',
				'meta_data' => [
					'term_meta_taxonomy' => [
						'active'            => false,
						'original_meta_key' => 'term_meta_taxonomy',
						'custom_key'        => '',
					]
				]
			]
		);

		$taxonomy_data = (array) $this->factory->term->create_and_get(
			array(
				'taxonomy' => 'custom_taxonomy',
				'name'     => 'Custom Taxonomy Value - New'
			)
		);

		update_term_meta( $taxonomy_data['term_id'], 'tax_position', '100' );

		( new WP_REST_API_Controller() )->append_taxonomy_meta_data_to_api();

		$this->setup_rest_api();

		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_taxonomy' );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$values = wp_list_pluck( $data, 'name' );

		$this->assertTrue( ! in_array( 'Custom Taxonomy Value!', $values, true ) );

	}

}
