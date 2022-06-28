<?php
/**
 * WP REST API Controller Settings
 *
 * @package WP REST API Controller
 */

if ( ! class_exists( 'WP_REST_API_Controller_Settings' ) ) {

	/**
	 * WP REST API Controller Settings class
	 */
	class WP_REST_API_Controller_Settings {

		/**
		 * REST API Endpoint base.
		 *
		 * @var string
		 */
		public $rest_endpoint_base;

		/**
		 * Post type slugs that we should not allow users to enable/disable.
		 *
		 * @var array
		 */
		private $always_enabled_post_type_slugs;

		/**
		 * Class constructor.
		 */
		public function __construct() {

			add_action( 'admin_init', array( $this, 'wp_rest_api_controller_settings_api_init' ) );
			add_action( 'admin_init', array( $this, 'wp_rest_api_controller_delete_api_cache' ) );

		}

		/**
		 * Return an array of excluded post types.
		 *
		 * @return array Array of excluded post types.
		 */
		public function get_excluded_post_types() {

			/**
			 * Array of excluded post types.
			 *
			 * @var array
			 */
			return apply_filters(
				'wp_rest_api_controller_excluded_post_type_slugs',
				array(
					'nav_menu_item',
					'wp_template',
					'wp_template_part',
				)
			);

		}

		/**
		 * Return an array of excluded taxonomies.
		 *
		 * @return array Array of excluded taxonomies.
		 */
		public function get_excluded_taxonomies() {

			/**
			 * Array of excluded taxonomies.
			 *
			 * @var array
			 */
			return apply_filters(
				'wp_rest_api_controller_excluded_taxonomy_slugs',
				array(
					'nav_menu',
				)
			);

		}

		/**
		 * Initialie the plugin settings.
		 */
		public function wp_rest_api_controller_settings_api_init() {

			$post_types = $this->get_registered_post_types();

			/**
			 * Get the rest URL endpoint.
			 *
			 * @link https://developer.wordpress.org/reference/functions/get_rest_url/
			 * @return Full URL to the endpoint.
			 */
			$this->rest_endpoint_base = esc_url( get_rest_url( null, '/wp/v2/' ) );

			add_settings_section(
				'wp_rest_api_controller_setting_section',
				null,
				array( $this, 'wp_rest_api_controller_setting_section_callback_function' ),
				'wp-rest-api-controller'
			);

			if ( ! empty( $post_types ) ) {

				$wp_rest_api_controller_post_types = array();

				foreach ( $post_types as $post_type_slug => $post_type_name ) {

					$post_type      = get_post_type_object( $post_type_slug );
					$post_type_name = isset( $post_type->label ) ? $post_type->label : $post_type_name;

					add_settings_field(
						"wp_rest_api_controller_post_types_{$post_type_slug}",
						$post_type_name,
						array( $this, 'wp_rest_api_controller_setting_section_setting_callback_function' ),
						'wp-rest-api-controller',
						'wp_rest_api_controller_setting_section',
						array(
							'option_id'      => "wp_rest_api_controller_post_types_{$post_type_slug}",
							'post_type_name' => $post_type_name,
							'post_type_slug' => $post_type_slug,
						)
					);

					register_setting( 'wp-rest-api-controller', "wp_rest_api_controller_post_types_{$post_type_slug}" );
					$wp_rest_api_controller_post_types[] = $post_type_slug;

				}

				update_option( 'wp_rest_api_controller_post_types', $wp_rest_api_controller_post_types );

			}

			$taxonomies = $this->get_registered_taxonomies();

			if ( ! empty( $taxonomies ) ) {

				$wp_rest_api_controller_taxonomies = array();

				foreach ( $taxonomies as $tax_slug ) {

					add_settings_field(
						"wp_rest_api_controller_taxonomies_{$tax_slug}",
						$tax_slug,
						array( $this, 'wp_rest_api_controller_setting_section_setting_tax_callback_function' ),
						'wp-rest-api-controller',
						'wp_rest_api_controller_setting_section',
						array(
							'option_id' => "wp_rest_api_controller_taxonomies_{$tax_slug}",
							'tax_slug'  => $tax_slug,
							'class'     => 'hidden',
						)
					);

					register_setting( 'wp-rest-api-controller', "wp_rest_api_controller_taxonomies_{$tax_slug}" );
					$wp_rest_api_controller_taxonomies[] = $tax_slug;

				}

				update_option( 'wp_rest_api_controller_taxonomies', $wp_rest_api_controller_taxonomies );

			}

		}

		/**
		 * Get registered post types on the site.
		 *
		 * @return array Array of registered post types.
		 */
		public function get_registered_post_types() {

			$post_types          = get_post_types();
			$excluded_post_types = $this->get_excluded_post_types();

			foreach ( $post_types as $key => $slug ) {
				if ( in_array( $key, $excluded_post_types, true ) ) {
					unset( $post_types[ $key ] );
				}
			}

			return apply_filters( 'wp_rest_api_controller_post_types', $post_types );

		}

		/**
		 * Get registered taxonomies on the site.
		 *
		 * @return array Array of available taxonomies.
		 */
		public function get_registered_taxonomies() {

			$taxonomies          = get_taxonomies();
			$excluded_taxonomies = $this->get_excluded_taxonomies();

			foreach ( $taxonomies as $key => $tax_slug ) {
				if ( in_array( $key, $excluded_taxonomies, true ) ) {
					unset( $taxonomies[ $key ] );
				}
			}

			return apply_filters( 'wp_rest_api_controller_taxonomies', $taxonomies );

		}

		/**
		 * Callback function for our setting toggle.
		 *
		 * @return mixed Markup for the settings toggle.
		 */
		public function wp_rest_api_controller_setting_section_callback_function() {
			?>
			<div class="rest-controller-tabs">
				<ul id="rest-controller-tabs-list">
						<li class="active rest-controller-tabs-list-item active"><a data-tab="post-types"><?php esc_html_e( 'Post Types', 'wp-rest-api-controller' ); ?></a></li>
						<li class="rest-controller-tabs-list-item"><a data-tab="taxonomies"><?php esc_html_e( 'Taxonomies', 'wp-rest-api-controller' ); ?></a></li>
				</ul>
			</div>

			<p id="rest-api-controller-post-types" class="rest-api-controller-post-types rest-api-controller-section"> <?php esc_html_e( 'Toggle visibility of post types and select meta data to the REST API.', 'wp-rest-api-controller' ); ?> </p>
			<p id="rest-api-controller-taxonomies" class="rest-api-controller-taxonomies rest-api-controller-section hidden"> <?php esc_html_e( 'Toggle visibility of taxonomies and select meta data to the REST API.', 'wp-rest-api-controller' ); ?> </p>
			<?php
		}

		/**
		 * Callback function for our example setting post type toggle.
		 *
		 * @param array $args Settings array.
		 *
		 * @return mixed Markup for the settings toggle.
		 */
		public function wp_rest_api_controller_setting_section_setting_callback_function( $args ) {

			$post_type_object = get_post_type_object( $args['post_type_slug'] );
			$rest_base        = ( new WP_REST_API_Controller() )->get_post_type_rest_base( $args['post_type_slug'] );
			$singular_name    = ( ! empty( $post_type_object->labels ) && ! empty( $post_type_object->labels->singular_name ) ) ? $post_type_object->labels->singular_name : $args['post_type_name'];
			$options          = get_option(
				$args['option_id'],
				array(
					'active'    => 0,
					'meta_data' => array(),
				)
			);

			$active_state   = isset( $options['active'] ) && absint( $options['active'] ) === 1 || ! empty( $post_type_object->show_in_rest ) && true === $post_type_object->show_in_rest;
			$disabled_attr  = $active_state ? '' : 'disabled=disabled';
			$post_type_meta = $this->retrieve_post_type_meta_keys( $args['post_type_slug'] );

			?>

			<!-- Anchor for our JS -->
			<span class="rest-api-controller-post-types rest-api-controller-section"></span>

			<!-- Display the checkboxes/descriptions -->
			<label class="switch switch-green">
				<input name="<?php echo esc_attr( $args['option_id'] ); ?>[active]" type="checkbox" class="switch-input" onchange="toggleEndpointLink(this);" value="1" <?php checked( 1, $active_state ); ?>>
				<span class="switch-label" data-on="<?php esc_attr_e( 'Enabled', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Disabled', 'wp-rest-api-controller' ); ?>"></span>
				<span class="switch-handle"></span>
			</label>

			<section class="rest-api-endpoint-container<?php if ( ! $active_state ) { echo ' hidden-container'; } ?>">

				<!-- API Endpoint Example -->
				<p class="description">

					<small class="edit-post-type-rest-base-disabled">
						<?php printf( '%s', '<span class="top-right tipso edit-rest-permalink-icon" data-tipso-title="' . esc_attr__( 'REST Endpoint', 'wp-rest-api-controller' ) . '" data-tipso="' . sprintf( /* translators: %s is the post type singular name. */ esc_attr__( 'Access the %s post type via the REST API at the following URL.', 'wp-rest-api-controller' ), esc_attr( $singular_name ) ) . '"><span class="dashicons dashicons-editor-help"></span></span><a class="endpoint-link" ' . esc_attr( $disabled_attr ) . ' href="' . esc_url( $this->rest_endpoint_base . $rest_base ) . '" target="_blank">' . esc_url( $this->rest_endpoint_base . $rest_base ) ); ?></a>
						<a href="#" onclick="toggleRestBaseVisbility(this,event);" class="button-secondary edit-endpoint edit-endpoint-secondary-btn" class=""><?php esc_attr_e( 'Edit Endpoint', 'wp-rest-api-controller' ); ?></a>
					</small>

					<small class="edit-post-type-rest-base-active" style="display:none;">
						<?php echo esc_url( $this->rest_endpoint_base ); ?>
						<input class="inline-input" type="text" data-rest-base="<?php echo esc_url( $this->rest_endpoint_base ); ?>" name="<?php echo esc_attr( $args['option_id'] ); ?>[rest_base]" value="<?php echo esc_attr( $rest_base ); ?>">
						<a href="#" onclick="toggleRestBaseVisbility(this,event);" class="button-secondary save-endpoint edit-endpoint-secondary-btn" class=""><?php esc_attr_e( 'Save New Endpoint', 'wp-rest-api-controller' ); ?></a>
					</small>

					<!-- updated API endpoint notice -->
					<span class="rest-api-endpoint-updated rest-api-controller-warning-notice">
						<span class="dashicons dashicons-info"></span>
						<?php esc_attr_e( 'This endpoint was updated. You need to re-save the settings to access this post type at the endpoint above.', 'wp-rest-api-controller' ); ?>
					</span>
				</p>

				<!-- End API Endpoint Example -->

			</section>

			<!-- Only if post type meta is assigned here -->
			<?php if ( $post_type_meta && ! empty( $post_type_meta ) ) { ?>
				<section class="object-meta-data post-type-meta-data<?php if ( ! $active_state ) { echo ' hidden-container'; } ?>">
					<table class="widefat fixed rest-api-controller-meta-data-table" cellspacing="0">
						<thead>
							<tr>
								<th id="cb" class="manage-column column-cb check-column" scope="col">
									<label class="switch switch-green">
										<input name="" type="checkbox" class="switch-input all-meta-switch-input" value="1">
										<span class="switch-label" data-on="<?php esc_attr_e( 'Disable All', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Enable All', 'wp-rest-api-controller' ); ?>"></span>
										<span class="switch-handle"></span>
									</label>
								</th>
								<th id="columnname" class="manage-column column-columnname" scope="col"><span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Meta Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'This is the default meta key stored by WordPress.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Meta Key', 'wp-rest-api-controller' ); ?></span></th>
								<th id="columnname" class="manage-column column-columnname" scope="col"><span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Custom Meta Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'Specify a custom meta key to use instead of the default.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Custom Meta Key', 'wp-rest-api-controller' ); ?></span></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$x = 1;
							foreach ( $post_type_meta as $meta_key ) {
								$meta_active_state = isset( $options['meta_data'][ $meta_key ]['active'] );
								$custom_meta_key   = isset( $options['meta_data'][ $meta_key ]['custom_key'] ) ? $options['meta_data'][ $meta_key ]['custom_key'] : '';
								?>
									<tr class="<?php echo ( 0 === $x % 2 ) ? '' : 'alternate'; ?>">
										<th class="check-column" scope="row">
											<label class="switch small switch-green">
												<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][<?php echo esc_attr( $meta_key ); ?>][active]" type="checkbox" class="switch-input meta-switch-input" value="1" <?php checked( 1, $meta_active_state ); ?>>
												<span class="switch-label" data-on="<?php esc_attr_e( 'On', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Off', 'wp-rest-api-controller' ); ?>"></span>
												<span class="switch-handle"></span>
											</label>
										</th>
										<td><?php echo esc_attr( $meta_key ); ?></td>
										<td>
											<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][<?php echo esc_attr( $meta_key ); ?>][original_meta_key]" type="hidden" value="<?php echo esc_attr( $meta_key ); ?>">
											<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][<?php echo esc_attr( $meta_key ); ?>][custom_key]" type="text" value="<?php echo esc_attr( $custom_meta_key ); ?>" placeholder="<?php echo esc_attr( $meta_key ); ?>">
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
			<p class="description"><?php printf( /* translators: %s is a post type label. */ esc_attr__( 'Expose the %s post type to the REST API.', 'wp-rest-api-controller' ), '<code>' . esc_attr( $singular_name ) . '</code>' ); ?></p>
			<?php
		}

		/**
		 * Display the rest API toggle.
		 *
		 * @param  array $args Settings array.
		 *
		 * @return mixed Markup for the API toggle switch.
		 */
		public function wp_rest_api_controller_setting_section_setting_tax_callback_function( $args ) {
			$taxonomy = get_taxonomy( $args['tax_slug'] );

			$options = get_option(
				$args['option_id'],
				array(
					'active'    => 0,
					'meta_data' => array(),
					'rest_base' => ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $args['tax_slug'],
				)
			);

			$active_state  = isset( $options['active'] ) && absint( $options['active'] ) === 1;
			$rest_base     = ! empty( $options['rest_base'] ) ? $options['rest_base'] : $args['tax_slug'];
			$taxonomy_meta = $this->retrieve_taxonomy_meta_keys( $taxonomy->name );
			$singular_name = ( isset( $taxonomy->labels ) && isset( $taxonomy->labels->singular_name ) && ! empty( $taxonomy->labels->singular_name ) ) ? $taxonomy->labels->singular_name : $args['tax_slug'];

			?>

			<span class="rest-api-controller-taxonomies rest-api-controller-section"></span>

			<!-- Display the checkboxes/descriptions -->
			<label class="switch switch-green">
				<input name="<?php echo esc_attr( $args['option_id'] ); ?>[active]" type="checkbox" class="switch-input" onchange="toggleEndpointLink(this);" value="1" <?php checked( 1, $active_state ); ?>>
				<span class="switch-label" data-on="<?php esc_attr_e( 'Enabled', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Disabled', 'wp-rest-api-controller' ); ?>"></span>
				<span class="switch-handle"></span>
			</label>

			<section class="rest-api-endpoint-container<?php if ( ! $active_state ) { echo ' hidden-container'; } ?>">

				<!-- API Endpoint Example -->
				<p class="description">

					<small class="edit-post-type-rest-base-disabled">
						<?php printf( '%s', '<span class="top-right tipso edit-rest-permalink-icon" data-tipso-title="' . esc_attr__( 'REST Endpoint', 'wp-rest-api-controller' ) . '" data-tipso="' . sprintf( /* translators: %s is the taxonomy label. */ esc_attr__( 'Access the %s taxonomy via the REST API at the following URL.', 'wp-rest-api-controller' ), esc_attr( $taxonomy->labels->menu_name ) ) . '"><span class="dashicons dashicons-editor-help"></span></span><a class="endpoint-link" href="' . esc_url( $this->rest_endpoint_base . $rest_base ) . '" target="_blank">' . esc_url( $this->rest_endpoint_base . $rest_base ) ); ?></a>
						<a href="#" onclick="toggleRestBaseVisbility(this,event);" class="button-secondary edit-endpoint edit-endpoint-secondary-btn" class=""><?php esc_attr_e( 'Edit Endpoint', 'wp-rest-api-controller' ); ?></a>
					</small>

					<small class="edit-post-type-rest-base-active" style="display:none;">
						<?php echo esc_url( $this->rest_endpoint_base ); ?>
						<input class="inline-input" type="text" data-rest-base="<?php echo esc_url( $this->rest_endpoint_base ); ?>" name="<?php echo esc_attr( $args['option_id'] ); ?>[rest_base]" value="<?php echo esc_attr( $rest_base ); ?>">
						<a href="#" onclick="toggleRestBaseVisbility(this,event);" class="button-secondary save-endpoint edit-endpoint-secondary-btn" class=""><?php esc_attr_e( 'Save New Endpoint', 'wp-rest-api-controller' ); ?></a>
					</small>

					<!-- updated API endpoint notice -->
					<span class="rest-api-endpoint-updated rest-api-controller-warning-notice">
						<span class="dashicons dashicons-info"></span>
						<?php esc_attr_e( 'This endpoint was updated. You need to re-save the settings to access this post type at the endpoint above.', 'wp-rest-api-controller' ); ?>
					</span>
				</p>

				<!-- End API Endpoint Example -->

			</section>

			<!-- Only if post type meta is assigned here -->
			<?php if ( $taxonomy_meta && ! empty( $taxonomy_meta ) ) { ?>
				<section class="object-meta-data taxonomy-meta-data<?php if ( ! $active_state ) { echo ' hidden-container'; } ?>">
					<table class="widefat fixed rest-api-controller-meta-data-table" cellspacing="0">
						<thead>
							<tr>
								<th id="cb" class="manage-column column-cb check-column" scope="col">
									<label class="switch switch-green">
										<input name="" type="checkbox" class="switch-input all-meta-switch-input" value="1">
										<span class="switch-label" data-on="<?php esc_attr_e( 'Disable All', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Enable All', 'wp-rest-api-controller' ); ?>"></span>
										<span class="switch-handle"></span>
									</label>
								</th>
								<th id="columnname" class="manage-column column-columnname" scope="col"><span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Meta Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'This is the default meta key stored by WordPress.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Meta Key', 'wp-rest-api-controller' ); ?></span></th>
								<th id="columnname" class="manage-column column-columnname" scope="col"><span class="top-right tipso" data-tipso-title="<?php esc_attr_e( 'Custom Meta Key', 'wp-rest-api-controller' ); ?>" data-tipso="<?php esc_attr_e( 'Specify a custom meta key to use instead of the default.', 'wp-rest-api-controller' ); ?>"><?php esc_attr_e( 'Custom Meta Key', 'wp-rest-api-controller' ); ?></span></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$x = 1;
							foreach ( $taxonomy_meta as $meta_key ) {
								$meta_active_state = isset( $options['meta_data'][ $meta_key ]['active'] );
								$custom_meta_key   = isset( $options['meta_data'][ $meta_key ]['custom_key'] ) ? $options['meta_data'][ $meta_key ]['custom_key'] : '';
								?>
									<tr class="<?php echo ( 0 === $x % 2 ) ? '' : 'alternate'; ?>">
										<th class="check-column" scope="row">
											<label class="switch small switch-green">
												<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][<?php echo esc_attr( $meta_key ); ?>][active]" type="checkbox" class="switch-input meta-switch-input" value="1" <?php checked( 1, $meta_active_state ); ?>>
												<span class="switch-label" data-on="<?php esc_attr_e( 'On', 'wp-rest-api-controller' ); ?>" data-off="<?php esc_attr_e( 'Off', 'wp-rest-api-controller' ); ?>"></span>
												<span class="switch-handle"></span>
											</label>
										</th>
										<td><?php echo esc_attr( $meta_key ); ?></td>
										<td>
											<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][<?php echo esc_attr( $meta_key ); ?>][original_meta_key]" type="hidden" value="<?php echo esc_attr( $meta_key ); ?>">
											<input name="<?php echo esc_attr( $args['option_id'] ); ?>[meta_data][<?php echo esc_attr( $meta_key ); ?>][custom_key]" type="text" value="<?php echo esc_attr( $custom_meta_key ); ?>" placeholder="<?php echo esc_attr( $meta_key ); ?>">
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
			<p class="description"><?php printf( /* translators: %s The taxonomy label. */ esc_attr__( 'Expose the %s taxonomy to the REST API.', 'wp-rest-api-controller' ), '<code>' . esc_attr( $singular_name ) . '</code>' ); ?></p>
			<?php
		}

		/**
		 * Retrieve the meta data assigned to a post, and cache it in a transient
		 * Note: This only retreives meta that has already been stored. If the meta has been
		 * registered, but no post has any meta assigned to it - it will not display.
		 *
		 * @param  string $post_type The post type name to retreive meta data for.
		 * @return array             The array of meta data for the given post type.
		 */
		public function retrieve_post_type_meta_keys( $post_type ) {

			$meta_keys = get_transient( $post_type . '_meta_keys' );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG || false === $meta_keys ) {

				global $wpdb;

				$meta_keys = $wpdb->get_col(
					$wpdb->prepare(
						"
						SELECT DISTINCT($wpdb->postmeta.meta_key)
						FROM $wpdb->posts
						LEFT JOIN $wpdb->postmeta
						ON $wpdb->posts.ID = $wpdb->postmeta.post_id
						WHERE $wpdb->posts.post_type = '%s'
						",
						$post_type
					)
				);

				$meta_keys = array_filter( $meta_keys );

				set_transient( $post_type . '_meta_keys', $meta_keys, DAY_IN_SECONDS );

			}

			return $meta_keys;

		}

		/**
		 * Retrieve the meta data assigned to a post, and cache it in a transient
		 * Note: This only retreives meta that has already been stored. If the meta has been
		 * registered, but no post has any meta assigned to it - it will not display.
		 *
		 * @param  string $tax_slug  The post type name to retreive meta data for.
		 * @return array             The array of meta data for the given post type.
		 */
		public function retrieve_taxonomy_meta_keys( $tax_slug ) {

			$meta_keys = get_transient( $tax_slug . '_meta_keys' );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG || false === $meta_keys ) {

				global $wpdb;

				$meta_keys = $wpdb->get_col(
					$wpdb->prepare(
						"
							SELECT DISTINCT($wpdb->termmeta.meta_key)
							FROM $wpdb->terms
							LEFT JOIN $wpdb->termmeta
							ON $wpdb->terms.term_id = $wpdb->termmeta.term_id
							LEFT JOIN $wpdb->term_taxonomy
							ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id
							WHERE $wpdb->term_taxonomy.taxonomy = '%s'
						",
						$tax_slug
					)
				);
				$meta_keys = array_filter( $meta_keys );

				set_transient( $tax_slug . '_meta_keys', $meta_keys, DAY_IN_SECONDS );

			}

			return $meta_keys;

		}

		/**
		 * Clear The REST API Controller Transients
		 *
		 * @since 1.0.1
		 */
		public function wp_rest_api_controller_delete_api_cache() {

			if (
				! isset( $_GET['_wpnonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), 'clear-api-cache' )
			) {
				return;
			}

			$post_types = ( new WP_REST_API_Controller() )->get_stored_post_types();
			$taxonomies = $this->get_registered_taxonomies();

			$transient_keys = array_merge( $post_types, $taxonomies );

			if ( ! empty( $transient_keys ) ) {
				foreach ( $transient_keys as $key ) {
					delete_transient( "{$key}_meta_keys" );
				}
			}

			wp_safe_redirect(
				add_query_arg(
					array(
						'wp-rest-api-cache-cleared' => 'true',
					),
					admin_url( 'tools.php?page=wp-rest-api-controller-settings' )
				)
			);

		}

	}

}

new WP_REST_API_Controller_Settings();
