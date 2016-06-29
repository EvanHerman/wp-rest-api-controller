<?php
 // ------------------------------------------------------------------
 // Add all your sections, fields and settings during admin_init
 // ------------------------------------------------------------------
 //

class rest_api_exposed_Settings {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'rest_api_exposed_settings_api_init' ) );
	}

	public function rest_api_exposed_settings_api_init() {
		$post_types = $this->get_registered_post_types();

		// Add the section to reading settings so we can add our
		// fields to it
		add_settings_section(
			'rest_api_exposed_setting_section',
			__( 'Example settings section in reading', 'rest-api-exposed' ),
			array( $this, 'rest_api_exposed_setting_section_callback_function' ),
			'rest-api-exposed'
		);

		if ( ! empty( $post_types ) ) {
			$rest_api_exposed_post_types = array();
			foreach ( $post_types as $post_type_slug => $post_type_name ) {
				// Add the field with the names and function to use for our new
				// settings, put it in our new section
				add_settings_field(
					'rest_api_exposed_post_types_' . $post_type_slug,
					ucwords( $post_type_name ),
					array( $this, 'rest_api_exposed_setting_section_setting_callback_function' ),
					'rest-api-exposed',
					'rest_api_exposed_setting_section',
					array(
						'option_id' => 'rest_api_exposed_post_types_' . $post_type_slug,
						'post_type_name' => $post_type_name,
					)
				);
				// Register our setting so that $_POST handling is done for us and
				register_setting( 'rest-api-exposed', 'rest_api_exposed_post_types_' . $post_type_slug );
				$rest_api_exposed_post_types[] = $post_type_slug;
			}
			// store our options for later use
			update_option( 'rest_api_exposed_post_types', $rest_api_exposed_post_types );
		}
	} // eg_settings_api_init()

	public function get_registered_post_types() {
		$post_types = get_post_types();
		unset( $post_types['revision'], $post_types['nav_menu_item'] );
		return apply_filters( 'rest_api_exposed_post_types', $post_types );
	}

	// ------------------------------------------------------------------
	// Settings section callback function
	// ------------------------------------------------------------------
	//
	// This function is needed if we added a new section. This function
	// will be run at the start of our section
	//
	public function rest_api_exposed_setting_section_callback_function() {
		echo '<p>' . esc_attr__( 'Intro text for our settings section', 'rest-api-exposed' ) . '</p>';
	}

	// ------------------------------------------------------------------
	// Callback function for our example setting
	// ------------------------------------------------------------------
	//
	// creates a checkbox true/false option. Other types are surely possible
	//
	public function rest_api_exposed_setting_section_setting_callback_function( $args ) {
		echo '<input name="' . $args['option_id'] . '" id="' . $args['option_id'] . '" type="checkbox" value="1" class="code" ' . checked( 1, get_option( $args['option_id'] ), false ) . ' />';
		echo '<p class="description">' . sprintf( __( 'Expose the %s post type to the REST API.', 'rest-api-exposed' ), '<code>' . $args['post_type_name'] . '</code>' ) . '</p>';
	}
}
$settings = new rest_api_exposed_Settings();
