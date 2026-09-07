<?php
namespace MRCWC;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	private static $instance;

	public static function instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function boot(): void {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_notices', array( $this, 'maybe_show_dependency_notice' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( MRCWC_FILE ), array( $this, 'add_settings_link' ) );

		Settings::instance()->boot();

		add_action( 'woocommerce_init', array( $this, 'boot_woocommerce_features' ) );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain(
			'merchant-review-connect-for-woocommerce',
			false,
			dirname( plugin_basename( MRCWC_FILE ) ) . '/languages/'
		);
	}

	public function maybe_show_dependency_notice(): void {
		if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		?>
		<div class="notice notice-error"><p>
			<?php esc_html_e( 'Merchant Review Connect for WooCommerce requires WooCommerce to be installed and active.', 'merchant-review-connect-for-woocommerce' ); ?>
		</p></div>
		<?php
	}

	public function add_settings_link( array $links ): array {
		$url = admin_url( 'admin.php?page=mrcwc' );
		array_unshift(
			$links,
			'<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'merchant-review-connect-for-woocommerce' ) . '</a>'
		);
		return $links;
	}

	public function boot_woocommerce_features(): void {
		Survey::instance()->boot();
		Store_Widget::instance()->boot();
	}
}
