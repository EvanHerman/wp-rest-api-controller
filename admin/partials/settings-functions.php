<?php
// ------------------------------------------------------------------
// Add all your sections, fields and settings during admin_init
// ------------------------------------------------------------------
//
class wp_rest_api_controller_Settings {

	// Store our REST API Endpoint base
	public $rest_endpoint_base;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'wp_rest_api_controller_settings_api_init' ) );
	}

	public function wp_rest_api_controller_settings_api_init() {
		$post_types = $this->get_registered_post_types();
		$this->rest_endpoint_base = esc_url( site_url( '/wp-json/wp/v2/' ) );

		// Add the section to reading settings so we can add our
		// fields to it
		add_settings_section(
			'wp_rest_api_controller_setting_section',
			null,
			array( $this, 'wp_rest_api_controller_setting_section_callback_function' ),
			'wp-rest-api-controller'
		);

		if ( ! empty( $post_types ) ) {
			$wp_rest_api_controller_post_types = array();
			foreach ( $post_types as $post_type_slug => $post_type_name ) {
				// Add the field with the names and function to use for our new
				// settings, put it in our new section
				add_settings_field(
					'wp_rest_api_controller_post_types_' . $post_type_slug,
					ucwords( $post_type_name ),
					array( $this, 'wp_rest_api_controller_setting_section_setting_callback_function' ),
					'wp-rest-api-controller',
					'wp_rest_api_controller_setting_section',
					array(
						'option_id' => 'wp_rest_api_controller_post_types_' . $post_type_slug,
						'post_type_name' => $post_type_name,
						'post_type_slug' => $post_type_slug,
					)
				);
				// Register our setting so that $_POST handling is done for us and
				register_setting( 'wp-rest-api-controller', 'wp_rest_api_controller_post_types_' . $post_type_slug );
				$wp_rest_api_controller_post_types[] = $post_type_slug;
			}
			// store our options for later use
			update_option( 'wp_rest_api_controller_post_types', $wp_rest_api_controller_post_types );
		}
	} // eg_settings_api_init()

	public function get_registered_post_types() {
		$post_types = get_post_types();
		unset( $post_types['revision'], $post_types['nav_menu_item'] );
		return apply_filters( 'wp_rest_api_controller_post_types', $post_types );
	}

	// ------------------------------------------------------------------
	// Settings section callback function
	// ------------------------------------------------------------------
	//
	// This function is needed if we added a new section. This function
	// will be run at the start of our section
	//
	public function wp_rest_api_controller_setting_section_callback_function() {
		echo '<p>' . esc_attr__( 'Toggle visibility of post types and select meta data to the REST API.', 'wp-rest-api-controller' ) . '</p>';
	}

	// ------------------------------------------------------------------
	// Callback function for our example setting
	// ------------------------------------------------------------------
	//
	// creates a checkbox true/false option. Other types are surely possible
	//
	public function wp_rest_api_controller_setting_section_setting_callback_function( $args ) {
		$post_type_object = get_post_type_object( $args['post_type_slug'] );
		$rest_base = wp_rest_api_controller::get_post_type_rest_base( $args['post_type_slug'] );
		if ( isset( $post_type_object ) && ! empty( $post_type_object ) ) {
			$singular_name = ( isset( $post_type_object->labels->singular_name ) ) ? $post_type_object->labels->singular_name : $args['post_type_name'];
		} else {
			$singular_name = $args['post_type_name'];
		}
		$disabled_attr = ( 1 === absint( get_option( $args['option_id'], false ) ) ) ? '' : 'disabled=disabled';
		$post_type_meta = $this->retreive_post_type_meta_keys( $args['post_type_slug'] );
		?>
		<!-- Display the checkboxes/descriptions -->
		<label class="switch switch-green">
			<input name="<?php echo esc_attr( $args['option_id'] ); ?>" type="checkbox" class="switch-input" onchange="toggleEndpointLink(this);" value="1" <?php checked( 1, get_option( $args['option_id'], false ) );?>>
			<span class="switch-label" data-on="<?php esc_attr_e( 'Enabled', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Disabled', 'wp-rest-api-controller' ); ?>"></span>
			<span class="switch-handle"></span>
		</label>
		<!-- Only if post type meta is assigned here -->
		<?php if ( $post_type_meta && ! empty( $post_type_meta ) ) { ?>
			<section class="post-type-meta-data">
				<table class="widefat fixed rest-api-controller-meta-data-table" cellspacing="0">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column" scope="col">&nbsp;</th>
							<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_attr_e( 'Meta Key', 'wp-rest-api-controller' ); ?></th>
							<th id="columnname" class="manage-column column-columnname" scope="col"><?php esc_attr_e( 'Custom Meta Key', 'wp-rest-api-controller' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$x = 1;
							foreach( $post_type_meta as $meta_key ) {
								?>
									<tr class="<?php echo ( $x % 2 == 0 ) ? '' : 'alternate'; ?>">
										<th class="check-column" scope="row"><input type="checkbox" value="1"></th>
										<td><?php echo esc_attr( $meta_key ); ?></td>
										<td><input type="text" value="" placeholder="<?php echo esc_attr( $meta_key ); ?>"></td>
									</tr>
								<?php
								$x++;
							}
						?>
					</tbody>
				</table>
			</section>
		<?php } ?>
		<!-- Description -->
		<p class="description"><?php printf( esc_attr__( 'Expose the %s post type to the REST API.', 'wp-rest-api-controller' ), '<code>' . esc_attr( $singular_name ) . '</code>' ); ?></p>
		<!-- API Endpoint Example -->
		<p class="description"><small>
			<?php printf( esc_attr__( '%s', 'wp-rest-api-controller' ), '<a class="endpoint-link" ' . esc_attr( $disabled_attr ) . ' href="' . esc_url( $this->rest_endpoint_base . $rest_base ) . '" target="_blank">' . esc_attr__( 'View Endpoint', 'wp-rest-api-controller' ) . '</code>' ); ?>
		</small></p>
		<?php
	}

	/**
	 * Retreive the meta data assigned to a post, and cache it in a transient
	 * Note: This only retreives meta that has already been stored. If the meta has been
	 * registered, but no post has any meta assigned to it - it will not display.
	 *
	 * @param  string $post_type The post type name to retreive meta data for.
	 * @return array 						 The array of meta data for the given post type.
	 */
	public function retreive_post_type_meta_keys( $post_type ) {
		// if transient is already set, abort
		if ( get_transient( $post_type . '_meta_keys' ) ) {
			return get_transient( $post_type . '_meta_keys' );
		}
		global $wpdb;
		$query = "
			SELECT DISTINCT($wpdb->postmeta.meta_key)
			FROM $wpdb->posts
			LEFT JOIN $wpdb->postmeta
			ON $wpdb->posts.ID = $wpdb->postmeta.post_id
			WHERE $wpdb->posts.post_type = '%s'
			AND $wpdb->postmeta.meta_key != ''
			AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
			AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
		";
		$meta_keys = $wpdb->get_col( $wpdb->prepare( $query, $post_type ) );
		set_transient( $post_type . '_meta_keys', $meta_keys, 60*60*24 ); # create 1 Day Expiration
		return $meta_keys;
	}
}
$settings = new wp_rest_api_controller_Settings();