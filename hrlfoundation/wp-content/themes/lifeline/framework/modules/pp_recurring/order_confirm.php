<?php

/* ==================================================================
  PayPal Express Checkout Call
  ===================================================================
 */
require_once ("paypalfunctions.php");
$r = session_id();
$temp = get_option( 'temp_donation_recuring' . $r );
$type = sh_set( $temp, 'type' );
$id = sh_set( $temp, 'id' );
if ( session_id() == '' )
	session_start();
$sh_express = new SH_Express_checkout;
$PaymentOption = "PayPal";
if ( $PaymentOption == "PayPal" ) {
	/*
	  '------------------------------------
	  ' The paymentAmount is the total value of
	  ' the shopping cart, that was set
	  ' earlier in a session variable
	  ' by the shopping cart page
	  '------------------------------------
	 */

	$finalPaymentAmount = sh_set( $_SESSION, "Payment_Amount" );

	/*
	  '------------------------------------
	  ' Calls the DoExpressCheckoutPayment API call
	  '
	  ' The ConfirmPayment function is defined in the file PayPalFunctions.jsp,
	  ' that is included at the top of this file.
	  '-------------------------------------------------
	 */

	//$resArray = ConfirmPayment ( $finalPaymentAmount ); Remove comment with ontime payment.

	$resArray = $sh_express->CreateRecurringPaymentsProfile();



	$ack = strtoupper( sh_set( $resArray, "ACK" ) );

	if ( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" ) {
		$resArray2 = $sh_express->ConfirmPayment( $finalPaymentAmount );
		$ack = strtoupper( sh_set( $resArray, "ACK" ) );
		$resArray = array_merge( $resArray, $resArray2 );
	}


	if ( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" ) {

		$transactionId = sh_set( $resArray, "TRANSACTIONID" ); // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs. 
		$transactionType = sh_set( $resArray, "TRANSACTIONTYPE" ); //' The type of transaction Possible values: l  cart l  express-checkout 
		$paymentType = sh_set( $resArray, "PAYMENTTYPE" );  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant 
		$orderTime = sh_set( $resArray, "ORDERTIME" );  //' Time/date stamp of payment
		$amt = sh_set( $resArray, "AMT" );  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
		$currencyCode = sh_set( $resArray, "CURRENCYCODE" );  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD. 
		$feeAmt = sh_set( $resArray, "FEEAMT" );  //' PayPal fee amount charged for the transaction
		$settleAmt = sh_set( $resArray, "SETTLEAMT" );  //' Amount deposited in your PayPal account after a currency conversion.
		$taxAmt = sh_set( $resArray, "TAXAMT" );  //' Tax charged on the transaction.
		$exchangeRate = sh_set( $resArray, "EXCHANGERATE" );  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.
		$paymentStatus = sh_set( $resArray, "PAYMENTSTATUS" );
		$pendingReason = sh_set( $resArray, "PENDINGREASON" );
		$reasonCode = sh_set( $resArray, "REASONCODE" );
		$theme_options = get_option( SH_NAME );

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
				'donner_name' => sh_set( $_SESSION, 'donor_name' ),
				'donner_email' => sh_set( $_SESSION, 'donor_email' ),
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
				'payer_id' => sh_set( $_SESSION, 'payer_id' ),
				'ship_to_name' => sh_set( $_SESSION, 'shipToName' ),
				'donation_type' => 'Multiple',
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
				'donner_name' => sh_set( $_SESSION, 'donor_name' ),
				'donner_email' => sh_set( $_SESSION, 'donor_email' ),
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
				'payer_id' => sh_set( $_SESSION, 'payer_id' ),
				'ship_to_name' => sh_set( $_SESSION, 'shipToName' ),
				'donation_type' => 'Multiple',
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
				'donner_name' => sh_set( $_SESSION, 'donor_name' ),
				'donner_email' => sh_set( $_SESSION, 'donor_email' ),
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
				'payer_id' => sh_set( $_SESSION, 'payer_id' ),
				'ship_to_name' => sh_set( $_SESSION, 'shipToName' ),
				'donation_type' => 'Multiple',
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
		echo "<div class='confirm_popup'>Thank you for your payment.</div>";
		delete_option( 'temp_donation_recuring' . $r );
		if ( isset( $_SESSION ) ) {
			session_destroy();
		}
	} else {
		$ErrorCode = urldecode( sh_set( $resArray, "L_ERRORCODE0" ) );
		$ErrorShortMsg = urldecode( sh_set( $resArray, "L_SHORTMESSAGE0" ) );
		$ErrorLongMsg = urldecode( sh_set( $resArray, "L_LONGMESSAGE0" ) );
		$ErrorSeverityCode = urldecode( sh_set( $resArray, "L_SEVERITYCODE0" ) );

		echo "GetExpressCheckoutDetails API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}
