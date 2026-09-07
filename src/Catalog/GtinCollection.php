<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Catalog;

use VinayShankar\MerchantReviewConnect\Config\Configuration;

defined( 'ABSPATH' ) || exit;

final class GtinCollection {
	public function from_order( \WC_Order $order, Configuration $config ) {
		$unique = array();

		foreach ( $order->get_items( 'line_item' ) as $item ) {
			if ( ! $item instanceof \WC_Order_Item_Product ) {
				continue;
			}
			$product = $item->get_product();
			if ( ! $product ) {
				continue;
			}

			$gtin = $this->first_valid_identifier( $product, $config );
			$gtin = (string) apply_filters( 'mrc_product_gtin', $gtin, $product, $item );
			$gtin = $this->digits_only( $gtin );
			if ( $this->passes_check_digit( $gtin ) ) {
				$unique[ $gtin ] = true;
			}
		}

		$output = array();
		foreach ( array_keys( $unique ) as $gtin ) {
			$output[] = array( 'gtin' => $gtin );
		}
		return $output;
	}

	private function first_valid_identifier( \WC_Product $product, Configuration $config ) {
		foreach ( $this->candidate_products( $product ) as $candidate_product ) {
			foreach ( $this->candidate_values( $candidate_product, $config ) as $candidate ) {
				$normalized = $this->digits_only( $candidate );
				if ( $this->passes_check_digit( $normalized ) ) {
					return $normalized;
				}
			}
		}
		return '';
	}

	private function candidate_products( \WC_Product $product ) {
		$products = array( $product );
		if ( $product->is_type( 'variation' ) ) {
			$parent = wc_get_product( $product->get_parent_id() );
			if ( $parent ) {
				$products[] = $parent;
			}
		}
		return $products;
	}

	private function candidate_values( \WC_Product $product, Configuration $config ) {
		$values = array();
		if ( method_exists( $product, 'get_global_unique_id' ) ) {
			$values[] = $product->get_global_unique_id();
		}

		$keys = array( '_global_unique_id', 'global_unique_id', '_gtin', 'gtin', '_ean', 'ean' );
		$keys = array_merge( $keys, $config->extra_product_meta_keys() );
		$keys = array_values( array_unique( (array) apply_filters( 'mrc_product_identifier_meta_keys', $keys, $product ) ) );

		foreach ( $keys as $key ) {
			$key = sanitize_key( $key );
			if ( '' === $key ) {
				continue;
			}
			$value = $product->get_meta( $key, true );
			if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
				$values[] = (string) $value;
			}
		}
		return $values;
	}

	private function digits_only( $value ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}
		return preg_replace( '/[^0-9]/', '', (string) $value );
	}

	private function passes_check_digit( $gtin ) {
		$length = strlen( (string) $gtin );
		if ( ! in_array( $length, array( 8, 12, 13, 14 ), true ) || ! ctype_digit( (string) $gtin ) ) {
			return false;
		}

		$digits = str_split( (string) $gtin );
		$check  = (int) array_pop( $digits );
		$total  = 0;
		$weight = 3;
		for ( $index = count( $digits ) - 1; $index >= 0; --$index ) {
			$total += (int) $digits[ $index ] * $weight;
			$weight = 3 === $weight ? 1 : 3;
		}
		return $check === ( 10 - ( $total % 10 ) ) % 10;
	}
}
