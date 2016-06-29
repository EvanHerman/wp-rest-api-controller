<h2>
	<?php esc_attr_e( 'WP REST API Controller Settings', 'wp-rest-api-controller' ); ?>
</h2>

<form method="POST" action="options.php">
	<?php
		settings_fields( 'wp-rest-api-controller' );	//pass slug name of page, also referred to in Settings API as option group name
		do_settings_sections( 'wp-rest-api-controller' ); 	//pass slug name of page
		submit_button();
	?>
</form>
