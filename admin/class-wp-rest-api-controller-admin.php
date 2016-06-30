<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.yikesinc.com
 * @since      1.0.0
 *
 * @package    wp_rest_api_controller
 * @subpackage wp_rest_api_controller/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wp_rest_api_controller
 * @subpackage wp_rest_api_controller/admin
 * @author     YIKES, Inc., Evan Herman
 */
class wp_rest_api_controller_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// Register our Settings page nested inside of 'Tools'
		add_action( 'admin_menu', array( $this, 'register_wp_rest_api_controller_submenu_page' ) );
		// Include our settings
		include( WP_REST_API_CONTROLLER_PATH . 'admin/partials/settings-functions.php' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wp_rest_api_controller_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wp_rest_api_controller_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/wp-rest-api-controller-admin.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in wp_rest_api_controller_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The wp_rest_api_controller_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/min/wp-rest-api-controller-admin.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'rest_api_controller_localized_admin_data', array(
			'disabled_notice' => __( 'This post type is disabled. Enable it and save the settings, to access this link.', 'wp-rest-api-controller' ),
		) );

		$screen = get_current_screen();
		$base = ( isset( $screen->base ) ) ? $screen->base : false;
		if ( $base && 'tools_page_wp-rest-api-controller-settings' === $base ) {
			wp_enqueue_script( 'tipso.js', plugin_dir_url( __FILE__ ) . 'js/min/tipso.min.js', array( 'jquery' ), $this->version, false );
		}
	}

	// Register the menu
	public function register_wp_rest_api_controller_submenu_page() {
		add_submenu_page(
			'tools.php',
			__( 'WP REST API Controller', 'wp-rest-api-controller' ),
			__( 'REST API Controller', 'wp-rest-api-controller' ),
			'manage_options',
			'wp-rest-api-controller-settings',
			array( $this, 'wp_rest_api_controller_submenu_page_callback' )
		);
	}

	// Generate the Settings Page
	public function wp_rest_api_controller_submenu_page_callback() {
		ob_start();
		include( WP_REST_API_CONTROLLER_PATH . 'admin/partials/settings-page.php' );
		$content = ob_get_contents();
		ob_get_clean();
		echo $content;
	}
}
