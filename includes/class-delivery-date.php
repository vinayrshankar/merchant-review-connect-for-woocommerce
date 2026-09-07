<?php
namespace MRCWC;

defined( 'ABSPATH' ) || exit;

final class Delivery_Date {
	public static function for_order( \WC_Order $order, array $settings ): string {
		$meta_key = isset( $settings['delivery_date_meta_key'] ) ? sanitize_key( $settings['delivery_date_meta_key'] ) : '';
		if ( '' !== $meta_key ) {
			$meta_value = $order->get_meta( $meta_key, true );
			$meta_date  = self::parse_date( $meta_value );
			if ( $meta_date ) {
				return (string) apply_filters( 'mrcwc_estimated_delivery_date', $meta_date, $order, $settings );
			}
		}

		$created = $order->get_date_created();
		$zone    = function_exists( 'wp_timezone' ) ? wp_timezone() : new \DateTimeZone( 'UTC' );

		if ( $created ) {
			$date = new \DateTimeImmutable( '@' . $created->getTimestamp() );
			$date = $date->setTimezone( $zone );
		} else {
			$date = new \DateTimeImmutable( 'now', $zone );
		}

		$days = isset( $settings['delivery_days'] ) ? max( 0, absint( $settings['delivery_days'] ) ) : 7;
		$mode = isset( $settings['delivery_mode'] ) && 'business' === $settings['delivery_mode'] ? 'business' : 'calendar';

		if ( 'business' === $mode ) {
			$date = self::add_business_days( $date, $days );
		} elseif ( $days > 0 ) {
			$date = $date->modify( '+' . $days . ' days' );
		}

		$result = $date->format( 'Y-m-d' );
		return (string) apply_filters( 'mrcwc_estimated_delivery_date', $result, $order, $settings );
	}

	private static function add_business_days( \DateTimeImmutable $date, int $days ): \DateTimeImmutable {
		$added = 0;
		while ( $added < $days ) {
			$date = $date->modify( '+1 day' );
			$weekday = (int) $date->format( 'N' );
			if ( $weekday <= 5 ) {
				++$added;
			}
		}
		return $date;
	}

	private static function parse_date( $value ): string {
		if ( ! is_scalar( $value ) || '' === trim( (string) $value ) ) {
			return '';
		}

		$value = trim( (string) $value );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			$parts = array_map( 'intval', explode( '-', $value ) );
			if ( checkdate( $parts[1], $parts[2], $parts[0] ) ) {
				return $value;
			}
		}

		$zone = function_exists( 'wp_timezone' ) ? wp_timezone() : new \DateTimeZone( 'UTC' );
		try {
			$date = new \DateTimeImmutable( $value, $zone );
			return $date->format( 'Y-m-d' );
		} catch ( \Exception $e ) {
			return '';
		}
	}
}
