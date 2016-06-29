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

	public $plugin;

	private $enabled_post_types;

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

		$this->plugin_name = 'WP REST API Controller';
		$this->version = '1.0.0';
		$this->enabled_post_types = $this->get_stored_post_types();
		$this->plugin = $plugin;

		if ( $this->enabled_post_types && ! empty( $this->enabled_post_types ) ) {
			add_action( 'init', array( $this, 'expose_api_endpoints' ), 30 );
		}
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
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

		$plugin_admin = new wp_rest_api_controller_Admin( $this->get_plugin_name(), $this->get_version() );

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
		// if none have been saved, abort
		if ( ! $stored_post_types ) {
			return false;
		}
		$post_types_array = array();
		// Loop over and check and push to our array
		foreach ( $stored_post_types as $post_type_slug ) {
			if ( 0 !== absint( get_option( 'wp_rest_api_controller_post_types_' . $post_type_slug, 0 ) ) ) {
				$post_types_array[ $post_type_slug ] = 'enabled';
			} else {
				$post_types_array[ $post_type_slug ] = 'disabled';
			}
		}
		// return the post types array
		return $post_types_array;
	}

	/**
	 * Expose (or disable) post types to the REST API
	 *
	 * @return null Expose the enabled API endpoints
	 * @since 1.0.0
	 */
	public function expose_api_endpoints() {
		$enabled_post_types = $this->enabled_post_types;
		if ( $enabled_post_types && ! empty( $enabled_post_types ) ) {
			global $wp_post_types;
			foreach ( $enabled_post_types as $post_type_slug => $enabled ) {
				// Get the post type rest base to use for the API endpoint (eg: /v2/posts or /v2/pages)
				$rest_base = $this->get_post_type_rest_base( $post_type_slug );
				// Check the enabled state
				if ( 'enabled' === $enabled ) {
					$wp_post_types[ $post_type_slug ]->show_in_rest = true;
					$wp_post_types[ $post_type_slug ]->rest_base = $rest_base;
					$wp_post_types[ $post_type_slug ]->rest_controller_class = 'WP_REST_Posts_Controller';
				} else {
					$wp_post_types[ $post_type_slug ]->show_in_rest = false;
				}
			}
		}
	}

	/**
	 * Get the rest base for a given post type
	 * @param  string $post_type_slug Slug of the post type to return.
	 * @return string                 REST API base name.
	 */
	public function get_post_type_rest_base( $post_type_slug ) {
		// Re-set the default 'post'/'page' rest base values
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
		return apply_filters( 'wp_rest_api_controller_rest_base', $rest_base, $post_type_slugg );
	}
}
