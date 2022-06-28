<?php
/**
 * Settings Page Markup
 *
 * @package WP REST API Controller
 */

?>

<div class="wrap">

	<h1><?php esc_html_e( 'WP REST API Controller Settings', 'wp-rest-api-controller' ); ?></h1>

	<form method="POST" action="options.php">
		<?php

		settings_fields( 'wp-rest-api-controller' );

		do_settings_sections( 'wp-rest-api-controller' );

		wp_nonce_field( 'clear_wp_rest_api_controller_cache', 'clear_wp_rest_api_controller_cache', false );

		echo wp_kses_post( '<div class="submit-buttons">' );

		submit_button( __( 'Save Settings', 'wp-rest-api-controller' ), 'primary', 'save-wp-rest-api-controller-settings', false );

		submit_button(
			__( 'Clear Cache', 'wp-rest-api-controller' ),
			'secondary',
			'clear-wp-rest-api-controller-cache',
			false,
			array(
				'class'            => 'button tipso',
				'data-tipso-title' => esc_attr__( 'Delete REST API Cache', 'wp-rest-api-controller' ),
				'data-tipso'       => esc_attr__( 'Clear the WP REST API Cache stored in this plugin. If you recently registered a new post type, or assigned new meta data to a post - click this to update the lists above.', 'wp-rest-api-controller' ),
			)
		);

		echo wp_kses_post( '</div>' );

		?>
	</form>

</div>
