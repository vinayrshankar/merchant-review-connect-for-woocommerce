<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Platform;

defined( 'ABSPATH' ) || exit;

final class Compatibility {
	public function register() {
		add_action( 'before_woocommerce_init', array( $this, 'declare_features' ) );
		add_filter( 'script_loader_tag', array( $this, 'script_attributes' ), 10, 3 );
	}

	public function declare_features() {
		$class = '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil';
		if ( ! class_exists( $class ) ) {
			return;
		}
		$class::declare_compatibility( 'custom_order_tables', MRC_PLUGIN_FILE, true );
		$class::declare_compatibility( 'cart_checkout_blocks', MRC_PLUGIN_FILE, true );
	}

	public function script_attributes( $tag, $handle, $src ) {
		if ( 'mrc-google-customer-reviews' === $handle ) {
			return '<script id="' . esc_attr( $handle ) . '-js" src="' . esc_url( $src ) . '" async defer></script>' . "\n";
		}
		if ( 'mrc-google-store-widget' === $handle ) {
			return '<script id="' . esc_attr( $handle ) . '-js" src="' . esc_url( $src ) . '" defer></script>' . "\n";
		}
		return $tag;
	}
}
