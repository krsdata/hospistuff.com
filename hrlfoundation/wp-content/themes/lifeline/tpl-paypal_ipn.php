<?php
// Template Name: PayPal IPN

sh_custom_header();
$theme_options = get_option( SH_NAME );
$r = session_id();
$temp = get_option( 'temp_donation_recuring' . $r );
$type = sh_set( $temp, 'type' );
$id = sh_set( $temp, 'id' );
$redirect = sh_set( $temp, 'redirect' );
$donor_opt = get_option( 'temp_donor_info' . $r );
$don_name = sh_set( $donor_opt, 'don_name' );
$don_email = sh_set( $donor_opt, 'don_email' );
if ( isset( $_POST ) ) {
	$transactionId = sh_set( $_POST, "txn_id" ); // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs. 
	$transactionType = sh_set( $_POST, "txn_type" ); //' The type of transaction Possible values: l  cart l  express-checkout 
	$paymentType = sh_set( $_POST, "payment_type" );  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant 
	$orderTime = sh_set( $_POST, "payment_date" );  //' Time/date stamp of payment
	$amt = sh_set( $_POST, "mc_gross" );  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
	$currencyCode = sh_set( $_POST, "mc_currency" );  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD. 
	$feeAmt = sh_set( $_POST, "" );  //' PayPal fee amount charged for the transaction
	$settleAmt = sh_set( $_POST, "" );  //' Amount deposited in your PayPal account after a currency conversion.
	$taxAmt = sh_set( $_POST, "tax" );  //' Tax charged on the transaction.
	$exchangeRate = sh_set( $_POST, "" );  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.
	$paymentStatus = sh_set( $_POST, "payment_status" );
	$pendingReason = sh_set( $_POST, "" );
	$reasonCode = sh_set( $_POST, "" );
	$amount = sh_set( $_POST, "mc_gross" );
	$pair_id = sh_set( $_POST, 'item_name' ) . ' ' . sh_set( $_POST, 'last_name' ) . ' ' . sh_set( $_POST, 'payer_id' );
	if ( !array_key_exists( 'donation_collected', $theme_options ) ) {
		$theme_options['donation_collected'] = 0;
	}
	$target_amount = (sh_set( $theme_options, 'donation_collected' ) >= 0) ? (int) sh_set( $theme_options, 'donation_collected' ) + $amt : 10;
	if ( (int) $target_amount > 0 ) {
		$theme_options['donation_collected'] = $target_amount;
		$theme_options['paypal_raised'] = $theme_options['donation_collected'];
	}
	update_option( SH_NAME, $theme_options );

	if ( isset( $temp ) && !empty( $temp ) && $type == 'post' ) {
		$cause_donation = array();
		$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
		array_push(
				$cause_donation, array(
			'donner_name' => $don_name,
			'donner_email' => $don_email,
			'transaction_id' => $transactionId,
			'transaction_type' => $transactionType,
			'payment_type' => $paymentType,
			'order_time' => $orderTime,
			'amount' => $amt,
			'currency_code' => $currencyCode,
			'fee_amount' => $feeAmt,
			'settle_amount' => $settleAmt,
			'tax_amount' => $taxAmt,
			'exchange_rate' => $exchangeRate,
			'payment_status' => $paymentStatus,
			'pending_reason' => $pendingReason,
			'reason_code' => $reasonCode,
			'payer_id' => $pair_id,
			'ship_to_name' => sh_set( $_SESSION, 'shipToName' ),
			'donation_type' => 'Single',
				)
		);
		$get_old = get_post_meta( $id, '_dict_causes_settings', true );
		$c_collect_ = (sh_set( $get_old, 'donation_collected' )) ? sh_set( $get_old, 'donation_collected' ) : 0;
		$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amt;
		foreach ( $get_old as $k => $o ) {
			if ( $k == 'donation_collected' ) {
				$get_old['donation_collected'] = number_format( $updated );
			}
		}
		update_post_meta( $id, '_dict_causes_settings', $get_old );
		update_post_meta( $id, 'single_causes_donation', $cause_donation );
	} elseif ( isset( $temp ) && !empty( $temp ) && $type == 'project' ) {
		$cause_donation = array();
		$cause_donation = (get_post_meta( $id, 'single_causes_donation', true )) ? get_post_meta( $id, 'single_causes_donation', true ) : array();
		array_push(
				$cause_donation, array(
			'donner_name' => $don_name,
			'donner_email' => $don_email,
			'transaction_id' => $transactionId,
			'transaction_type' => $transactionType,
			'payment_type' => $paymentType,
			'order_time' => $orderTime,
			'amount' => $amt,
			'currency_code' => $currencyCode,
			'fee_amount' => $feeAmt,
			'settle_amount' => $settleAmt,
			'tax_amount' => $taxAmt,
			'exchange_rate' => $exchangeRate,
			'payment_status' => $paymentStatus,
			'pending_reason' => $pendingReason,
			'reason_code' => $reasonCode,
			'payer_id' => $pair_id,
			'ship_to_name' => sh_set( $_SESSION, 'shipToName' ),
			'donation_type' => 'Single',
				)
		);
		$get_old = get_post_meta( $id, '_dict_project_settings', true );
		$c_collect_ = (sh_set( $get_old, 'amount_needed' )) ? sh_set( $get_old, 'amount_needed' ) : 0;
		$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amt;
		foreach ( $get_old as $k => $o ) {
			if ( $k == 'amount_needed' ) {
				$get_old['amount_needed'] = number_format( $updated );
			}
		}
		update_post_meta( $id, '_dict_project_settings', $get_old );
		update_post_meta( $id, 'single_causes_donation', $cause_donation );
	} else {
		$cause_donation = array();
		$cause_donation = (get_option( 'general_donation' )) ? get_option( 'general_donation' ) : array();
		array_push(
				$cause_donation, array(
			'donner_name' => $don_name,
			'donner_email' => $don_email,
			'transaction_id' => $transactionId,
			'transaction_type' => $transactionType,
			'payment_type' => $paymentType,
			'order_time' => $orderTime,
			'amount' => $amt,
			'currency_code' => $currencyCode,
			'fee_amount' => $feeAmt,
			'settle_amount' => $settleAmt,
			'tax_amount' => $taxAmt,
			'exchange_rate' => $exchangeRate,
			'payment_status' => $paymentStatus,
			'pending_reason' => $pendingReason,
			'reason_code' => $reasonCode,
			'payer_id' => $pair_id,
			'ship_to_name' => sh_set( $_SESSION, 'shipToName' ),
			'donation_type' => 'Single',
				)
		);

		$donation_data = get_option( SH_NAME );
		$c_collect_ = (sh_set( $donation_data, 'paypal_raised' )) ? sh_set( $donation_data, 'paypal_raised' ) : 0;
		$updated = (int) str_replace( ',', '', $c_collect_ ) + (int) $amount;
		foreach ( $donation_data as $k => $o ) {
			if ( $k == 'paypal_raised' ) {
				$donation_data['paypal_raised'] = number_format( $updated );
			}
		}
		update_option( SH_NAME, $donation_data );
		update_option( 'general_donation', $cause_donation );
	}
	delete_option( 'temp_donation_recuring' . $r );
	delete_option( 'temp_donor_info' . $r );
	?>
	<div class="paypal_ipn">
		<div class="container">
			<div class='success_msg'>
				<?php _e( 'Thank you for your payment. You will be redirect in 10 Secounds', SH_NAME ); ?>
			</div>
		</div>
	</div>

	<script>
	<?php if ( $redirect ): ?>
		    setTimeout(function () {
		        window.location.href = "<?php echo $redirect ?>"
		    }, 3000);
	<?php endif; ?>
	</script>
	<?php
	if ( isset( $_SESSION ) ) {
		session_destroy();
	}
}
get_footer();
