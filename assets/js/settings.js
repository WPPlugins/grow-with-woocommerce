(function( $ ) {
	$('#woocommerce_woocommerce_grow_ga_authentication').click( function(){
		var url = $(this).data('redirect-url');
		wc_grow_open_new_window( url );

		// TODO: May be add handling for the Access token field to be highlighted

		return false;
	});

	function wc_grow_open_new_window( url ) {
		// TODO: dimensions, window
		var width = '500';
		var height = '500';
		var left = (screen.width/2)-(width/2);
		var top = (screen.height/8);

		window.open(url, 'Allow Google Account Access', 'location=0, status=0, menubar=0, scrollbars=0, resizable=1, copyhistory=0, width='+width+', height='+height+', top='+top+', left='+left);
	}
})( jQuery );
