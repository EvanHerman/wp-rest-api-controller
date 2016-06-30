<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}


// Custom Post types for the REST API

// Register Custom Post Type
function custom_post_type() {

	$labels = array(
		'name'                  => _x( 'Jobs', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Jobs', 'text_domain' ),
		'name_admin_bar'        => __( 'Jobs', 'text_domain' ),
		'archives'              => __( 'Job Archives', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Job:', 'text_domain' ),
		'all_items'             => __( 'All Jobs', 'text_domain' ),
		'add_new_item'          => __( 'Add New Job', 'text_domain' ),
		'add_new'               => __( 'Add Job', 'text_domain' ),
		'new_item'              => __( 'New Job', 'text_domain' ),
		'edit_item'             => __( 'Edit Job', 'text_domain' ),
		'update_item'           => __( 'Update Job', 'text_domain' ),
		'view_item'             => __( 'View Job', 'text_domain' ),
		'search_items'          => __( 'Search Job', 'text_domain' ),
		'not_found'             => __( 'Job Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Job Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Job Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set job image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove job image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as job image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into job', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this job', 'text_domain' ),
		'items_list'            => __( 'Jobs list', 'text_domain' ),
		'items_list_navigation' => __( 'Jobs list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter jobs list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Job', 'text_domain' ),
		'description'           => __( 'Job', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 30,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'jobs', $args );

}
add_action( 'init', 'custom_post_type', 0 );


// Register Custom Post Type
function custom_post_type_2() {

	$labels = array(
		'name'                  => _x( 'Portfolios', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Portfolio', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Portfolio', 'text_domain' ),
		'name_admin_bar'        => __( 'Portfolio', 'text_domain' ),
		'archives'              => __( 'Portfolio Archives', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Portfolio:', 'text_domain' ),
		'all_items'             => __( 'All Portfolio Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Portfolio Item', 'text_domain' ),
		'add_new'               => __( 'Add Portfolio Item', 'text_domain' ),
		'new_item'              => __( 'New Portfolio Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Portfolio Item', 'text_domain' ),
		'update_item'           => __( 'Update Portfolio Item', 'text_domain' ),
		'view_item'             => __( 'View Portfolio Item', 'text_domain' ),
		'search_items'          => __( 'Search Portfolio Items', 'text_domain' ),
		'not_found'             => __( 'Portfolio Item Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Portfolio Item Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Portfolio Item Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set portfolio image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove portfolio image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as portfolio image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into portfolio', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this portfolio', 'text_domain' ),
		'items_list'            => __( 'Portfolios list', 'text_domain' ),
		'items_list_navigation' => __( 'Portfolios list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter portfolios list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Job', 'text_domain' ),
		'description'           => __( 'Job', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', ),
		'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 30,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		// 'rest_base'					=> 'portfolio-api',
		// 'show_in_rest'       => true,
		// 'rest_controller_class' => 'WP_REST_Posts_Controller',
	);
	register_post_type( 'portfolio', $args );

}
add_action( 'init', 'custom_post_type_2', 0 );


// Custom Metaboxes
/**
 * Register meta box(es).
 */
function jobs_register_meta_boxes() {
	add_meta_box( 'jobs-metabox', __( 'Jobs Details', 'textdomain' ), 'jobs_my_display_callback', 'jobs' );
}
add_action( 'add_meta_boxes', 'jobs_register_meta_boxes' );

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function jobs_my_display_callback( $post ) {
	$job_title = ( get_post_meta( $post->ID, 'job_title', true ) ) ? get_post_meta( $post->ID, 'job_title', true ) : '';
	$company_name = ( get_post_meta( $post->ID, 'company_name', true ) ) ? get_post_meta( $post->ID, 'company_name', true ) : '';
	$employment_length = ( get_post_meta( $post->ID, 'employment_length', true ) ) ? get_post_meta( $post->ID, 'employment_length', true ) : '';
	// Display code/markup goes here. Don't forget to include nonces!
	?>
	<p>
		<label>Job Title <br />
			<input type="text" class="widefat" name="job_title" value="<?php echo esc_attr( $job_title ); ?>" />
		</label>
	</p>
	<p>
		<label>Company Name <br />
			<input type="text" class="widefat" name="company_name" value="<?php echo esc_attr( $company_name ); ?>" />
		</label>
	</p>
	<p>
		<label>Length of Employment <br />
			<select name="employment_length" class="widefat">
				<option value="1" <?php selected( $employment_length, 1 ); ?>>1</option>
				<option value="2" <?php selected( $employment_length, 2 ); ?>>2</option>
				<option value="3" <?php selected( $employment_length, 3 ); ?>>3</option>
				<option value="4" <?php selected( $employment_length, 4 ); ?>>4</option>
				<option value="5" <?php selected( $employment_length, 5 ); ?>>5</option>
			</select>
		</label>
	</p>
	<?php
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function jobs_save_meta_box( $post_id ) {
	if ( 'jobs' === get_post_type( $post_id ) ) {
		$job_title = ( isset( $_POST['job_title'] ) ) ? $_POST['job_title'] : '';
		$company_name = ( isset( $_POST['company_name'] ) ) ? $_POST['company_name'] : '';
		$job_title = ( isset( $_POST['job_title'] ) ) ? $_POST['job_title'] : '';
		$employment_length = ( isset( $_POST['employment_length'] ) ) ? $_POST['employment_length'] : 1;
		// Save logic goes here. Don't forget to include nonce checks!
		update_post_meta( $post_id, 'job_title', $job_title );
		update_post_meta( $post_id, 'company_name', $company_name );
		update_post_meta( $post_id, 'employment_length', $employment_length );
	}
}
add_action( 'save_post', 'jobs_save_meta_box' );


// Portfolio Metaboxes
/**
 * Register meta box(es).
 */
function portfolio_register_meta_boxes() {
	add_meta_box( 'portfolio-metabox', __( 'Portfolio Details', 'textdomain' ), 'portfolio_my_display_callback', 'portfolio' );
}
add_action( 'add_meta_boxes', 'portfolio_register_meta_boxes' );

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function portfolio_my_display_callback( $post ) {
	if ( 'portfolio' === get_post_type( $post_id ) ) {
		$portfolio_title = ( get_post_meta( $post->ID, 'portfolio_title', true ) ) ? get_post_meta( $post->ID, 'portfolio_title', true ) : '';
		$price = ( get_post_meta( $post->ID, 'price', true ) ) ? get_post_meta( $post->ID, 'price', true ) : '';
		$details = ( get_post_meta( $post->ID, 'details', true ) ) ? get_post_meta( $post->ID, 'details', true ) : '';
		// Display code/markup goes here. Don't forget to include nonces!
		?>
		<p>
			<label>Portfolio Title <br />
				<input type="text" class="widefat" name="portfolio_title" value="<?php echo esc_attr( $portfolio_title ); ?>" />
			</label>
		</p>
		<p>
			<label>Portfolio Price <br />
				<input type="text" class="widefat" name="price" value="<?php echo esc_attr( $price ); ?>" />
			</label>
		</p>
		<p>
			<label>Portfoilio Item Details <br />
				<?php wp_editor( $details, 'details', array(
					'textarea_name' => 'details',
				) ); ?>
			</label>
		</p>
		<?php
	}
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function portfolio_save_meta_box( $post_id ) {
	$portfolio_title = ( isset( $_POST['portfolio_title'] ) ) ? $_POST['portfolio_title'] : '';
	$price = ( isset( $_POST['price'] ) ) ? $_POST['price'] : '';
	$details = ( isset( $_POST['details'] ) ) ? $_POST['details'] : 1;
	// Save logic goes here. Don't forget to include nonce checks!
	update_post_meta( $post_id, 'portfolio_title', $portfolio_title );
	update_post_meta( $post_id, 'price', $price );
	update_post_meta( $post_id, 'details', $details );
}
add_action( 'save_post', 'portfolio_save_meta_box' );
