/**
 * Product Countdown for WooCommerce - Simple Countdown JS
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

jQuery( document ).ready( function() {
	jQuery( '#alg_product_countdown' ).each( function() {
		if ( '' != alg_data_countdown.time_left ) {
			var update_ms = parseInt( alg_data_countdown.update_rate_ms );
			jQuery('#alg_product_countdown').html( alg_data_countdown.time_left );
			update();

			function update() {
				var data = {
					'action': 'alg_product_countdown',
					'product_id': alg_data_countdown.product_id,
				};
				jQuery.post(alg_data_countdown.ajax_url, data, function(response) {
					if ( '' != response ) {
						jQuery('#alg_product_countdown').html(response);
					} else {
						location.reload();
					}
				});
			}

			setInterval( function() {
				update();
			}, update_ms );
		}
	});
});