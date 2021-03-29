jQuery( document ).ready( function ( $ ) {
	const FORM = $( '#products-export-form' );
	var chunkSize = 100;

	FORM.on( 'submit', function ( e ) {
		e.preventDefault();
		var formData = $( this ).serialize();

		// Give user some visuall feedback.
		var initialText = $( '<p></p>' ).text(
			'Your request is processing. Download will start soon.'
		);
		$( this ).after( initialText );
		// Prevent click stream.
		$( '#submit', this ).prop( 'disabled', true );

		downloadChunk( 1, chunkSize, formData );
	} );

	function downloadChunk( page, chunk, urlEncodedData ) {
		$.post(
			simplePluginExport.ajaxUrl,
			appendDataToForm( urlEncodedData, {
				limit: chunk,
				page: page
			} ),
			function ( response ) {

				if ( !response.success ) {
					$( '.wrap > p:last-of-type' ).remove();
					$( '#submit', FORM ).prop( 'disabled', false );
					$( FORM ).after( $(	'<div id="error-holder"></div>' ) );
					response.data.forEach( error => {
						$( '<p></p>' ).text( error ).appendTo( $( '#error-holder' ) );
					} )
					return false;
				}

				if ( response.data.current < response.data.max ) {
					// Point request to save results into the same file.
					urlEncodedData = appendDataToForm(
						urlEncodedData, {
							file: response.data.file
						} )
					// Export next chunk page if not reached to the end.
					return downloadChunk( ++page, chunk, urlEncodedData );
				}

				$( '.wrap > p:last-of-type' ).remove();
				$( '#submit', FORM ).prop( 'disabled', false );
				window.location.href = response.data.download;
				return true;
			}
		)
	}

	function appendDataToForm( urlEncodedString, dataObject ) {
		let newEncodedString = urlEncodedString;
		for ( const key in dataObject ) {
			newEncodedString += `&${key}=${dataObject[key]}`;
		}
		return newEncodedString;
	}
} )
