## WP REST API Controller by YIKES, Inc., Evan Herman

Control post type and associated meta data exposure to the REST API. Say goodbye to manually enabling/disabling rest API endpoints via code, now you can use our dashboard interface to control exposure of your post types to the WP REST API.

**Features:**
* Enable/Disable custom post type REST API endpoints
* Enable/Disable custom post type meta data (add or remove meta fields from API requests)
* Rename custom post type base endpoints (eg: change the posts API endpoints to `/announcements/`, and access it at `/wp-json/wp/v2/announcements`)
* Rename meta data models in API requests, so they are more user friendly.
* Manipulate and control post types and data created by third party plugins and themes.

<strong>WP REST API Controller Settings:</strong>

![WP REST API Controller Settings Page](https://cldup.com/DVYcj6g3RO.png)

<strong>Important: This plugin requires the [WP REST API (Version 2)](https://wordpress.org/plugins/rest-api/) to be installed. Previous versions will not work properly as the API endpoints differ.</strong>

<em>This plugin has reached maturity and has been released to the [WordPress.org repository](https://wordpress.org/support/plugin/wp-rest-api-controller). Enjoy!</em>

All pull requests welcome!

##### TwentySixteen Child Theme

During development, there is a directory bundled with this plugin `TwentySixteen-Child`, which is a child theme of [TwentySixteen](https://wordpress.org/themes/twentysixteen/) WordPress theme, and is used for testing purposes. It has a number of custom post types, and post meta registered which is used for testing the API endpoints.

The child theme is not required for this plugin to function properly, and can (<em>and should be</em>) deleted before installing and activating the plugin.

##### Filters

* `wp_rest_api_controller_rest_base`
* `wp_rest_api_controller_post_types` (***Note: Revisions and nav menu items are not currently controllable with this plugin***)
* `wp_rest_api_controller_api_property_value`
* `wp_rest_api_controller_exclude_hidden_meta_keys_post_types`
* `wp-rest-api-controller-excluded-taxonomies`
