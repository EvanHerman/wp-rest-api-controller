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
		$options = get_option( $args['option_id'], array(
			'active' => 0,
			'meta_data' => array(),
		) );
		$active_state = ( isset( $options['active'] ) && 1 === absint( $options['active'] ) ) ? true : false;
		$disabled_attr = ( $active_state ) ? '' : 'disabled=disabled';
		$post_type_meta = $this->retreive_post_type_meta_keys( $args['post_type_slug'] );
		$post_type_taxonomies = $this->retreive_post_type_taxonomies( $args['post_type_slug'] );
		?>
		<!-- Display the checkboxes/descriptions -->
		<label class="switch switch-green">
			<input name="<?php echo esc_attr( $args['option_id'] ); ?>[active]" type="checkbox" class="switch-input" onchange="toggleEndpointLink(this);" value="1" <?php checked( 1, $active_state );?>>
			<span class="switch-label" data-on="<?php esc_attr_e( 'Enabled', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Disabled', 'wp-rest-api-controller' ); ?>"></span>
			<span class="switch-handle"></span>
		</label>

		<section class="rest-api-endpoint-container<?php if ( ! $active_state ) { echo ' hidden-container'; } ?>">
			<!-- API Endpoint Example -->
			<p class="description">
				<small class="edit-post-type-rest-base-disabled">
					<?php printf( esc_attr( '%s', 'wp-rest-api-controller' ), '<span class="top-right tipso edit-rest-permalink-icon" data-tipso-title="' . esc_attr__( 'Meta Key', 'wp-rest-api-controller' ) . '" data-tipso="' . sprintf( esc_attr__( 'Access the %s post type via the REST API at the following URL.', 'wp-rest-api-controller' ), esc_attr( $singular_name ) ) . '"><span class="dashicons dashicons-editor-help"></span></span><a class="endpoint-link" ' . esc_attr( $disabled_attr ) . ' href="' . esc_url( $this->rest_endpoint_base . $rest_base ) . '" target="_blank">' . esc_url( $this->rest_endpoint_base . $rest_base ) ); ?></a>
					<a href="#" onclick="toggleRestBaseVisbility(this,event);" class="button-secondary edit-endpoint edit-endpoint-secondary-btn" class=""><?php esc_attr_e( 'Edit Endpoint', 'wp-rest-api-controller' ); ?></a>
				</small>
				<small class="edit-post-type-rest-base-active" style="display:none;">
					<?php echo esc_url( $this->rest_endpoint_base ); ?>
					<input type="text" onchange="toggleRestBaseInput(this);" data-rest-base="<?php echo esc_url( $this->rest_endpoint_base ); ?>" name="<?php echo esc_attr( $args['option_id'] ); ?>[rest_base]" value="<?php echo esc_attr( $rest_base ); ?>">
					<a href="#" onclick="toggleRestBaseVisbility(this,event);" class="button-secondary save-endpoint edit-endpoint-secondary-btn" class=""><?php esc_attr_e( 'Save New Endpoint', 'wp-rest-api-controller' ); ?></a>
				</small>
				<!-- updated API endpoint notice -->
				<span class="rest-api-endpoint-updated rest-api-controller-warning-notice">
					<span class="dashicons dashicons-info"></span>
					<?php esc_attr_e( 'This endpoint was updated. You need to re-save the settings to access this post type at the endpoint above.', 'wp-rest-api-controller' ); ?>
				</span>
				<!-- Original rest base -->
				<input type="hidden" class="rest-base-original-hidden-input" value="<?php echo esc_url( $this->rest_endpoint_base . $rest_base ); ?>">
				<!-- New rest base -->
				<input type="hidden" class="rest-base-hidden-input" name="<?php echo esc_attr( $args['option_id'] ); ?>[rest_base]" value="<?php echo esc_attr( $rest_base ); ?>">
			</p>
			<!-- End API Endpoint Example -->
		</section>

		<!-- Only if post type meta is assigned here --> 
		<?php if ( $post_type_meta && ! empty( $post_type_meta ) ) { ?>
			<section class="post-type-meta-data<?php if ( ! $active_state ) { echo ' hidden-container'; } ?>">
				<table class="widefat fixed rest-api-controller-meta-data-table" cellspacing="0">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column" scope="col">&nbsp;</th>
							<th id="columnname" class="manage-column column-columnname" scope="col"><span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Meta Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'This is the default meta key stored by WordPress.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Meta Key', 'wp-rest-api-controller' ); ?></span></th>
							<th id="columnname" class="manage-column column-columnname" scope="col"><span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Custom Meta Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'Specify a custom meta key to use instead of the default.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Custom Meta Key', 'wp-rest-api-controller' ); ?></span></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$x = 1;
						foreach ( $post_type_meta as $meta_key ) {
							$meta_active_state = ( isset( $options['meta_data'][ $meta_key ]['active'] ) ) ? true : false;
							$custom_meta_key = ( isset( $options['meta_data'][ $meta_key ]['custom_key'] ) ) ? $options['meta_data'][ $meta_key ]['custom_key'] : false;
							?>
								<tr class="<?php echo ( 0 === $x % 2 ) ? '' : 'alternate'; ?>">
									<th class="check-column" scope="row">
										<label class="switch small switch-green">
											<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][<?php echo esc_attr( $meta_key ); ?>][active]" type="checkbox" class="switch-input" value="1" <?php checked( 1, $meta_active_state );?>>
											<span class="switch-label" data-on="<?php esc_attr_e( 'On', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Off', 'wp-rest-api-controller' ); ?>"></span>
											<span class="switch-handle"></span>
										</label>
									</th>
									<td><?php echo esc_attr( $meta_key ); ?></td>
									<td>
										<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][ <?php echo esc_attr( $meta_key ); ?> ][original_meta_key]" type="hidden" value="<?php echo esc_attr( $meta_key ); ?>">
										<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][ <?php echo esc_attr( $meta_key ); ?> ][custom_key]" type="text" value="<?php echo esc_attr( $custom_meta_key ); ?>" placeholder="<?php echo esc_attr( $meta_key ); ?>">
									</td>
								</tr>
							<?php
							$x++;
						}
						?>
					</tbody>
				</table>
			</section>
		<?php } ?>

		<!-- Only if post type taxonomies is assigned here -->
		<?php if ( $post_type_taxonomies && ! empty( $post_type_taxonomies ) ) { ?>
			<section class="post-type-taxonomy-data<?php if ( ! $active_state ) { echo ' hidden-container'; } ?>">
				<table class="widefat fixed rest-api-controller-taxonomy-data-table" cellspacing="0">
					<thead>
						<tr>
							<th id="cb" class="manage-column column-cb check-column" scope="col">&nbsp;</th>
							<th id="columnname" class="manage-column column-columnname" scope="col">
								<span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Taxonomy Name', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'This is the name of the taxonomy.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Taxonomy Name', 'wp-rest-api-controller' ); ?></span>
							</th>
							<th id="columnname" class="manage-column column-columnname" scope="col">
								<span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Taxonomy Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'This is the default taxonomy stored by WordPress.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Taxonomy Key', 'wp-rest-api-controller' ); ?></span>
							</th>
							<th id="columnname" class="manage-column column-columnname" scope="col">
								<span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Custom Taxonomy Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'Specify a custom taxonomy key to use instead of the default.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Custom Taxonomy Name', 'wp-rest-api-controller' ); ?></span>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$x = 1;
						foreach ( $post_type_taxonomies as $taxonomy ) {
							$taxonomy_active_state = ( isset( $options['taxonomy'][ $taxonomy['slug'] ]['active'] ) ) ? true : false;
							$custom_taxonomy = ( isset( $options['taxonomy'][ $taxonomy['slug'] ]['custom_key'] ) ) ? $options['taxonomy'][ $taxonomy['slug'] ]['custom_key'] : false;
							?>
								<tr class="<?php echo ( 0 === $x % 2 ) ? '' : 'alternate'; ?>">
									<th class="check-column" scope="row">
										<label class="switch small switch-green">
											<input name="<?php echo esc_attr( $args['option_id'] ); ?>[taxonomy][<?php echo esc_attr( $taxonomy['slug'] ); ?>][active]" type="checkbox" class="switch-input" onchange="console.log( 'Meta Data toggled' );" value="1" <?php checked( 1, $taxonomy_active_state );?>>
											<span class="switch-label" data-on="<?php esc_attr_e( 'On', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Off', 'wp-rest-api-controller' ); ?>"></span>
											<span class="switch-handle"></span>
										</label>
									</th>
									<td><?php echo esc_attr( $taxonomy['menu_name'] ); ?></td>
									<td><?php echo esc_attr( $taxonomy['name'] ); ?></td>
									<td>
										<input name="<?php echo esc_attr( $args['option_id'] ); ?>[taxonomy][ <?php echo esc_attr( $taxonomy['slug'] ); ?> ][original_meta_key]" type="hidden" value="<?php echo esc_attr( $taxonomy['slug'] ); ?>">
										<input name="<?php echo esc_attr( $args['option_id'] ); ?>[taxonomy][ <?php echo esc_attr( $taxonomy['slug'] ); ?> ][custom_key]" type="text" value="<?php echo esc_attr( $custom_taxonomy ); ?>" placeholder="<?php echo esc_attr( $taxonomy['slug'] ); ?>">
									</td>
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
		if ( WP_DEBUG ) {
			if ( get_transient( $post_type . '_meta_keys' ) ) {
				return get_transient( $post_type . '_meta_keys' );
			}
		}
		if ( WP_DEBUG || false === ( $meta_keys = get_transient( $post_type . '_meta_keys' ) ) ) {
			// It wasn't there, so regenerate the data and save the transient
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
			set_transient( $post_type . '_meta_keys', $meta_keys, 60 * 60 * 24 ); # create 1 Day Expiration
		}
		return $meta_keys;
	}

	/**
	 * Retreive the taxonomies assigned to a post, and cache it in a transient
	 * Note: This only retreives meta that has already been stored. If the meta has been
	 * registered, but no post has any meta assigned to it - it will not display.
	 *
	 * @param  string $post_type The post type name to retreive meta data for.
	 * @return array 						 The array of taxonomy for the given post type.
	 */
	public function retreive_post_type_taxonomies( $post_type ) {
		delete_transient( $post_type . '_taxonomies' );
		// if transient is already set, abort
		if ( WP_DEBUG ) {
			if ( get_transient( $post_type . '_taxonomies' ) ) {
				return get_transient( $post_type . '_taxonomies' );
			}
		}
		if ( WP_DEBUG || false === ( $registered_taxonomies = get_transient( $post_type . '_taxonomies' ) ) ) {
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			$registered_taxonomies = array();
			if ( $taxonomies && ! empty ( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					$slug = ( isset( $taxonomy->rewrite->slug ) && ! empty( $taxonomy->rewrite->slug ) ) ? $taxonomy->rewrite->slug : sanitize_title( $taxonomy->name );
					// build an array of taxonomies to not allow
					$excluded_taxonomies = apply_filters( 'wp-rest-api-controller-excluded-taxonomies', array(
						'post_format', // Post post-format taxonomy
					) );
					// if in the excluded array, skip
					if ( in_array( $taxonomy->name, $excluded_taxonomies ) ) {
						continue;
					}
					// push to our array
					$registered_taxonomies[] = array(
						'singular_name' => $taxonomy->labels->singular_name,
						'menu_name' => $taxonomy->labels->menu_name,
						'name' => $taxonomy->name,
						'slug' => $slug,
					);
				}
			}
			set_transient( $post_type . '_taxonomies', $registered_taxonomies, 60 * 60 * 24 ); # create 1 Day Expiration
		}
		return $registered_taxonomies;
	}
}
$settings = new wp_rest_api_controller_Settings();
