# Architecture

Merchant Review Connect 1.1 is organized around small injected services rather than static helpers or singleton classes.

## Runtime composition

`Application` constructs the runtime after WooCommerce is ready. Dependencies are passed into integrations explicitly:

- `OptionRepository` reads normalized configuration.
- `ConfirmationOrder` authorizes access to the current order-confirmation request.
- `Estimator` determines the delivery date.
- `GtinCollection` collects and validates product identifiers.
- `CustomerReviewOptIn` creates the Google Customer Reviews payload and registers scripts.
- `StoreWidget` registers the optional Google store widget.
- `ReviewSettingsTab` uses WooCommerce's own settings-screen framework.
- `Compatibility` declares WooCommerce feature compatibility and external script attributes.

## Deliberate design choices

- No singleton service locator.
- No custom PHP autoloader or Composer runtime.
- No `includes/class-*.php` convention.
- No custom WordPress Settings API page.
- No PHP templates containing Google JavaScript snippets.
- Google integrations are expressed as WordPress-enqueued scripts plus small local JavaScript runtimes.
- Configuration is stored as independent WordPress options and exposed as a read-only `Configuration` object.
- Order access, delivery calculation, catalog identifiers, admin settings, and external integrations are separated by responsibility.
