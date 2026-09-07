<?php
/**
 * Plugin Name:       Merchant Review Connect for WooCommerce
 * Description:       A privacy-conscious WooCommerce bridge for Google Customer Reviews and the Google store widget.
 * Version:           1.1.0
 * Plugin URI:        https://tfaworld.org/
 * Author:            Vinay Shankar
 * Author URI:        https://tfaworld.org/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       merchant-review-connect-for-woocommerce
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 7.0
 * WC tested up to:   11.1
 *
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

defined( 'ABSPATH' ) || exit;

define( 'MRC_VERSION', '1.1.0' );
define( 'MRC_PLUGIN_FILE', __FILE__ );
define( 'MRC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MRC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

$merchant_review_connect_files = array(
	'src/Config/Configuration.php',
	'src/Config/OptionRepository.php',
	'src/Checkout/ConfirmationOrder.php',
	'src/Delivery/Estimator.php',
	'src/Catalog/GtinCollection.php',
	'src/Integration/CustomerReviewOptIn.php',
	'src/Integration/StoreWidget.php',
	'src/Admin/ReviewSettingsTab.php',
	'src/Platform/Compatibility.php',
	'src/Application.php',
);

foreach ( $merchant_review_connect_files as $merchant_review_connect_file ) {
	require_once MRC_PLUGIN_DIR . $merchant_review_connect_file;
}

$merchant_review_connect = new \VinayShankar\MerchantReviewConnect\Application(
	new \VinayShankar\MerchantReviewConnect\Config\OptionRepository()
);
$merchant_review_connect->register();
