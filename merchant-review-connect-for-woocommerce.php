<?php
/**
 * Plugin Name:       Merchant Review Connect for WooCommerce
 * Description:       Connects WooCommerce order confirmations to Google Customer Reviews and the Google store widget.
 * Version:           1.0.0
 * Plugin URI:         https://tfaworld.org/
 * Author:            Vinay Shankar
 * Author URI:         https://tfaworld.org/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       merchant-review-connect-for-woocommerce
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * WC requires at least: 7.0
 * WC tested up to:   11.1
 */

defined( 'ABSPATH' ) || exit;

define( 'MRCWC_VERSION', '1.0.0' );
define( 'MRCWC_FILE', __FILE__ );
define( 'MRCWC_DIR', plugin_dir_path( __FILE__ ) );
define( 'MRCWC_URL', plugin_dir_url( __FILE__ ) );

add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', MRCWC_FILE, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', MRCWC_FILE, true );
		}
	}
);

require_once MRCWC_DIR . 'includes/class-settings.php';
require_once MRCWC_DIR . 'includes/class-delivery-date.php';
require_once MRCWC_DIR . 'includes/class-gtin-resolver.php';
require_once MRCWC_DIR . 'includes/class-order-context.php';
require_once MRCWC_DIR . 'includes/class-survey.php';
require_once MRCWC_DIR . 'includes/class-store-widget.php';
require_once MRCWC_DIR . 'includes/class-plugin.php';

MRCWC\Plugin::instance()->boot();
