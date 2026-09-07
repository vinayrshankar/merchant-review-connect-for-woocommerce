<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Delivery;

use VinayShankar\MerchantReviewConnect\Config\Configuration;

defined( 'ABSPATH' ) || exit;

final class Estimator {
	public function estimate( \WC_Order $order, Configuration $config ) {
		$stored = $this->date_from_order_meta( $order, $config->eta_meta_key() );
		if ( $stored ) {
			return (string) apply_filters( 'mrc_delivery_date', $stored, $order, $config );
		}

		$date = $this->order_day( $order );
		$days = $config->eta_days();

		if ( 'weekdays' === $config->eta_strategy() ) {
			$date = $this->advance_weekdays( $date, $days );
		} elseif ( $days > 0 ) {
			$date = $date->modify( sprintf( '+%d days', $days ) );
		}

		$value = $date->format( 'Y-m-d' );
		return (string) apply_filters( 'mrc_delivery_date', $value, $order, $config );
	}

	private function order_day( \WC_Order $order ) {
		$zone    = function_exists( 'wp_timezone' ) ? wp_timezone() : new \DateTimeZone( 'UTC' );
		$created = $order->get_date_created();
		if ( ! $created ) {
			return new \DateTimeImmutable( 'today', $zone );
		}

		$date = new \DateTimeImmutable( '@' . $created->getTimestamp() );
		return $date->setTimezone( $zone )->setTime( 0, 0, 0 );
	}

	private function advance_weekdays( \DateTimeImmutable $date, $days ) {
		$remaining = max( 0, (int) $days );
		while ( $remaining > 0 ) {
			$date = $date->modify( '+1 day' );
			if ( (int) $date->format( 'N' ) < 6 ) {
				--$remaining;
			}
		}
		return $date;
	}

	private function date_from_order_meta( \WC_Order $order, $meta_key ) {
		if ( '' === $meta_key ) {
			return '';
		}

		$value = $order->get_meta( $meta_key, true );
		if ( ! is_scalar( $value ) || '' === trim( (string) $value ) ) {
			return '';
		}

		$zone = function_exists( 'wp_timezone' ) ? wp_timezone() : new \DateTimeZone( 'UTC' );
		try {
			$date = new \DateTimeImmutable( trim( (string) $value ), $zone );
			return $date->format( 'Y-m-d' );
		} catch ( \Exception $exception ) {
			return '';
		}
	}
}
