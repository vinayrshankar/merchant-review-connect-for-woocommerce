<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Integration;

use VinayShankar\MerchantReviewConnect\Catalog\GtinCollection;
use VinayShankar\MerchantReviewConnect\Checkout\ConfirmationOrder;
use VinayShankar\MerchantReviewConnect\Config\OptionRepository;
use VinayShankar\MerchantReviewConnect\Delivery\Estimator;

defined( 'ABSPATH' ) || exit;

final class CustomerReviewOptIn {
	private $options;
	private $confirmation;
	private $delivery;
	private $gtins;

	public function __construct( OptionRepository $options, ConfirmationOrder $confirmation, Estimator $delivery, GtinCollection $gtins ) {
		$this->options      = $options;
		$this->confirmation = $confirmation;
		$this->delivery     = $delivery;
		$this->gtins        = $gtins;
	}

	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_when_needed' ), 30 );
	}

	public function enqueue_when_needed() {
		if ( is_admin() ) {
			return;
		}

		$config = $this->options->read();
		if ( ! $config->survey_is_enabled() || '' === $config->merchant_id() ) {
			return;
		}
		if ( ! is_ssl() && ! apply_filters( 'mrc_allow_non_https_confirmation', false ) ) {
			return;
		}

		$order = $this->confirmation->resolve();
		if ( ! $order ) {
			return;
		}

		$email   = sanitize_email( $order->get_billing_email() );
		$country = $this->confirmation->delivery_country( $order );
		if ( ! is_email( $email ) || '' === $country ) {
			return;
		}

		$payload = array(
			'merchant_id'             => (int) $config->merchant_id(),
			'order_id'                => (string) $order->get_order_number(),
			'email'                   => $email,
			'delivery_country'        => $country,
			'estimated_delivery_date' => $this->delivery->estimate( $order, $config ),
			'opt_in_style'            => $config->survey_position(),
		);

		if ( $config->product_ids_are_enabled() ) {
			$products = $this->gtins->from_order( $order, $config );
			if ( $products ) {
				$payload['products'] = $products;
			}
		}

		$payload = (array) apply_filters( 'mrc_customer_review_payload', $payload, $order, $config );
		$runtime = array(
			'payload' => $payload,
			'locale'  => $config->locale(),
		);

		wp_register_script(
			'mrc-review-runtime',
			MRC_PLUGIN_URL . 'assets/js/customer-review-optin.js',
			array(),
			MRC_VERSION,
			true
		);
		wp_localize_script( 'mrc-review-runtime', 'MRCReviewConnect', $runtime );
		wp_enqueue_script( 'mrc-review-runtime' );

		wp_enqueue_script(
			'mrc-google-customer-reviews',
			'https://apis.google.com/js/platform.js?onload=MRCReviewConnectReady',
			array( 'mrc-review-runtime' ),
			null,
			true
		);
	}
}
