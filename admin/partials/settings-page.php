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
			'<a href="%1$s" class="button tipso tipso_style" data-tipso-title="%2$s" data-tipso="%3$s">%4$s</a>',
			esc_url( add_query_arg( '_wpnonce', wp_create_nonce( 'clear-api-cache' ), admin_url( 'tools.php?page=wp-rest-api-controller-settings' ) ) ),
			esc_attr__( 'Delete REST API Cache', 'wp-rest-api-controller' ),
			esc_attr__( 'Clear the WP REST API Cache stored in this plugin. If you recently registered a new post type, or assigned new meta data to a post - click this to update the lists above.', 'wp-rest-api-controller' ),
			esc_html__( 'Clear Cache', 'wp-rest-api-controller' )
		);

		echo wp_kses_post( '</div>' );

		?>
	</form>

</div>
