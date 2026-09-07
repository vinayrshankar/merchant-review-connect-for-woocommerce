<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Config;

defined( 'ABSPATH' ) || exit;

final class OptionRepository {
	private $defaults = array(
		'merchant_id'          => '',
		'survey_enabled'       => 'yes',
		'survey_position'      => 'CENTER_DIALOG',
		'locale'               => '',
		'eta_strategy'         => 'calendar',
		'eta_days'             => 7,
		'eta_meta_key'         => '',
		'product_ids_enabled'  => 'yes',
		'product_meta_keys'    => '',
		'widget_enabled'       => 'no',
		'widget_position'      => 'RIGHT_BOTTOM',
		'widget_side'          => 36,
		'widget_bottom'        => 36,
		'widget_mobile_side'   => 16,
		'widget_mobile_bottom' => 46,
	);

	private $option_map = array(
		'merchant_id'          => 'mrc_review_merchant_id',
		'survey_enabled'       => 'mrc_review_survey_enabled',
		'survey_position'      => 'mrc_review_survey_position',
		'locale'               => 'mrc_review_locale',
		'eta_strategy'         => 'mrc_review_eta_strategy',
		'eta_days'             => 'mrc_review_eta_days',
		'eta_meta_key'         => 'mrc_review_eta_meta_key',
		'product_ids_enabled'  => 'mrc_review_product_ids_enabled',
		'product_meta_keys'    => 'mrc_review_product_meta_keys',
		'widget_enabled'       => 'mrc_review_widget_enabled',
		'widget_position'      => 'mrc_review_widget_position',
		'widget_side'          => 'mrc_review_widget_side',
		'widget_bottom'        => 'mrc_review_widget_bottom',
		'widget_mobile_side'   => 'mrc_review_widget_mobile_side',
		'widget_mobile_bottom' => 'mrc_review_widget_mobile_bottom',
	);

	public function read() {
		$values = array();
		foreach ( $this->option_map as $key => $option_name ) {
			$values[ $key ] = get_option( $option_name, $this->defaults[ $key ] );
		}
		return new Configuration( $this->normalize( $values ) );
	}

	public function option_name( $key ) {
		return isset( $this->option_map[ $key ] ) ? $this->option_map[ $key ] : '';
	}

	public function default_value( $key ) {
		return isset( $this->defaults[ $key ] ) ? $this->defaults[ $key ] : '';
	}

	public function all_option_names() {
		return $this->option_map;
	}

	private function normalize( array $values ) {
		$values['merchant_id'] = substr( preg_replace( '/\D+/', '', (string) $values['merchant_id'] ), 0, 30 );

		foreach ( array( 'survey_enabled', 'product_ids_enabled', 'widget_enabled' ) as $toggle ) {
			$values[ $toggle ] = 'yes' === $values[ $toggle ] ? 'yes' : 'no';
		}

		$allowed_positions = array( 'CENTER_DIALOG', 'BOTTOM_RIGHT_DIALOG', 'BOTTOM_LEFT_DIALOG', 'TOP_RIGHT_DIALOG', 'TOP_LEFT_DIALOG', 'BOTTOM_TRAY' );
		if ( ! in_array( $values['survey_position'], $allowed_positions, true ) ) {
			$values['survey_position'] = $this->defaults['survey_position'];
		}

		$allowed_locales = array( '', 'af', 'ar', 'cs', 'da', 'de', 'en', 'en-AU', 'en-GB', 'en-US', 'es', 'es-419', 'fil', 'fr', 'ga', 'id', 'it', 'ja', 'ms', 'nl', 'no', 'pl', 'pt-BR', 'pt-PT', 'ru', 'sv', 'tr', 'zh-CN', 'zh-TW' );
		if ( ! in_array( $values['locale'], $allowed_locales, true ) ) {
			$values['locale'] = '';
		}

		$values['eta_strategy'] = 'weekdays' === $values['eta_strategy'] ? 'weekdays' : 'calendar';
		$values['eta_days']     = min( 90, max( 0, absint( $values['eta_days'] ) ) );
		$values['eta_meta_key'] = sanitize_key( $values['eta_meta_key'] );

		$values['widget_position'] = 'LEFT_BOTTOM' === $values['widget_position'] ? 'LEFT_BOTTOM' : 'RIGHT_BOTTOM';
		foreach ( array( 'widget_side', 'widget_bottom', 'widget_mobile_side', 'widget_mobile_bottom' ) as $margin ) {
			$values[ $margin ] = min( 500, max( 0, absint( $values[ $margin ] ) ) );
		}

		$values['product_meta_keys'] = sanitize_text_field( (string) $values['product_meta_keys'] );
		return $values;
	}
}
