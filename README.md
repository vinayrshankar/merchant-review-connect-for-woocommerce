# Merchant Review Connect for WooCommerce

An independently implemented WooCommerce integration for Google Customer Reviews and the Google store widget.

**Version:** 1.1.0  
**Author:** [Vinay Shankar](https://tfaworld.org/)  
**Website:** [https://tfaworld.org/](https://tfaworld.org/)  
**License:** GPL-2.0-or-later

## What it does

- Offers the Google Customer Reviews opt-in on authorized WooCommerce order-confirmation pages.
- Sends Google's required order fields using WooCommerce CRUD APIs.
- Supports optional product GTIN collection with check-digit validation.
- Calculates estimated delivery dates using calendar days, weekdays, or an exact order-meta override.
- Loads Google's optional store widget with configurable placement and margins.
- Declares HPOS and Cart/Checkout Blocks compatibility.
- Adds configuration as a native **WooCommerce > Settings > Merchant Reviews** tab.

## Architecture

Version 1.1 is a clean architectural redesign. It uses injected services, a domain-oriented `src/` tree, WooCommerce's native settings framework, and dedicated local JavaScript runtime files. See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

For implementation provenance and independent-design notes, see [docs/INDEPENDENT-IMPLEMENTATION.md](docs/INDEPENDENT-IMPLEMENTATION.md).

## Requirements

- WordPress 6.0+
- WooCommerce 7.0+
- PHP 7.4+
- HTTPS on order-confirmation pages
- Google Merchant Center with Google Customer Reviews enabled

## Installation

1. Download the release ZIP.
2. In WordPress, go to **Plugins > Add New Plugin > Upload Plugin**.
3. Upload and activate **Merchant Review Connect for WooCommerce**.
4. Open **WooCommerce > Settings > Merchant Reviews**.
5. Enter your Merchant Center ID and configure the integration.

## Developer filters

- `mrc_customer_review_payload`
- `mrc_delivery_date`
- `mrc_product_gtin`
- `mrc_product_identifier_meta_keys`
- `mrc_store_widget_options`
- `mrc_allow_non_https_confirmation`

## Privacy

When the survey integration is enabled, the order-confirmation integration provides Google with the transaction fields required by Google Customer Reviews. This can include the order number, customer email address, delivery country, estimated delivery date, and optional product GTINs.

## License

Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/

Licensed under GPL-2.0-or-later. See [LICENSE](LICENSE).

## Trademarks

Google, Google Customer Reviews, and Google Merchant Center are trademarks of Google LLC. WooCommerce is a trademark of Automattic Inc. This project is an independent integration and is not endorsed by Google or Automattic.
