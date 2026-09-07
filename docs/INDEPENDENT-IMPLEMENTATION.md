# Independent implementation record

Merchant Review Connect for WooCommerce is implemented as an independent WordPress/WooCommerce integration maintained by Vinay Shankar.

The implementation is based on the public integration contracts provided by Google Merchant Center and the public extension APIs provided by WooCommerce/WordPress. Required external API names, parameter names, script URLs, and WooCommerce hook/API identifiers necessarily match those public platforms.

Version 1.1 was intentionally redesigned around a new architecture:

- `VinayShankar\MerchantReviewConnect` PHP namespace.
- Dependency injection instead of global singletons.
- WooCommerce native settings tab instead of a standalone settings page.
- Individual WordPress options rather than one shared settings array.
- Local JavaScript runtime files rather than PHP-emitted integration snippets.
- `src/` domain-oriented folders rather than `includes/class-*.php` files.
- Separate order authorization, delivery estimation, catalog identifier, integration, and platform services.

The repository does not bundle a third-party licensing SDK, telemetry SDK, updater framework, or vendor dependency tree.
