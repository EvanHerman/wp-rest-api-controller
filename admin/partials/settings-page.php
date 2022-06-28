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

		echo wp_kses_post( '<div class="submit-buttons">' );

		submit_button( __( 'Save Settings', 'wp-rest-api-controller' ), 'primary', 'save-wp-rest-api-controller-settings', false );

		printf(
			'<a href="%1$s" class="button tipso tipso_style" data-tipso-title="Delete REST API Cache" data-tipso="Clear the WP REST API Cache stored in this plugin. If you recently registered a new post type, or assigned new meta data to a post - click this to update the lists above.">%2$s</a>',
			add_query_arg( 'api-cache-cleared', true, admin_url( 'tools.php?page=wp-rest-api-controller-settings' ) ),
			esc_html__( 'Clear Cache', 'wp-rest-api-controller' )
		);

		echo wp_kses_post( '</div>' );

		?>
	</form>

</div>
