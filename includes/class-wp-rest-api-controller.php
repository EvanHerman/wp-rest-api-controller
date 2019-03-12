<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.yikesinc.com
 * @since      1.0.0
 *
 * @package    wp_rest_api_controller
 * @subpackage wp_rest_api_controller/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    wp_rest_api_controller
 * @subpackage wp_rest_api_controller/includes
 * @author     YIKES, Inc., Evan Herman
 */
class wp_rest_api_controller {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      wp_rest_api_controller_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Enabled post types.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 */
	private $enabled_post_types;

	/**
	 * Enabled taxonomies.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    array
	 */
	private $enabled_taxonomies;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name        = 'WP REST API Controller';
		$this->version            = '2.0.0';
		$this->enabled_post_types = $this->get_stored_post_types();
		$this->enabled_taxonomies = $this->get_stored_taxonomies();

		if ( ! empty( $this->enabled_post_types ) || ! empty( $this->enabled_taxonomies ) ) {
			add_action( 'init', array( $this, 'expose_api_endpoints' ), 100 );
			add_action( 'rest_api_init', array( $this, 'append_meta_data_to_api_request' ) );
		}

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->run_one_point_four_update_check();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - wp_rest_api_controller_Loader. Orchestrates the hooks of the plugin.
	 * - wp_rest_api_controller_i18n. Defines internationalization functionality.
	 * - wp_rest_api_controller_Admin. Defines all hooks for the admin area.
	 * - wp_rest_api_controller_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-rest-api-controller-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-rest-api-controller-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-rest-api-controller-admin.php';

		$this->loader = new wp_rest_api_controller_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the wp_rest_api_controller_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new wp_rest_api_controller_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new WP_REST_API_Controller_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * As of 1.4.0, we've made some core changes to the plugin, so run the changes
	 *
	 * @since    1.4.0
	 */
	public function run_one_point_four_update_check() {

		// Check if we've done this before.
		if ( get_option( 'wp_rest_api_controller_one_point_four', false ) === false ) {

			// We no longer support enabling/disabling post/page endpoints, so remove these options.
			delete_option( 'wp_rest_api_controller_post_types_post' );
			delete_option( 'wp_rest_api_controller_post_types_page' );

			// Add a flag so we don't do this all the time.
			add_option( 'wp_rest_api_controller_one_point_four', true );
		}
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    wp_rest_api_controller_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get the stored post types to expose/disable to the REST API
	 *
	 * @return array Array of post type slugs to expose to our API
	 * @since 1.0.0
	 */
	public function get_stored_post_types() {
		$stored_post_types = get_option( 'wp_rest_api_controller_post_types', false );

		if ( ! $stored_post_types ) {
			return;
		}

		$post_types_array = array();

		foreach ( $stored_post_types as $post_type_slug ) {

			$post_type_options = get_option( "wp_rest_api_controller_post_types_{$post_type_slug}", 'unset' );

			// This means nothing has been set. If the options have never been saved, don't turn things on and off.
			if ( $post_type_options === 'unset' ) {
				continue;
			}

			if ( $post_type_options && ( isset( $post_type_options['active'] ) && $post_type_options['active'] ) ) {

				$post_types_array[ $post_type_slug ] = 'enabled';
				continue;
			}

			$post_types_array[ $post_type_slug ] = 'disabled';

		}

		return $post_types_array;
	}

	/**
	 * Get the stored taxonomies to expose/disable to the REST API
	 *
	 * @return array Array of post type slugs to expose to our API
	 * @since 1.0.0
	 */
	public function get_stored_taxonomies() {
		$taxonomies = get_option( 'wp_rest_api_controller_taxonomies', false );

		if ( ! $taxonomies ) {
			return;
		}

		$taxonomies_array = array();

		foreach ( $taxonomies as $tax_slug ) {

			$tax_options = get_option( "wp_rest_api_controller_taxonomies_{$tax_slug}", 'unset' );

			// This means nothing has been set. If the options have never been saved, don't turn things on and off.
			if ( $tax_options === 'unset' ) {
				continue;
			}

			$taxonomies_array[ $tax_slug ] = array(
				'enabled'   => ! empty( $tax_options['active'] ) && $tax_options['active'],
				'rest_base' => ! empty( $tax_options['rest_base'] ) ? $tax_options['rest_base'] : $tax_slug,
				'meta_data' => ! empty( $tax_options['meta_data'] ) ? $tax_options['meta_data'] : array(),
			);
		}

		return $taxonomies_array;
	}

	/**
	 * Based on the value of a custom meta key in the WP REST API array
	 * we return the original key, so that get_post_meta() can be used properly
	 *
	 * @param  string $custom_meta_key_name The custom meta key defined in the options.
	 * @return string                       The original meta key to use in get_post_meta() function
	 */
	public function get_original_meta_key_name( $object_slug, $custom_meta_key_name, $is_tax = false ) {

		$option_name  = $is_tax ? "wp_rest_api_controller_taxonomies_{$object_slug}" : "wp_rest_api_controller_post_types_{$object_slug}";
		$meta_options = get_option( $option_name, array(
			'active'    => 0,
			'meta_data' => array(),
		) );

		if ( empty( $meta_options['meta_data'] ) ) {
			return;
		}

		foreach ( $meta_options['meta_data'] as $key ) {
			if ( strtolower( $custom_meta_key_name ) === strtolower( $key['custom_key'] ) ) {
				return $key['original_meta_key'];
			}
		}

		// If we can't find this key, it's already the original meta key name, so return it.
		return $custom_meta_key_name;
	}

	/**
	 * Expose (or disable) post types/taxonomies to the REST API.
	 *
	 * @since 1.0.0
	 */
	public function expose_api_endpoints() {

		if ( ! empty( $this->enabled_post_types ) ) {
			$this->expose_post_type_api_endpoints();
		}

		if ( ! empty( $this->enabled_taxonomies ) ) {
			$this->expose_taxonomy_api_endpoints();
		}
	}

	/**
	 * Expose (or disable) post types to the REST API.
	 *
	 * @since 1.0.0
	 */
	private function expose_post_type_api_endpoints() {
		global $wp_post_types;

		foreach ( $this->enabled_post_types as $post_type_slug => $enabled ) {

			if ( ! isset( $wp_post_types[ $post_type_slug ] ) || ! is_object( $wp_post_types[ $post_type_slug ] ) ) {
				continue;
			}

			$rest_base = $this->get_post_type_rest_base( $post_type_slug );

			if ( 'enabled' !== $enabled ) {
				$wp_post_types[ $post_type_slug ]->show_in_rest = false;
				continue;
			}

			$wp_post_types[ $post_type_slug ]->show_in_rest = true;
			$wp_post_types[ $post_type_slug ]->rest_base    = $rest_base;
		}
	}

	/**
	 * Expose (or disable) taxonomies to the REST API
	 *
	 * @since 1.0.0
	 */
	private function expose_taxonomy_api_endpoints() {
		global $taxonomies;

		foreach ( $this->enabled_taxonomies as $tax_slug => $tax_params ) {

			$taxonomy = get_taxonomy( $tax_slug );

			if ( empty( $taxonomy ) || ! is_object( $taxonomy ) ) {
				continue;
			}

			if ( ! $tax_params['enabled'] ) {
				$taxonomy->show_in_rest = false;
			} else {
				$taxonomy->show_in_rest = true;
				$taxonomy->rest_base    = $tax_params['rest_base'];
			}
		}
	}

	/**
	 * Append post type/taxonomy meta data to the API request.
	 *
	 * @since 1.0.0
	 */
	public function append_meta_data_to_api_request() {

		if ( ! empty( $this->enabled_post_types ) ) {
			$this->append_post_type_meta_data_to_api();
		}

		if ( ! empty( $this->enabled_taxonomies ) ) {
			$this->append_taxonomy_meta_data_to_api();
		}
	}

	/**
	 * Append post type meta data to the API request
	 *
	 * All requests append data using get_post_meta() inside custom_meta_data_callback()
	 * Users can override the value and provide a custom value using our filter `wp_rest_api_controller_api_property_value`
	 * For help, see the 'Other Notes' section in the WordPress.org repository for this plugin
	 *
	 * @since 1.0.0
	 */
	private function append_post_type_meta_data_to_api() {
		foreach ( $this->enabled_post_types as $post_type_slug => $enabled ) {

			if ( 'enabled' !== $enabled ) {
				continue;
			}

			$post_type_options = get_option( "wp_rest_api_controller_post_types_{$post_type_slug}", array(
				'active'    => 0,
				'meta_data' => array(),
			) );

			if ( ! isset( $post_type_options['meta_data'] ) || empty( $post_type_options['meta_data'] ) ) {
				continue;
			}

			foreach ( $post_type_options['meta_data'] as $meta_key => $meta_data ) {

				if ( ! isset( $meta_data['active'] ) || ( isset( $meta_data['active'] ) && 1 !== absint( $meta_data['active'] ) ) ) {
					continue;
				}

				$rest_api_meta_name = ( isset( $meta_data['custom_key'] ) && ! empty( $meta_data['custom_key'] ) ) ? $meta_data['custom_key'] : $meta_key;

				register_rest_field(
					$post_type_slug,
					$rest_api_meta_name,
					array(
						'get_callback'    => array( $this, 'custom_meta_data_callback' ),
						'update_callback' => null,
						'schema'          => null,
					)
				);
			}
		}
	}

	private function append_taxonomy_meta_data_to_api() {
		foreach ( $this->enabled_taxonomies as $tax_slug => $tax_params ) {

			if ( ! $tax_params['enabled'] || empty( $tax_params['meta_data'] ) ) {
				continue;
			}

			foreach ( $tax_params['meta_data'] as $meta_key => $meta_data ) {

				if ( empty( $meta_data['active'] ) ) {
					continue;
				}

				register_rest_field(
					$tax_slug,
					! empty( $meta_data['custom_key'] ) ? $meta_data['custom_key'] : $meta_key,
					array(
						'get_callback'    => array( $this, 'custom_meta_data_callback' ),
						'update_callback' => null,
						'schema'          => null,
					)
				);
			}
		}
	}

	/**
	 * Callback function to append our metadata value to the field
	 *
	 * @param  array   $object      Post object
	 * @param  string  $field_name  Field name.
	 * @param  array   $request     API request.
	 *
	 * @return string               The original meta key name to use in get_post_meta();
	 */
	function custom_meta_data_callback( $object, $field_name, $request ) {

		$is_tax      = isset( $object['taxonomy'] );
		$object_type = $is_tax ? $object['taxonomy'] : $object['type'];

		$original_meta_key_name = $this->get_original_meta_key_name( $object_type, $field_name, $is_tax );
		
		// If we can't find the original meta key name, then return. 
		// We do not want our get_post_meta() call to look like get_post_meta( $id, NULL, true ) or all meta fields will be returned
		if ( empty( $original_meta_key_name ) ) {
			return;
		}

		/**
		 * Toggle the get_post_meta's $single argument.
		 *
		 * For meta data stored with repeating keys (i.e. multiple DB entries for one meta_key value), you should set this value to false.
		 * If you do not set this to false, you will only retrieve the first value found in the DB, rather than an array of all the values.
		 *
		 * @param bool   $retrieve_single        The default is true - return single.
		 * @param string $field_name             The renamed meta key - allows users to filter this called based on the renamed meta key name.
		 * @param string $original_meta_key_name The meta key - allows users to filter this call based on the original meta key name.
		 *
		 * @return bool  True to return single, False to return all.
		 */
		$retrieve_post_meta_single = apply_filters( 'wp_rest_api_controller_retrieve_meta_single', true, $field_name, $original_meta_key_name );
		$meta_value                = $is_tax ? get_term_meta( $object['id'], $original_meta_key_name, $retrieve_post_meta_single ) : get_post_meta( $object['id'], $original_meta_key_name, $retrieve_post_meta_single );

		return apply_filters( 'wp_rest_api_controller_api_property_value', $meta_value, $object['id'], $original_meta_key_name, $is_tax );
	}

	/**
	 * Get the rest base for a given post type
	 *
	 * @param  string $post_type_slug Slug of the post type to return.
	 *
	 * @return string                 REST API base name.
	 */
	public static function get_post_type_rest_base( $post_type_slug ) {

		$post_type_options = $options = get_option( "wp_rest_api_controller_post_types_{$post_type_slug}", array(
			'active' => 0,
			'meta_data' => array(),
		) );

		switch ( $post_type_slug ) {

			case 'post':
				$rest_base = 'posts';
				break;

			case 'page':
				$rest_base = 'pages';
				break;

			default:
				$rest_base = $post_type_slug;
				break;

		}

		if ( isset( $post_type_options['rest_base'] ) && ! empty( $post_type_options['rest_base'] ) ) {

			$rest_base = $post_type_options['rest_base'];

		}

		return apply_filters( 'wp_rest_api_controller_rest_base', $rest_base, $post_type_slug, 0 );
	}

}
