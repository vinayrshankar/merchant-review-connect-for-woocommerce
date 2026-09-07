<?php
namespace MRCWC;

defined( 'ABSPATH' ) || exit;

final class Store_Widget {
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
		add_action( 'wp_head', array( $this, 'render' ), 90 );
	}

	public function render(): void {
		if ( $this->rendered || is_admin() ) {
			return;
		}

		$settings = Settings::all();
		if ( 'yes' !== $settings['widget_enabled'] ) {
			return;
		}

		$config = array(
			'position'           => (string) $settings['widget_position'],
			'sideMargin'         => absint( $settings['widget_side_margin'] ),
			'bottomMargin'       => absint( $settings['widget_bottom_margin'] ),
			'mobileSideMargin'   => absint( $settings['widget_mobile_side_margin'] ),
			'mobileBottomMargin' => absint( $settings['widget_mobile_bottom_margin'] ),
		);
		$config = (array) apply_filters( 'mrcwc_store_widget_config', $config, $settings );
		$json   = wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		if ( ! $json ) {
			return;
		}

		$this->rendered = true;
		?>
		<!-- Merchant Review Connect: Google store widget -->
		<script id="mrcwc-merchant-widget-script" src="https://www.gstatic.com/shopping/merchant/merchantwidget.js" defer></script>
		<script>
		(function () {
			var script = document.getElementById('mrcwc-merchant-widget-script');
			if (!script) {
				return;
			}
			script.addEventListener('load', function () {
				if (window.merchantwidget && window.merchantwidget.start) {
					window.merchantwidget.start(<?php echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>);
				}
			});
		}());
		</script>
		<?php
	}
}
