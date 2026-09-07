<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect;

use VinayShankar\MerchantReviewConnect\Admin\ReviewSettingsTab;
use VinayShankar\MerchantReviewConnect\Catalog\GtinCollection;
use VinayShankar\MerchantReviewConnect\Checkout\ConfirmationOrder;
use VinayShankar\MerchantReviewConnect\Config\OptionRepository;
use VinayShankar\MerchantReviewConnect\Delivery\Estimator;
use VinayShankar\MerchantReviewConnect\Integration\CustomerReviewOptIn;
use VinayShankar\MerchantReviewConnect\Integration\StoreWidget;
use VinayShankar\MerchantReviewConnect\Platform\Compatibility;

defined( 'ABSPATH' ) || exit;

final class Application {
	private $options;

	public function __construct( OptionRepository $options ) {
		$this->options = $options;
	}

	public function register() {
		$compatibility = new Compatibility();
		$compatibility->register();

		add_action( 'plugins_loaded', array( $this, 'translations' ) );
		add_action( 'admin_notices', array( $this, 'woocommerce_notice' ) );
		add_action( 'woocommerce_init', array( $this, 'woocommerce_ready' ) );
	}

	public function translations() {
		load_plugin_textdomain(
			'merchant-review-connect-for-woocommerce',
			false,
			dirname( plugin_basename( MRC_PLUGIN_FILE ) ) . '/languages/'
		);
	}

	public function woocommerce_notice() {
		if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>';
		echo esc_html__( 'Merchant Review Connect for WooCommerce needs WooCommerce to be installed and active.', 'merchant-review-connect-for-woocommerce' );
		echo '</p></div>';
	}

	public function woocommerce_ready() {
		$confirmation = new ConfirmationOrder();
		$delivery     = new Estimator();
		$gtins        = new GtinCollection();

		$settings = new ReviewSettingsTab( $this->options );
		$survey   = new CustomerReviewOptIn( $this->options, $confirmation, $delivery, $gtins );
		$widget   = new StoreWidget( $this->options );

		$settings->register();
		$survey->register();
		$widget->register();
	}
}
