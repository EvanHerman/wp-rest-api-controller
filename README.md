# WP REST API Controller

![Rest API Controller Banner](https://ps.w.org/wp-rest-api-controller/assets/banner-772x250.png)

Control post type and associated meta data exposure to the REST API. Say goodbye to manually enabling/disabling rest API endpoints via code, now you can use our dashboard interface to control exposure of your post types to the WP REST API.

**Features:**
* Enable/Disable REST API endpoints for taxonomies and CPTs
* Enable/Disable post type/taxonomy meta data (add or remove meta fields from API requests)
* Rename post type/taxonomy base endpoints
* Rename meta data names in API requests so they are more user friendly.

<em>This plugin has reached maturity and has been released to the [WordPress.org repository](https://wordpress.org/support/plugin/wp-rest-api-controller). Enjoy!</em>

### Contributing

All pull requests welcome!

#### WP REST API Controller Settings:

![WP REST API Controller Settings Page](https://cldup.com/DVYcj6g3RO.png)

<strong>Important: As of December 6th, 2016 this plugin requires WordPress version 4.7, which has the WP REST API built into core. Prior versions of WordPress will no longer work with WP Rest API Controller version 1.3 or later. Please update your sites version of WordPress to 4.7 or later.</strong>

##### Filters

* `wp_rest_api_controller_rest_base`
* `wp_rest_api_controller_post_types` (***Note: Revisions, Posts, Pages, and Nav Menu Items are not controllable with this plugin without using filters***)
* `wp_rest_api_controller_api_property_value`
* `wp_rest_api_controller_always_enabled_post_types`
* `wp_rest_api_controller_excluded_taxonomy_slugs`
* `wp_rest_api_controller_taxonomies`
* `wp_rest_api_controller_retrieve_meta_single`

_________________

<div align="center" style="font-weight: bold;">Originally built with <span style="color: #F3A4B2;">&hearts;</span> by YIKES Inc. in Philadelphia, PA.<br />Now Maintained by Evan Herman in Lancaster, PA.</div>
