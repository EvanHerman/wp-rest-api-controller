(function( $ ) {
	'use strict';

	jQuery( document ).ready( function() {

		checkAllToggle();

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

		jQuery( 'body' ).on( 'click', '.rest-controller-tabs-list-item > a', function() {
			var tab = jQuery( this ).data( 'tab' );
			
			if ( typeof tab === 'undefined' ) {
				return;
			}

			jQuery( '#rest-controller-active-tab' ).val( tab );

			toggle_section_visibility( tab, this );
		});
		jQuery( '.rest-controller-tabs-list-item > a[data-tab="' + jQuery( '#rest-controller-active-tab' ).val() + '"]' ).click();
	});

	function toggle_section_visibility( tab, element ) {
		jQuery( '.rest-controller-tabs-list-item' ).removeClass( 'active' );
		jQuery( element ).parent( '.rest-controller-tabs-list-item' ).addClass( 'active' );
		jQuery( '.rest-api-controller-section' ).hide().parents( 'tr' ).hide();
		jQuery( '.rest-api-controller-' + tab ).show().parents( 'tr' ).show();
	}

	function checkAllToggle() {
		jQuery( '.object-meta-data' ).each( function() {
			var allChecked = true;

			jQuery( this ).find( '.meta-switch-input' ).each( function() {
				if ( jQuery( this ).prop( 'checked' ) === false ) {
					allChecked = false;
				}
			});

			if ( allChecked ) {
				jQuery( this ).find( '.all-meta-switch-input' ).prop( 'checked', true );
			}
		});
	}

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
	jQuery( checkbox ).parents( 'td' ).find( '.object-meta-data' ).fadeToggle();
	jQuery( checkbox ).parents( 'td' ).find( '.rest-api-endpoint-container' ).fadeToggle();
}

/**
 * Toggle the visibility of the rest base input field and associated 'permalink'
 *
 * @param  mixed HTML element of the clicked button
 * @return null
 */
function toggleRestBaseVisbility( clicked_button, event ) {
	var clicked_button_obj = jQuery( clicked_button );
	var parent_container   = clicked_button_obj.parents( 'td' );
	if ( clicked_button_obj.hasClass( 'save-endpoint' ) ) {
		var endpoint  = clicked_button_obj.siblings( '.inline-input' ).val();
		var rest_base = clicked_button_obj.siblings( '.inline-input' ).data( 'rest-base' );
		var href      = rest_base + endpoint;
		parent_container.find( '.edit-post-type-rest-base-active' ).fadeTo( 'fast', 0, function() {
			jQuery( this ).hide();
			parent_container.find( '.edit-post-type-rest-base-disabled' ).fadeTo( 'fast', 1 );
			parent_container.find( '.endpoint-link' ).attr( 'href', href ).text( href );
		});
	} else {
		parent_container.find( '.edit-post-type-rest-base-disabled' ).fadeTo( 'fast', 0, function() {
			jQuery( this ).hide();
			parent_container.find( '.edit-post-type-rest-base-active' ).fadeTo( 'fast', 1 );
		});
	}
	event.preventDefault();
}
