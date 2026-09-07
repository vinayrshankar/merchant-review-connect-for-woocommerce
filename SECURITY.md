# Security

This plugin does not contain a license client, telemetry service, remote updater SDK, or account-connection layer.

The Google Customer Reviews survey payload is rendered only for a valid WooCommerce order-confirmation request where either the order key matches or the current signed-in user owns the order. The plugin uses WooCommerce CRUD objects for HPOS compatibility.

If you discover a security issue in this custom build, disable the affected feature while investigating and avoid publishing customer or order data in public bug reports.
