<h1 align="center">WP REST API Controller
	<a href="https://github.com/EvanHerman/wp-rest-api-controller/releases/latest/">
		<img src="https://img.shields.io/static/v1?pluginVersion=&message=v2.0.7&label=&color=999&style=flat-square">
	</a>
</h1>

<h4 align="center">Control post type and associated meta data exposure to the REST API. Say goodbye to manually enabling/disabling rest API endpoints via code, now you can use our dashboard interface to control exposure of your post types/taxonomies to the WP REST API.</h4>

<p align="center">
	<a href="https://github.com/EvanHerman/wp-rest-api-controller/actions/workflows/phpunit.yml?query=branch%3Amaster" target="_blank">
		<img src="https://github.com/EvanHerman/wp-rest-api-controller/actions/workflows/phpunit.yml/badge.svg?branch=master">
	</a>
	<a href="https://github.com/EvanHerman/wp-rest-api-controller/actions/workflows/wpcs.yml?query=branch%3Amaster" target="_blank">
		<img src="https://github.com/EvanHerman/wp-rest-api-controller/actions/workflows/wpcs.yml/badge.svg?branch=master">
	</a>
</p>

<p align="center">
	<a href="https://codeclimate.com/github/EvanHerman/wp-rest-api-controller/maintainability">
		<img src="https://api.codeclimate.com/v1/badges/31291cf5b446387d7cd4/maintainability" />
	</a>
	<a href="https://codeclimate.com/github/EvanHerman/wp-rest-api-controller/test_coverage">
		<img src="https://api.codeclimate.com/v1/badges/31291cf5b446387d7cd4/test_coverage" />
	</a>
</p>

<p align="center">
	<a href="https://wordpress.org/" target="_blank">
		<img src="https://img.shields.io/static/v1?label=&message=4.7+-+6.0&color=blue&style=flat-square&logo=wordpress&logoColor=white" alt="WordPress Versions">
	</a>
	<a href="https://www.php.net/" target="_blank">
		<img src="https://img.shields.io/static/v1?label=&message=5.6+-+8.0&color=777bb4&style=flat-square&logo=php&logoColor=white" alt="PHP Versions">
	</a>
</p>

<img src="https://ps.w.org/wp-rest-api-controller/assets/banner-772x250.png" width="100%" />

**Features:**
* Enable/Disable REST API endpoints for post types and taxonomies.
* Enable/Disable post type/taxonomy meta data (add or remove meta fields from API requests).
* Rename post type/taxonomy base endpoints.
* Rename post type/taxonomy meta data names in API requests so they are more user friendly.
* Manipulate and control post types/taxonomies and their data created by third party plugins and themes.
* Granular control of API responses without writing a single line of PHP code.
* Filters included to alter and extend default functionality.
* Localized and ready for translations.

<em>This plugin has reached maturity and has been released to the [WordPress.org repository](https://wordpress.org/support/plugin/wp-rest-api-controller). Enjoy!</em>

### Contributing

All pull requests welcome!

#### WP REST API Controller Settings:

![WP REST API Controller Settings Page](https://cldup.com/DVYcj6g3RO.png)

<strong>Important: As of December 6th, 2016 this plugin requires WordPress version 4.7, which has the WP REST API built into core. Prior versions of WordPress will no longer work with WP Rest API Controller version 1.3 or later. Please update your sites version of WordPress to 4.7 or later.</strong>

##### Filters

* `wp_rest_api_controller_rest_base`
* `wp_rest_api_controller_post_types`
* `wp_rest_api_controller_api_property_value`
* `wp_rest_api_controller_always_enabled_post_types`
* `wp_rest_api_controller_excluded_taxonomy_slugs`
* `wp_rest_api_controller_taxonomies`

_________________

<div align="center" style="font-weight: bold;">Originally built with <span style="color: #F3A4B2;">&hearts;</span> by YIKES Inc. in Philadelphia, PA.<br />Now Maintained by Evan Herman in Lancaster, PA.</div>
