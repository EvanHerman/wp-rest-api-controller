<?php
/**
 * Plugin Name: WP REST API Controller
 * Description: WP REST API Controller enables a UI to toggle REST API endpoints on/off and customize key names.
 * Version: 2.1.2
 * Author: Evan Herman
 * Author URI:  https://www.evan-herman.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: wp-rest-api-controller
 * Domain Path: /languages
 *
 * WP REST API Controller is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.
 *
 * @package WP REST API Controller
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

define( 'WP_REST_API_CONTROLLER_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_REST_API_CONTROLLER_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_REST_API_CONTROLLER_VERSION', '2.1.2' );

// Only load class if it hasn't already been loaded.
if ( ! class_exists( 'WP_REST_API_Controller' ) ) {

	/**
	 * Class WP_REST_API_Controller.
	 */
	class WP_REST_API_Controller {

		/**
		 * Stored post types.
		 * Enabled post types and associated data.
		 *
		 * @var array
		 */
		public $stored_post_types;

		/**
		 * Stored taxonomies.
		 * Enabled taxonomies and associated data.
		 *
		 * @var array
		 */
		public $stored_taxonomies;

		/**
		 * Main Constructor.
		 */
		public function __construct() {

			require_once WP_REST_API_CONTROLLER_PATH . 'admin/class-wp-rest-api-controller-admin.php';

			add_action( 'init', array( $this, 'expose_post_type_api_endpoints' ), PHP_INT_MAX );
			add_action( 'init', array( $this, 'expose_taxonomy_api_endpoints' ), PHP_INT_MAX );

			add_action( 'rest_api_init', array( $this, 'append_post_type_meta_data_to_api' ), PHP_INT_MAX );
			add_action( 'rest_api_init', array( $this, 'append_taxonomy_meta_data_to_api' ), PHP_INT_MAX );

			add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		}


		/**
		 * Expose (or disable) post types to the REST API.
		 *
		 * @since 1.0.0
		 */
		public function expose_post_type_api_endpoints() {

			$stored_post_types = $this->get_stored_post_types();

			if ( empty( $stored_post_types ) ) {

				return;

			}

			global $wp_post_types;

			foreach ( $stored_post_types as $post_type_slug => $enabled ) {

				if ( ! isset( $wp_post_types[ $post_type_slug ] ) || ! is_object( $wp_post_types[ $post_type_slug ] ) ) {

					continue;

				}

				$rest_base = $this->get_post_type_rest_base( $post_type_slug );

				if ( 'enabled' !== $enabled ) {

					$wp_post_types[ $post_type_slug ]->show_in_rest = false;

					continue;

				}

				$wp_post_types[ $post_type_slug ]->show_in_rest = true;
				$wp_post_types[ $post_type_slug ]->rest_base    = $rest_base;

			}

		}

		/**
		 * Expose (or disable) taxonomies to the REST API
		 *
		 * @since 1.0.0
		 */
		public function expose_taxonomy_api_endpoints() {

			$stored_taxonomies = $this->get_stored_taxonomies();

			if ( empty( $stored_taxonomies ) ) {

				return;

			}

			global $wp_taxonomies;

			foreach ( $stored_taxonomies as $tax_slug => $tax_params ) {

				if ( ! array_key_exists( $tax_slug, $wp_taxonomies ) || ! is_object( $wp_taxonomies[ $tax_slug ] ) ) {

					continue;

				}

				if ( ! $tax_params['enabled'] ) {

					$wp_taxonomies[ $tax_slug ]->show_in_rest = false;

					continue;

				}

				$wp_taxonomies[ $tax_slug ]->show_in_rest = true;
				$wp_taxonomies[ $tax_slug ]->rest_base    = $tax_params['rest_base'];

			}

		}

		/**
		 * Get the stored post types to expose/disable to the REST API
		 *
		 * @since 1.0.0
		 *
		 * @return array Array of post type slugs to expose to our API.
		 */
		public function get_stored_post_types() {

			$stored_post_types = get_option( 'wp_rest_api_controller_post_types', false );

			if ( ! $stored_post_types ) {

				return array();

			}

			$post_types_array = array();

			foreach ( $stored_post_types as $post_type_slug ) {

				$post_type_options = get_option( "wp_rest_api_controller_post_types_{$post_type_slug}", false );

				// This means nothing has been set. If the options have never been saved, don't turn things on and off.
				if ( ! $post_type_options ) {

					continue;

				}

				if ( $post_type_options && ( isset( $post_type_options['active'] ) && $post_type_options['active'] ) ) {

					$post_types_array[ $post_type_slug ] = 'enabled';

					continue;

				}

				$post_types_array[ $post_type_slug ] = 'disabled';

			}

			return $post_types_array;

		}

		/**
		 * Get the stored taxonomies to expose/disable to the REST API
		 *
		 * @since 1.0.0
		 *
		 * @return array Array of post type slugs to expose to our API
		 */
		public function get_stored_taxonomies() {

			$taxonomies = get_option( 'wp_rest_api_controller_taxonomies', false );

			if ( ! $taxonomies ) {

				return;

			}

			$taxonomies_array = array();

			foreach ( $taxonomies as $tax_slug ) {

				$tax_options = get_option( "wp_rest_api_controller_taxonomies_{$tax_slug}", false );

				// This means nothing has been set. If the options have never been saved, don't turn things on and off.
				if ( ! $tax_options ) {

					continue;

				}

				$taxonomies_array[ $tax_slug ] = array(
					'enabled'   => ! empty( $tax_options['active'] ) && $tax_options['active'],
					'rest_base' => ! empty( $tax_options['rest_base'] ) ? $tax_options['rest_base'] : $tax_slug,
					'meta_data' => ! empty( $tax_options['meta_data'] ) ? $tax_options['meta_data'] : array(),
				);

			}

			return $taxonomies_array;

		}

		/**
		 * Callback function to append our metadata value to the field
		 *
		 * @param  array  $object     Post object.
		 * @param  string $field_name Field name.
		 *
		 * @return string The original meta key name to use in get_post_meta();
		 */
		public function custom_meta_data_callback( $object, $field_name ) {

			$is_tax      = isset( $object['taxonomy'] );
			$object_type = $is_tax ? $object['taxonomy'] : $object['type'];

			$original_meta_key_name = $this->get_original_meta_key_name( $object_type, $field_name, $is_tax );

			// If we can't find the original meta key name, then return.
			// We do not want our get_post_meta() call to look like get_post_meta( $id, NULL, true ) or all meta fields will be returned.
			if ( empty( $original_meta_key_name ) ) {
				return;
			}

			$meta_value = $is_tax ? get_term_meta( $object['id'], $original_meta_key_name, true ) : get_post_meta( $object['id'], $original_meta_key_name, true );

			return apply_filters( 'wp_rest_api_controller_api_property_value', $meta_value, $object['id'], $original_meta_key_name, $is_tax );

		}

		/**
		 * Get the rest base for a given post type
		 *
		 * @param  string $post_type_slug Slug of the post type to return.
		 *
		 * @return string REST API base name.
		 */
		public function get_post_type_rest_base( $post_type_slug ) {

			$post_type_options = get_option(
				"wp_rest_api_controller_post_types_{$post_type_slug}",
				array(
					'active'    => 0,
					'meta_data' => array(),
				)
			);

			$post_type_obj = get_post_type_object( $post_type_slug );

			$rest_base = ( isset( $post_type_options['rest_base'] ) && ! empty( $post_type_options['rest_base'] ) ) ? $post_type_options['rest_base'] : ( ( isset( $post_type_obj->rest_base ) && ! empty( $post_type_obj->rest_base ) ) ? $post_type_obj->rest_base : $post_type_slug );

			return apply_filters( 'wp_rest_api_controller_rest_base', $rest_base, $post_type_slug );

		}

		/**
		 * Based on the value of a custom meta key in the WP REST API array
		 * we return the original key, so that get_post_meta() can be used properly
		 *
		 * @param string $object_slug          The post object slug.
		 * @param string $custom_meta_key_name The custom meta key defined in the options.
		 * @param string $is_tax               Is this a taxaonomy.
		 *
		 * @return string The original meta key to use in get_post_meta() function
		 */
		public function get_original_meta_key_name( $object_slug, $custom_meta_key_name, $is_tax = false ) {

			$option_name  = $is_tax ? "wp_rest_api_controller_taxonomies_{$object_slug}" : "wp_rest_api_controller_post_types_{$object_slug}";
			$meta_options = get_option(
				$option_name,
				array(
					'active'    => 0,
					'meta_data' => array(),
				)
			);

			if ( empty( $meta_options['meta_data'] ) ) {
				return;
			}

			foreach ( $meta_options['meta_data'] as $key ) {
				if ( strtolower( $custom_meta_key_name ) === strtolower( $key['custom_key'] ) ) {
					return $key['original_meta_key'];
				}
			}

			// If we can't find this key, it's already the original meta key name, so return it.
			return $custom_meta_key_name;

		}

		/**
		 * Append post type meta data to the API request
		 *
		 * All requests append data using get_post_meta() inside custom_meta_data_callback()
		 * Users can override the value and provide a custom value using our filter `wp_rest_api_controller_api_property_value`
		 * For help, see the 'Other Notes' section in the WordPress.org repository for this plugin
		 *
		 * @since 1.0.0
		 */
		public function append_post_type_meta_data_to_api() {

			$stored_post_types = $this->get_stored_post_types();

			if ( empty( $stored_post_types ) ) {

				return;

			}

			foreach ( $stored_post_types as $post_type_slug => $enabled ) {

				if ( 'enabled' !== $enabled ) {
					continue;
				}

				$post_type_options = get_option(
					"wp_rest_api_controller_post_types_{$post_type_slug}",
					array(
						'active'    => false,
						'meta_data' => array(),
					)
				);

				if ( ! isset( $post_type_options['meta_data'] ) || empty( $post_type_options['meta_data'] ) ) {
					continue;
				}

				foreach ( $post_type_options['meta_data'] as $meta_key => $meta_data ) {

					if ( ! isset( $meta_data['active'] ) || ( isset( $meta_data['active'] ) && 1 !== absint( $meta_data['active'] ) ) ) {
						continue;
					}

					$rest_api_meta_name = ( isset( $meta_data['custom_key'] ) && ! empty( $meta_data['custom_key'] ) ) ? $meta_data['custom_key'] : $meta_key;

					register_rest_field(
						$post_type_slug,
						$rest_api_meta_name,
						array(
							'get_callback'    => array( $this, 'custom_meta_data_callback' ),
							'update_callback' => null,
							'schema'          => null,
						)
					);

				}

			}

		}

		/**
		 * Append taxonomy meta data to the WP API.
		 */
		public function append_taxonomy_meta_data_to_api() {

			$stored_taxonomies = $this->get_stored_taxonomies();

			if ( empty( $stored_taxonomies ) ) {

				return;

			}

			foreach ( $stored_taxonomies as $tax_slug => $tax_params ) {

				if ( ! $tax_params['enabled'] || empty( $tax_params['meta_data'] ) ) {
					continue;
				}

				foreach ( $tax_params['meta_data'] as $meta_key => $meta_data ) {

					if ( empty( $meta_data['active'] ) ) {
						continue;
					}

					$rest_api_meta_name = ! empty( $meta_data['custom_key'] ) ? $meta_data['custom_key'] : $meta_key;

					register_rest_field(
						$tax_slug,
						$rest_api_meta_name,
						array(
							'get_callback'    => array( $this, 'custom_meta_data_callback' ),
							'update_callback' => null,
							'schema'          => null,
						)
					);

				}

			}

		}

		/**
		 * Internationalization.
		 *
		 * @codeCoverageIgnore
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain(
				'wp-rest-api-controller',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);

		}
	}
}

new WP_REST_API_Controller();
