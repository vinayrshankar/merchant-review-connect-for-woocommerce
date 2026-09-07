<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Integration;

use VinayShankar\MerchantReviewConnect\Config\OptionRepository;

defined( 'ABSPATH' ) || exit;

final class StoreWidget {
	private $options;

	public function __construct( OptionRepository $options ) {
		$this->options = $options;
	}

	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_when_enabled' ), 40 );
	}

	public function enqueue_when_enabled() {
		if ( is_admin() ) {
			return;
		}
		$config = $this->options->read();
		if ( ! $config->widget_is_enabled() ) {
			return;
		}

		$widget = array_merge(
			array( 'position' => $config->widget_position() ),
			$config->widget_margins()
		);
		$widget = (array) apply_filters( 'mrc_store_widget_options', $widget, $config );

		wp_enqueue_script(
			'mrc-google-store-widget',
			'https://www.gstatic.com/shopping/merchant/merchantwidget.js',
			array(),
			null,
			true
		);
		wp_register_script(
			'mrc-store-widget-runtime',
			MRC_PLUGIN_URL . 'assets/js/store-widget.js',
			array( 'mrc-google-store-widget' ),
			MRC_VERSION,
			true
		);
		wp_localize_script( 'mrc-store-widget-runtime', 'MRCStoreWidget', array( 'options' => $widget ) );
		wp_enqueue_script( 'mrc-store-widget-runtime' );
	}
}
