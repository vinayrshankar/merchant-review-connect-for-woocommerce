<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Config;

defined( 'ABSPATH' ) || exit;

final class Configuration {
	private $values;

	public function __construct( array $values ) {
		$this->values = $values;
	}

	public function merchant_id() {
		return (string) $this->values['merchant_id'];
	}

	public function survey_is_enabled() {
		return 'yes' === $this->values['survey_enabled'];
	}

	public function survey_position() {
		return (string) $this->values['survey_position'];
	}

	public function locale() {
		return (string) $this->values['locale'];
	}

	public function eta_strategy() {
		return (string) $this->values['eta_strategy'];
	}

	public function eta_days() {
		return (int) $this->values['eta_days'];
	}

	public function eta_meta_key() {
		return (string) $this->values['eta_meta_key'];
	}

	public function product_ids_are_enabled() {
		return 'yes' === $this->values['product_ids_enabled'];
	}

	public function extra_product_meta_keys() {
		$value = (string) $this->values['product_meta_keys'];
		if ( '' === trim( $value ) ) {
			return array();
		}

		$parts = preg_split( '/[\r\n,]+/', $value );
		$keys  = array();
		foreach ( (array) $parts as $part ) {
			$key = sanitize_key( trim( $part ) );
			if ( '' !== $key ) {
				$keys[ $key ] = $key;
			}
		}
		return array_values( $keys );
	}

	public function widget_is_enabled() {
		return 'yes' === $this->values['widget_enabled'];
	}

	public function widget_position() {
		return (string) $this->values['widget_position'];
	}

	public function widget_margins() {
		return array(
			'sideMargin'         => (int) $this->values['widget_side'],
			'bottomMargin'       => (int) $this->values['widget_bottom'],
			'mobileSideMargin'   => (int) $this->values['widget_mobile_side'],
			'mobileBottomMargin' => (int) $this->values['widget_mobile_bottom'],
		);
	}
}
