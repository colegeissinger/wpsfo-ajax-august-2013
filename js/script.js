jQuery(document).ready(function($) {

	// Listen for a click event on our text input submit button
	$( '.wcsf-submit-field' ).click( function( e ) {

		// Stop the button from submitting and refreshing the page.
		e.preventDefault();

		// Now that a click has happened, let's run Ajax!!!!!!!!!
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: wcsf_ajax.ajaxurl,
			data: {
				'action' : 'wcsf_ajax',
				'data' : $( '.wcsf-text-field' ).val(),
				'submission' : $( '.wcsf-submitted' ).val(),
				'nonce' : $( '#wcsf-nonce' ).val(),
			},
			complete: function( object ) {
				$( '.entry-content' ).text( object.responseJSON.body );
			}
		});
	});
});