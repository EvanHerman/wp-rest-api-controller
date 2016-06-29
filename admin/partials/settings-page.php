<h2>
	<?php esc_attr_e( 'REST API Exposed Settings', 'rest-api-exposed' ); ?>
</h2>

<form method="POST" action="options.php">
	<?php
		settings_fields( 'rest-api-exposed' );	//pass slug name of page, also referred to in Settings API as option group name
		do_settings_sections( 'rest-api-exposed' ); 	//pass slug name of page
		submit_button();
	?>
</form>
