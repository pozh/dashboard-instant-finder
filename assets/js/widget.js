/**
 * Dashboard Instant Finder - WordPress Plugin
 * by WP Chef (http://thewpchef.com)
 */

( function( $ ) {

	$( document ).on( 'keyup', '#daif_tags', function(event) {
		if( $( "#daif_tags" ).val() == '' ) {
			$( "#daif_results" ).empty();
		}
	} );

	$.widget( "app.autocomplete", $.ui.autocomplete, { 
		options: {
			suggest: false 
		},
		_suggest: function( items ) {
			if ( $.isFunction( this.options.suggest ) ) {
				return this.options.suggest( items );
			}
			this._super( items );   
		}, 
	});
	
	$( function() {	
		$( "#daif_tags" ).autocomplete( {
			minLength: 1,
			source: function( request, response ) {
				var results = $.ui.autocomplete.filter( daif_data, request.term ); 
				if( results.length == 0 ) {
					$( "#daif_results" ).html( daif_consts.str_nothing_found );
				}
				response( results.slice( 0, 5 ) );
    		},
			suggest: function( items ) {
				var $container = $( "#daif_results" );
				$container.empty();
				$.each( items, function() {
					var str = '<li>' + this.label + ' - <a href="' + daif_consts.home_url + '?p=' + this.value + 
					          '">View</a>  - <a href="' + daif_consts.admin_url + 'post.php?post=' + this.value + '&amp;action=edit">Edit</a></li>';
					$container.append( str );
				});
			}
		});  
	});

})( jQuery );
