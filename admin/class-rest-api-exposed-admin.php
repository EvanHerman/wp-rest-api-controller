<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.yikesinc.com
 * @since      1.0.0
 *
 * @package    rest_api_exposed
 * @subpackage rest_api_exposed/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    rest_api_exposed
 * @subpackage rest_api_exposed/admin
 * @author     YIKES, Inc., Evan Herman
 */
class rest_api_exposed_Admin {

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
		add_action( 'admin_menu', array( $this, 'register_rest_api_exposed_submenu_page' ) );
		// Include our settings
		include( REST_API_EXPOSED_PATH . 'admin/partials/settings-functions.php' );
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
		 * defined in rest_api_exposed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The rest_api_exposed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/rest-api-exposed-admin.min.css', array(), $this->version, 'all' );
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
		 * defined in rest_api_exposed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The rest_api_exposed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/min/rest-api-exposed-admin.min.js', array( 'jquery' ), $this->version, false );
	}

	// Register the menu
	public function register_rest_api_exposed_submenu_page() {
		add_submenu_page(
			'tools.php',
			__( 'REST API Exposed', 'rest-api-exposed' ),
			__( 'REST API Exposed', 'rest-api-exposed' ),
			'manage_options',
			'rest-api-exposed-settings',
			array( $this, 'rest_api_exposed_submenu_page_callback' )
		);
	}

	// Generate the Settings Page
	public function rest_api_exposed_submenu_page_callback() {
		ob_start();
		include( REST_API_EXPOSED_PATH . 'admin/partials/settings-page.php' );
		$content = ob_get_contents();
		ob_get_clean();
		echo $content;
	}
}
