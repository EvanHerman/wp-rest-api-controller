## REST API Exposed by YIKES, Inc., Evan Herman

Control post type exposure to the REST API. Say goodbye to manually enabling/disabling rest API endpoints by code, now you can use our dashboard interface to control exposure of your post types to the WP REST API.

<strong>Settings Page (found under Tools):</strong>

![WP REST API Controller Settings Page](https://cldup.com/FLb1L15r69.png)

<strong>Important: This plugin requires the [WP REST API (Version 2)](https://wordpress.org/plugins/rest-api/) to be installed. Previous versions will not work properly as the API endpoints differ.</strong>

<em>Please Note: This plugin is a work in progress, and may break backwards compatibility at any point, until we reach a stable release candidate.</em>

##### TwentySixteen Child Theme

During development, there is a directory bundled with this plugin `TwentySixteen-Child`, which is a child theme of [TwentySixteen](https://wordpress.org/themes/twentysixteen/) WordPress theme, and is used for testing purposes. It has a number of custom post types, and post meta registered which is used for testing the API endpoints.

The child theme is not required for this plugin to function properly, and can (<em>and should be</em>) deleted before installing and activating the plugin.

##### Filters

`rest_api_exposed_rest_base`
