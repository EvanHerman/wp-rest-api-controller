<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.evan-herman.com
 * @since      1.0.0
 *
 * @package    WP_REST_API_Controller
 * @subpackage WP_REST_API_Controller/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_REST_API_Controller
 * @subpackage WP_REST_API_Controller/admin
 * @author     YIKES, Inc., Evan Herman
 */
class WP_REST_API_Controller_Admin {

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
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Register our Settings page nested inside of 'Tools'.
		add_action( 'admin_menu', array( $this, 'register_wp_rest_api_controller_submenu_page' ) );

		// Include our settings.
		include WP_REST_API_CONTROLLER_PATH . 'admin/partials/settings-functions.php';

		// Generate our admin notices.
		add_action( 'admin_notices', array( $this, 'wp_rest_api_controller_admin_notices' ) );

		add_action( 'removable_query_args', array( $this, 'remove_custom_query_args' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/min/wp-rest-api-controller-admin.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();
		$base   = isset( $screen->base ) ? $screen->base : false;
		if ( $base && 'tools_page_wp-rest-api-controller-settings' === $base ) {
			wp_enqueue_script( 'tipso.js', plugin_dir_url( __FILE__ ) . 'js/min/tipso.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/min/wp-rest-api-controller-admin.min.js', array( 'jquery' ), $this->version, true );
			wp_localize_script(
				$this->plugin_name,
				'rest_api_controller_localized_admin_data',
				array(
					'disabled_notice' => __( 'This post type is disabled. Enable it and save the settings to access this link.', 'wp-rest-api-controller' ),
				)
			);
		}
	}

	/**
	 * Register our REST API Controller settings page
	 *
	 * @since 1.0.0
	 */
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

	/**
	 * Generate the REST API Controller settings page
	 *
	 * @since 1.0.0
	 */
	public function wp_rest_api_controller_submenu_page_callback() {
		ob_start();
		include WP_REST_API_CONTROLLER_PATH . 'admin/partials/settings-page.php';
		$content = ob_get_contents();
		ob_get_clean();
		echo $content; // phpcs:ignore
	}

	/**
	 * Generate admin notices to provide better feedback to the uesr
	 *
	 * @since 1.1.0
	 */
	public function wp_rest_api_controller_admin_notices() {
		$settings_updated = filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN );
		$flush_api_cache  = filter_input( INPUT_GET, 'api-cache-cleared', FILTER_VALIDATE_BOOLEAN );

		if ( $settings_updated ) {
			$class   = 'notice notice-success';
			$message = __( 'Settings have been successfully updated.', 'wp-rest-api-controller' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $message ) );
		}

		if ( $flush_api_cache ) {
			$class   = 'notice notice-success';
			$message = __( 'The WP REST API Controller cache has been cleared, and the post type and meta data lists below have been updated.', 'wp-rest-api-controller' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $message ) );
		}
	}

	/**
	 * Remove our api-cache-cleared query arg from the settings page.
	 *
	 * @param  array $removable_query_args Default query args that are removed.
	 *
	 * @return array Query args to be removed with our custom key.
	 */
	public function remove_custom_query_args( $removable_query_args ) {

		$removable_query_args[] = 'api-cache-cleared';

		return $removable_query_args;

	}
}
