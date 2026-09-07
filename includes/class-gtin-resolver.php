<?php
namespace MRCWC;

defined( 'ABSPATH' ) || exit;

final class GTIN_Resolver {
	public static function for_order( \WC_Order $order, array $settings ): array {
		$gtins = array();

		foreach ( $order->get_items( 'line_item' ) as $item ) {
			if ( ! is_a( $item, 'WC_Order_Item_Product' ) ) {
				continue;
			}

			$product = $item->get_product();
			if ( ! $product ) {
				continue;
			}

			$gtin = self::for_product( $product, $item, $settings );
			if ( '' !== $gtin ) {
				$gtins[ $gtin ] = array( 'gtin' => $gtin );
			}
		}

		return array_values( $gtins );
	}

	public static function for_product( \WC_Product $product, \WC_Order_Item_Product $item, array $settings ): string {
		$candidates = self::candidate_values( $product, $settings );

		if ( $product->is_type( 'variation' ) ) {
			$parent = wc_get_product( $product->get_parent_id() );
			if ( $parent ) {
				$candidates = array_merge( $candidates, self::candidate_values( $parent, $settings ) );
			}
		}

		$resolved = '';
		foreach ( $candidates as $candidate ) {
			$normalized = self::normalize( $candidate );
			if ( self::is_valid( $normalized ) ) {
				$resolved = $normalized;
				break;
			}
		}

		$resolved = (string) apply_filters( 'mrcwc_product_gtin', $resolved, $product, $item );
		$resolved = self::normalize( $resolved );

		return self::is_valid( $resolved ) ? $resolved : '';
	}

	private static function candidate_values( \WC_Product $product, array $settings ): array {
		$values = array();

		if ( method_exists( $product, 'get_global_unique_id' ) ) {
			$values[] = $product->get_global_unique_id();
		}

		$keys = array( '_global_unique_id', 'global_unique_id', '_gtin', 'gtin', '_ean', 'ean' );
		if ( ! empty( $settings['custom_gtin_meta_keys'] ) ) {
			$custom = preg_split( '/[,\r\n]+/', (string) $settings['custom_gtin_meta_keys'] );
			foreach ( (array) $custom as $key ) {
				$key = sanitize_key( trim( $key ) );
				if ( '' !== $key ) {
					$keys[] = $key;
				}
			}
		}

		$keys = array_values( array_unique( (array) apply_filters( 'mrcwc_gtin_meta_keys', $keys, $product ) ) );
		foreach ( $keys as $key ) {
			$value = $product->get_meta( $key, true );
			if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
				$values[] = (string) $value;
			}
		}

		return $values;
	}

	private static function normalize( $value ): string {
		if ( ! is_scalar( $value ) ) {
			return '';
		}
		return preg_replace( '/\D+/', '', (string) $value ) ?: '';
	}

	private static function is_valid( string $gtin ): bool {
		$length = strlen( $gtin );
		if ( ! in_array( $length, array( 8, 12, 13, 14 ), true ) || ! ctype_digit( $gtin ) ) {
			return false;
		}

		$body       = substr( $gtin, 0, -1 );
		$check      = (int) substr( $gtin, -1 );
		$sum        = 0;
		$multiplier = 3;

		for ( $i = strlen( $body ) - 1; $i >= 0; --$i ) {
			$sum       += (int) $body[ $i ] * $multiplier;
			$multiplier = 3 === $multiplier ? 1 : 3;
		}

		$expected = ( 10 - ( $sum % 10 ) ) % 10;
		return $check === $expected;
	}
}
