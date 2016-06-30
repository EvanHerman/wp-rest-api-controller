(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	jQuery( document ).ready( function() {
		// Check for tooltips, and initialize if they are present
		if ( jQuery( '.tipso' ).length > 0 ) {
			jQuery( '.tipso' ).each( function() {
				var title = jQuery( this ).attr( 'tipso-title' );
				jQuery( this ).tipso({
					speed             : 400,
					background        : '#222222',
					titleBackground   : 'tomato',
					color             : '#ffffff',
					titleColor        : '#ffffff',
					titleContent      : title
				});
			});
		}
		// Clicking a disabled link does nothing
		jQuery( 'body' ).on( 'click', '.endpoint-link[disabled="disabled"]', function() {
			alert( rest_api_controller_localized_admin_data.disabled_notice );
			return false;
		});
	});
})( jQuery );

/**
 * Toggle the end point link disabled attributes
 *
 * @param  mixed checkbox The HTML checkbox that was clicked (passed in using 'this')
 * @return null
 */
function toggleEndpointLink( checkbox ) {
	// checked state
	if ( jQuery( checkbox ).is( ':checked' ) ) {
		jQuery( checkbox ).parents( 'td' ).find( '.endpoint-link' ).removeAttr( 'disabled' );
	} else { // unchecked state
		jQuery( checkbox ).parents( 'td' ).find( '.endpoint-link' ).attr( 'disabled', 'disabled' );
	}
	// Toggle the visibility of the metadata fields
	jQuery( checkbox ).parents( 'td' ).find( '.post-type-meta-data' ).fadeToggle();
	jQuery( checkbox ).parents( 'td' ).find( '.rest-api-endpoint-container' ).fadeToggle();
}

/**
 * Toggle the visibility of the rest base input field and associated 'permalink'
 *
 * @param  mixed HTML element of the clicked button
 * @return null
 */
function toggleRestBaseVisbility( clicked_button ) {
	if ( jQuery( clicked_button ).hasClass( 'save-endpoint' ) ) {
		jQuery( '.edit-post-type-rest-base-active' ).fadeTo( 'fast', 0, function() {
			jQuery( this ).hide();
			jQuery( '.edit-post-type-rest-base-disabled' ).fadeTo( 'fast', 1 );
		});
	} else {
		jQuery( '.edit-post-type-rest-base-disabled' ).fadeTo( 'fast', 0, function() {
			jQuery( this ).hide();
			jQuery( '.edit-post-type-rest-base-active' ).fadeTo( 'fast', 1 );
		});
	}
}

/**
 * Populate the new reset base with the slug for this post type
 *
 * @param  mixed HTML value input for the new rest base
 * @return null
 * @since 1.0.0
 */
function toggleRestBaseInput( input_field ) {
	var new_text = jQuery( input_field ).val();
	var rest_base = jQuery( input_field ).data( 'rest-base' );
	// re-populate the permalink style link -- not working
	// jQuery( '.endpoint-link' ).attr( 'href', rest_base ).text( rest_base );
}
