<?php

class Test_Simple_Taxonomy_Ordering extends WP_UnitTestCase {

	public $page_id;

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

		wp_delete_post( $this->post_id );

	}

	private function setup_rest_api() {

		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;

		do_action( 'rest_api_init' );

	}

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

		$this->post_id = $this->factory->post->create(
			[
				'post_title'  => 'Custom Title',
				'post_type'   => 'custom_post_type',
				'post_status' => 'publish',
			]
		);

	}

	/**
	 * Test the core endpoint works as expected for default custom post type.
	 *
	 * @since NEXT
	 */
	function testCustomPostTypeEndpointCore() {

		$this->setup_rest_api();

		// Test default /wp/v2/custom_post_type/ core endpoint.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/custom_post_type/' . $this->post_id );
		$response = $this->server->dispatch( $request );
		$data     = $response->get_data();

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( 'Custom Title', $data['title']['rendered'] );

	}

	/**
	 * Test the constants are set.
	 *
	 * @since NEXT
	 */
	function testCustomPostTypeEndpointCustom() {

		$this->markTestSkipped( 'Test must be revisited after a refactor.' );

		update_option( 'wp_rest_api_controller_post_types', array( 'custom_post_type' ) );
		update_option(
			'wp_rest_api_controller_post_types_custom_post_type',
			[
				'active'    => 1,
				'rest_base' => 'test-custom_post_type',
				'meta_data' => [
					'_edit_lock' => [
						'original_meta_key' => '_edit_lock',
						'custom_key'        => '',
					],
				],
			]
		);

		$this->setup_rest_api();

		// @todo
		// Test custom /wp/v2/test-custom_post_type/ plugin endpoint.
		// $request  = new WP_REST_Request( 'GET', '/wp/v2/test-custom_post_type/' . $this->post_id );
		// $response = $this->server->dispatch( $request );
		// $data     = $response->get_data();
		//
		// $this->assertEquals( 200, $response->get_status() );
		// $this->assertEquals( 'Custom Title', $data['title']['rendered'] );

	}

}
