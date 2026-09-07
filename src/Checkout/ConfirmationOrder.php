<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Checkout;

defined( 'ABSPATH' ) || exit;

final class ConfirmationOrder {
	public function resolve() {
		if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
			return null;
		}

		$order_id = absint( get_query_var( 'order-received' ) );
		$order_key = '';
		if ( isset( $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order_key = wc_clean( wp_unslash( $_GET['key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( ! $order_id && $order_key && function_exists( 'wc_get_order_id_by_order_key' ) ) {
			$order_id = absint( wc_get_order_id_by_order_key( $order_key ) );
		}

		$order = $order_id ? wc_get_order( $order_id ) : false;
		if ( ! $order ) {
			return null;
		}

		if ( $order_key && hash_equals( (string) $order->get_order_key(), (string) $order_key ) ) {
			return $order;
		}

		if ( is_user_logged_in() && (int) $order->get_user_id() === (int) get_current_user_id() ) {
			return $order;
		}

		return null;
	}

	public function delivery_country( \WC_Order $order ) {
		$country = strtoupper( (string) $order->get_shipping_country() );
		if ( '' === $country ) {
			$country = strtoupper( (string) $order->get_billing_country() );
		}
		return preg_match( '/^[A-Z]{2}$/', $country ) ? $country : '';
	}
}
