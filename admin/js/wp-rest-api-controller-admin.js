/* global rest_api_controller_localized_admin_data */
( function( $ ) {
	'use strict';

	$( document ).ready( function() {

		checkAllToggle();

		$( '#clear-wp-rest-api-controller-cache' ).addClass( 'tipso' );

		// Check for tooltips, and initialize if they are present
		if ( $( '.tipso' ).length > 0 ) {
			$( '.tipso' ).each( function() {
				var title = $( this ).data( 'tipso-title' );
				$( this ).tipso( {
					speed: 400,
					background: '#222222',
					titleBackground: 'tomato',
					color: '#ffffff',
					titleColor: '#ffffff',
					titleContent: title
				} );
			} );
		}

		// Clicking a disabled link does nothing
		$( 'body' ).on( 'click', '.endpoint-link[disabled="disabled"]', function() {
			alert( rest_api_controller_localized_admin_data.disabled_notice );
			return false;
		} );

		$( 'body' ).on( 'click', '.rest-controller-tabs-list-item > a', function() {
			var tab = $( this ).data( 'tab' );

			if ( typeof tab === 'undefined' ) {
				return;
			}

			$( '#rest-controller-active-tab' ).val( tab );

			toggle_section_visibility( tab, this );
		} );
		$( '.rest-controller-tabs-list-item > a[data-tab="' + $( '#rest-controller-active-tab' ).val() + '"]' ).click();
	} );

	function toggle_section_visibility( tab, element ) {
		$( '.rest-controller-tabs-list-item' ).removeClass( 'active' );
		$( element ).parent( '.rest-controller-tabs-list-item' ).addClass( 'active' );
		$( '.rest-api-controller-section' ).hide().parents( 'tr' ).hide();
		$( '.rest-api-controller-' + tab ).show().parents( 'tr' ).show();
	}

	function checkAllToggle() {
		$( '.object-meta-data' ).each( function() {
			var allChecked = true;

			$( this ).find( '.meta-switch-input' ).each( function() {
				if ( $( this ).prop( 'checked' ) === false ) {
					allChecked = false;
				}
			});

			if ( allChecked ) {
				$( this ).find( '.all-meta-switch-input' ).prop( 'checked', true );
			}
		} );
	}

} )( jQuery );

/**
 * Toggle the end point link disabled attributes
 *
 * @param mixed checkbox The HTML checkbox that was clicked (passed in using 'this')
 */
/* eslint-disable */
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
 * @param mixed HTML element of the clicked button
 */
/* eslint-disable */
function toggleRestBaseVisbility( clicked_button, event ) {
	var clicked_button_obj = jQuery( clicked_button );
	var parent_container   = clicked_button_obj.parents( 'td' );

	if ( clicked_button_obj.hasClass( 'save-endpoint' ) ) {
		var endpoint  = clicked_button_obj.siblings( '.inline-input' ).val().toLowerCase();
		var rest_base = clicked_button_obj.siblings( '.inline-input' ).data( 'rest-base' );
		var href      = rest_base + endpoint;
		parent_container.find( '.edit-post-type-rest-base-active' ).fadeTo( 'fast', 0, function() {
			jQuery( this ).hide();
			parent_container.find( '.edit-post-type-rest-base-disabled' ).fadeTo( 'fast', 1 );
			parent_container.find( '.endpoint-link' ).attr( 'href', href ).text( href );
		} );
	} else {
		parent_container.find( '.edit-post-type-rest-base-disabled' ).fadeTo( 'fast', 0, function() {
			jQuery( this ).hide();
			parent_container.find( '.edit-post-type-rest-base-active' ).fadeTo( 'fast', 1 );
		} );
	}
	event.preventDefault();
}
