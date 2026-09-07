<?php
namespace MRCWC;

defined( 'ABSPATH' ) || exit;

final class Survey {
	private static $instance;
	private $rendered = false;

	public static function instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function boot(): void {
		add_action( 'wp_footer', array( $this, 'render' ), 99 );
	}

	public function render(): void {
		if ( $this->rendered || is_admin() ) {
			return;
		}

		$settings = Settings::all();
		if ( 'yes' !== $settings['survey_enabled'] || empty( $settings['merchant_id'] ) ) {
			return;
		}

		if ( ! is_ssl() && ! apply_filters( 'mrcwc_allow_insecure_survey', false ) ) {
			return;
		}

		$order = Order_Context::current_order();
		if ( ! $order ) {
			return;
		}

		$email   = sanitize_email( $order->get_billing_email() );
		$country = strtoupper( (string) $order->get_shipping_country() );
		if ( '' === $country ) {
			$country = strtoupper( (string) $order->get_billing_country() );
		}

		if ( ! is_email( $email ) || ! preg_match( '/^[A-Z]{2}$/', $country ) ) {
			return;
		}

		$payload = array(
			'merchant_id'             => (int) $settings['merchant_id'],
			'order_id'                => (string) $order->get_order_number(),
			'email'                   => $email,
			'delivery_country'        => $country,
			'estimated_delivery_date' => Delivery_Date::for_order( $order, $settings ),
			'opt_in_style'            => (string) $settings['survey_style'],
		);

		if ( 'yes' === $settings['include_product_gtins'] ) {
			$products = GTIN_Resolver::for_order( $order, $settings );
			if ( $products ) {
				$payload['products'] = $products;
			}
		}

		$payload = (array) apply_filters( 'mrcwc_survey_payload', $payload, $order, $settings );
		$json    = wp_json_encode( $payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		if ( ! $json ) {
			return;
		}

		$this->rendered = true;
		$language       = ! empty( $settings['language'] ) ? (string) $settings['language'] : '';
		?>
		<!-- Merchant Review Connect: Google Customer Reviews survey -->
		<?php if ( '' !== $language ) : ?>
		<script>window.___gcfg = {lang: <?php echo wp_json_encode( $language ); ?>};</script>
		<?php endif; ?>
		<script>
		window.renderOptIn = function () {
			if (!window.gapi || !window.gapi.load) {
				return;
			}
			window.gapi.load('surveyoptin', function () {
				if (window.gapi.surveyoptin && window.gapi.surveyoptin.render) {
					window.gapi.surveyoptin.render(<?php echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>);
				}
			});
		};
		</script>
		<script
		src="https://apis.google.com/js/platform.js?onload=renderOptIn"
		async
		defer
		></script>
		<?php
	}
}
