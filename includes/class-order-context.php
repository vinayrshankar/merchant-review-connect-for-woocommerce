<?php
namespace MRCWC;

defined( 'ABSPATH' ) || exit;

final class Order_Context {
	public static function current_order() {
		if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
			return null;
		}

		$order_id = absint( get_query_var( 'order-received' ) );
		$key      = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! $order_id && '' !== $key && function_exists( 'wc_get_order_id_by_order_key' ) ) {
			$order_id = absint( wc_get_order_id_by_order_key( $key ) );
		}

		if ( ! $order_id ) {
			return null;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return null;
		}

		if ( '' !== $key && hash_equals( (string) $order->get_order_key(), (string) $key ) ) {
			return $order;
		}

		if ( is_user_logged_in() && (int) $order->get_user_id() === get_current_user_id() ) {
			return $order;
		}

		return null;
	}
}
