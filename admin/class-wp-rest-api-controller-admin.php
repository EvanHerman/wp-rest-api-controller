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

if ( ! class_exists( 'WP_REST_API_Controller_Admin' ) ) {

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
		 * Class constructor.
		 */
		public function __construct() {

			// Include our settings.
			include WP_REST_API_CONTROLLER_PATH . 'admin/partials/settings-functions.php';

			// Register our Settings page nested inside of 'Tools'.
			add_action( 'admin_menu', array( $this, 'register_wp_rest_api_controller_submenu_page' ) );

			// Generate our admin notices.
			add_action( 'admin_notices', array( $this, 'wp_rest_api_controller_admin_notices' ) );

			add_action( 'removable_query_args', array( $this, 'remove_custom_query_args' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			$screen = get_current_screen();

			if ( is_null( $screen ) || ! isset( $screen->base ) || 'tools_page_wp-rest-api-controller-settings' !== $screen->base ) {

				return;

			}

			$min = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'tipso.css', WP_REST_API_CONTROLLER_URL . "admin/css/tipso{$min}.css", array(), '1.0.8', 'all' );
			wp_enqueue_style( 'wp-rest-api-controller-admin', WP_REST_API_CONTROLLER_URL . "admin/css/wp-rest-api-controller-admin{$min}.css", array( 'tipso.css' ), WP_REST_API_CONTROLLER_VERSION, 'all' );

			wp_enqueue_script( 'tipso.js', WP_REST_API_CONTROLLER_URL . "admin/js/tipso{$min}.js", array( 'jquery' ), '1.0.8', true );
			wp_enqueue_script( 'wp-rest-api-controller-admin', WP_REST_API_CONTROLLER_URL . "admin/js/wp-rest-api-controller-admin{$min}.js", array( 'tipso.js' ), WP_REST_API_CONTROLLER_VERSION, true );

			wp_localize_script(
				'wp-rest-api-controller-admin',
				'rest_api_controller_localized_admin_data',
				array(
					'disabled_notice' => __( 'This post type is disabled. Enable it and save the settings to access this link.', 'wp-rest-api-controller' ),
				)
			);

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

			if ( ! isset( $_GET['page'] ) ) {
				return;
			}

			$page = filter_var( $_GET['page'], FILTER_SANITIZE_STRING );

			if ( 'wp-rest-api-controller-settings' !== $page ) {
				return;
			}

			if ( isset( $_GET['settings-updated'] ) ) {
				$settings_updated = filter_var( $_GET['settings-updated'], FILTER_VALIDATE_BOOLEAN );

				if ( $settings_updated ) {
					$class   = 'notice notice-success';
					$message = __( 'Settings have been successfully updated.', 'wp-rest-api-controller' );
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $message ) );
				}
			}

			if ( ! isset( $_GET['settings-updated'] ) && isset( $_GET['wp-rest-api-cache-cleared'] ) ) {
				$settings_updated = filter_var( $_GET['wp-rest-api-cache-cleared'], FILTER_VALIDATE_BOOLEAN );

				if ( $settings_updated ) {
					$class   = 'notice notice-success';
					$message = __( 'The WP REST API Controller cache has been cleared, and the post type and meta data lists below have been updated.', 'wp-rest-api-controller' );
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $message ) );
				}
			}

		}

		/**
		 * Remove our wp-rest-api-cache-cleared query arg from the settings page.
		 *
		 * @param  array $removable_query_args Default query args that are removed.
		 *
		 * @return array Query args to be removed with our custom key.
		 */
		public function remove_custom_query_args( $removable_query_args ) {

			$removable_query_args[] = 'wp-rest-api-cache-cleared';

			return $removable_query_args;

		}

	}

}

new WP_REST_API_Controller_Admin();
