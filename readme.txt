=== Merchant Review Connect for WooCommerce ===
Tags: woocommerce, customer reviews, merchant center, store ratings, product ratings
Requires at least: 6.0
Requires PHP: 7.4
Requires Plugins: woocommerce
WC requires at least: 7.0
WC tested up to: 11.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Directly connect WooCommerce order confirmations with Google Customer Reviews and the Google store widget.

Author: Vinay Shankar
Website: https://tfaworld.org/

== Description ==

Merchant Review Connect is a lightweight WooCommerce integration that:

* Renders the Google Customer Reviews survey opt-in on valid order-confirmation pages.
* Sends the order number, customer email, delivery country and estimated delivery date required by Google.
* Optionally sends valid GTINs for product-review collection.
* Supports WooCommerce Global Unique ID plus configurable product meta keys for GTIN lookup.
* Optionally loads Google's current store widget with configurable desktop and mobile margins.
* Supports High-Performance Order Storage (HPOS) and Cart/Checkout Blocks.
* Uses WooCommerce CRUD APIs for order and product access.
* Contains no licensing client, activation-code system, telemetry, account connection, bundled updater, or vendor SDK.

The plugin makes no background API calls. The only remote resources it loads are Google's frontend scripts when the corresponding feature is enabled.

== Installation ==

1. Upload the plugin ZIP in WordPress under Plugins > Add Plugin > Upload Plugin.
2. Activate the plugin.
3. Open WooCommerce > Merchant Reviews.
4. Enter your Google Merchant Center ID.
5. Configure the survey delivery estimate and optional product GTIN settings.
6. Enable the optional Google store widget if desired.

Your Merchant Center account and website must be eligible for Google Customer Reviews, and Google requires the order confirmation page to use HTTPS.

== GTIN lookup ==

The plugin checks WooCommerce's Global Unique ID when available, followed by common GTIN/EAN product metadata. You can add more product meta keys in the settings page. Only GTIN-8, GTIN-12, GTIN-13 and GTIN-14 values with a valid check digit are sent.

Developers can customize GTIN resolution with the `mrcwc_product_gtin` and `mrcwc_gtin_meta_keys` filters.

== Delivery date ==

Choose calendar days or Monday-Friday business days after the order creation date. If your shipping integration saves an exact estimated date in order metadata, enter its meta key in settings and Merchant Review Connect will use that value when it can parse it.

Developers can customize the final value with the `mrcwc_estimated_delivery_date` filter.

== Privacy ==

When the survey feature is enabled, the Google Customer Reviews opt-in receives order information required by Google after the customer reaches a valid order confirmation page. This can include the order number, customer email, delivery country, estimated delivery date, and optional product GTINs. Review Google's current Customer Reviews terms and privacy requirements before enabling the feature.

== Changelog ==

= 1.0.0 =
* Initial release.
* Google Customer Reviews survey opt-in integration.
* Estimated delivery date calculation and order-meta override.
* Product GTIN collection with check-digit validation.
* Google store widget integration.
* HPOS and Cart/Checkout Blocks compatibility declarations.
