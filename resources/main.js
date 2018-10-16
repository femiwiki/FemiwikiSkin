var _FW = {
	BBS_NS: [3902, 3904]
};

$( function () {
	function menuResize( divId ) {
		var containerWidth = parseFloat( $( '#' + divId ).css( 'width' ) )
			-parseFloat( $( '#' + divId ).css( 'padding-left' ) )
			-parseFloat( $( '#' + divId ).css( 'padding-right' ) ),
			itemPadding
				= parseFloat( $( '#' + divId+' > div' ).css( 'padding-left' ) )
				+ parseFloat( $( '#' + divId+' > div' ).css( 'padding-right' ) ),
			itemMargin
				= parseFloat( $( '#' + divId+' > div' ).css( 'margin-left' ) )
				+ parseFloat( $( '#' + divId+' > div' ).css( 'margin-right' ) ),
			itemActualMinWidth =
				parseFloat( $( '#' + divId+' > div' ).css( 'min-width' ) )
				+ itemPadding + itemMargin,
			itemLength = $( '#' + divId + ' > div' ).filter( function() {
					return $( this ).css( 'display' ) !== 'none';
			} ).length,
			horizontalCapacity = Math.min( Math.floor( containerWidth / itemActualMinWidth ), itemLength );

		$( '#' + divId + ' > div' ).css( "width", Math.floor( containerWidth/horizontalCapacity - itemPadding - itemMargin ) );
	}

	var searchInput = $( '#searchInput' ),
	 searchClearButton = $( '#searchClearButton' );
	searchInput.on( "input", function() {
		searchClearButton.toggle( !!this.value );
	} );
	searchClearButton.click( function () {
		searchInput.val( '' ).trigger( 'input' ).focus();
	} );

	$( '#fw-menu-toggle' ).click( function () {
		$( '#fw-menu' ).toggle();
		menuResize( 'fw-menu' );
		$( '#fw-menu-toggle .badge' )
			.removeClass( 'active' )
	} );
	$( '#p-menu-toggle > a' ).click( function ( e ) {
		e.preventDefault();
		$( '#p-actions-and-toolbox' ).toggle();
		menuResize( 'p-actions-and-toolbox' );
	} );
	$( window ).resize( function() {
		menuResize( 'fw-menu' );
		menuResize( 'p-actions-and-toolbox' );
	} );

	// Notification badge
	var alerts = +$( '#pt-notifications-alert a' ).attr( 'data-counter-num' );
	var notice = +$( '#pt-notifications-notice a' ).attr( 'data-counter-num' );
	var badge = alerts + notice;
	if ( !isNaN( badge ) && badge !== 0 ) {
		$( '#fw-menu-toggle .badge' )
			.addClass( 'active' )
			.text( badge > 10 ? '+9' : badge )
	}

	// Set Mathjax linebreaks configuration
	if( typeof MathJax !== 'undefined' ) {
		MathJax.Hub.Config( {
			CommonHTML: { linebreaks: { automatic: true } },
			"HTML-CSS": { linebreaks: { automatic: true } },
						 SVG: { linebreaks: { automatic: true } }
		} );

		// Center single Mathjax line
		MathJax.Hub.Queue( function () {
			$( '#content p > span:only-child > span.MathJax, '
			+'#content p > span.mathjax-wrapper:only-child > div' ).each( function(){
				if( !$( this ).parent().parent().clone().children().remove().end().text().trim().length ) {
					$( this ).parent().css( 'display', 'block' );
					$( this ).parent().css( 'text-align', 'center' );
				}
			} );
		} );
	}
} );
