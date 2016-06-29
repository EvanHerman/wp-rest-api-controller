<?php
/**
 * 		Plugin Name:       REST API Exposed
 * 		Plugin URI:        https://www.yikesplugins.com
 * 		Description:       This plugin enables a UI to enable endpoints in the REST API.
 * 		Version:           1.0.0
 * 		Author:            YIKES, Inc., Evan Herman
 * 		Author URI:        https://www.yikesinc.com
 * 		License:           GPL-3.0+
 *		License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * 		Text Domain:       rest-api-exposed
 * 		Domain Path:       /languages
 *
 * 		REST API Exposed by YIKES, Inc., Evan Herman, Evan Herman is free software: you can redistribute it and/or modify
 * 		it under the terms of the GNU General Public License as published by
 * 		the Free Software Foundation, either version 2 of the License, or
 * 		any later version.
 *
 * 		REST API Exposed by YIKES, Inc., Evan Herman, Evan Herman is distributed in the hope that it will be useful,
 * 		but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * 		GNU General Public License for more details.
 *
 * 		You should have received a copy of the GNU General Public License
 *		along with REST API Exposed by YIKES, Inc., Evan Herman, Evan Herman If not, see <http://www.gnu.org/licenses/>.
 *
**/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * 	Define path constant to our plugin directory.
 *
 * 	@since 6.0.0
 *	@return void
 */
if ( ! defined( 'REST_API_EXPOSED_PATH' ) ) {
	define( 'REST_API_EXPOSED_PATH' , plugin_dir_path( __FILE__ ) );
}
/**
 * 	Define URL constant to our plugin directory.
 *
 * 	@since 6.0.0
 *	@return void
 */
if ( ! defined( 'REST_API_EXPOSED_URL' ) ) {
	define( 'REST_API_EXPOSED_URL' , plugin_dir_url( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rest-api-exposed-activator.php
 */
register_activation_hook( __FILE__, 'activate_rest_api_exposed' );
function activate_rest_api_exposed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rest-api-exposed-activator.php';
	rest_api_exposed_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rest-api-exposed-deactivator.php
 */
register_deactivation_hook( __FILE__, 'deactivate_rest_api_exposed' );
function deactivate_rest_api_exposed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rest-api-exposed-deactivator.php';
	rest_api_exposed_Deactivator::deactivate();
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rest-api-exposed.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rest_api_exposed() {

	$plugin = new rest_api_exposed();
	$plugin->run();

}
run_rest_api_exposed();
