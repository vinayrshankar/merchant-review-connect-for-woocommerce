# Merchant Review Connect for WooCommerce

A lightweight WooCommerce plugin that connects order-confirmation pages to Google Customer Reviews and Google's store widget.

**Version:** 1.0.0  
**Author:** [Vinay Shankar](https://tfaworld.org/)  
**Website:** [tfaworld.org](https://tfaworld.org/)  
**License:** GPL-2.0-or-later

## Features

- Google Customer Reviews survey opt-in on valid WooCommerce order-confirmation pages.
- Required order data for the Google Customer Reviews opt-in payload.
- Optional product GTIN submission for product-review collection.
- WooCommerce Global Unique ID and configurable product-meta GTIN lookup.
- GTIN-8, GTIN-12, GTIN-13, and GTIN-14 check-digit validation.
- Estimated delivery dates using calendar days, business days, or an exact order-meta override.
- Optional Google store widget with configurable desktop and mobile margins.
- WooCommerce HPOS compatibility.
- WooCommerce Cart and Checkout Blocks compatibility declaration.
- WooCommerce CRUD APIs for order and product access.
- No activation code, licensing client, telemetry, account connection, bundled updater, or third-party SDK.

## Requirements

- WordPress 6.0 or later.
- WooCommerce 7.0 or later.
- PHP 7.4 or later.
- A Google Merchant Center account eligible for Google Customer Reviews.
- HTTPS on the order-confirmation page when the survey feature is enabled.

## Installation

1. Download the release ZIP.
2. In WordPress, open **Plugins > Add New Plugin > Upload Plugin**.
3. Upload the ZIP and activate **Merchant Review Connect for WooCommerce**.
4. Open **WooCommerce > Merchant Reviews**.
5. Enter your Google Merchant Center ID.
6. Configure the survey, delivery estimate, GTIN lookup, and optional store widget.

## GTIN resolution

The plugin checks WooCommerce's Global Unique ID when available and then common GTIN/EAN product metadata. Additional product meta keys can be configured in the plugin settings.

Developers can customize GTIN handling with:

- `mrcwc_product_gtin`
- `mrcwc_gtin_meta_keys`

## Delivery-date handling

The plugin can calculate delivery dates using calendar days or Monday-Friday business days. An exact date saved by another shipping integration can be used by configuring its order meta key.

Developers can customize the final estimated-delivery value with:

- `mrcwc_estimated_delivery_date`

## Privacy

When the survey feature is enabled, order information required for Google Customer Reviews is provided to Google's frontend integration after a customer reaches a valid WooCommerce order-confirmation page. This can include the order number, customer email, delivery country, estimated delivery date, and optional product GTINs.

Review Google's current Customer Reviews program terms and privacy requirements before enabling the integration.

## Security

Please see [SECURITY.md](SECURITY.md).

## License

Copyright (C) 2026 Vinay Shankar.

This project is licensed under the GNU General Public License, version 2 or any later version. See [LICENSE](LICENSE).

## Trademark notice

Google, Google Customer Reviews, and Google Merchant Center are trademarks of Google LLC. WooCommerce is a trademark of Automattic Inc. This project is an independent integration and is not affiliated with or endorsed by Google or Automattic.
