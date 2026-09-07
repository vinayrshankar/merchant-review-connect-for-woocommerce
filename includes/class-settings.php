<?php
namespace MRCWC;

defined( 'ABSPATH' ) || exit;

final class Settings {
	const OPTION = 'mrcwc_settings';

	private static $instance;

	public static function instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function boot(): void {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_menu' ), 99 );
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public static function defaults(): array {
		return array(
			'merchant_id'              => '',
			'survey_enabled'            => 'yes',
			'survey_style'              => 'CENTER_DIALOG',
			'language'                  => '',
			'delivery_mode'             => 'calendar',
			'delivery_days'             => 7,
			'delivery_date_meta_key'    => '',
			'include_product_gtins'     => 'yes',
			'custom_gtin_meta_keys'     => '',
			'widget_enabled'            => 'no',
			'widget_position'           => 'RIGHT_BOTTOM',
			'widget_side_margin'        => 36,
			'widget_bottom_margin'      => 36,
			'widget_mobile_side_margin' => 16,
			'widget_mobile_bottom_margin' => 46,
		);
	}

	public static function all(): array {
		$saved = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::defaults() );
	}

	public static function get( string $key, $fallback = null ) {
		$settings = self::all();
		return array_key_exists( $key, $settings ) ? $settings[ $key ] : $fallback;
	}

	public function add_menu(): void {
		add_submenu_page(
			'woocommerce',
			__( 'Merchant Review Connect', 'merchant-review-connect-for-woocommerce' ),
			__( 'Merchant Reviews', 'merchant-review-connect-for-woocommerce' ),
			'manage_woocommerce',
			'mrcwc',
			array( $this, 'render_page' )
		);
	}

	public function enqueue_assets( string $hook ): void {
		if ( 'woocommerce_page_mrcwc' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'mrcwc-admin', MRCWC_URL . 'assets/admin.css', array(), MRCWC_VERSION );
	}

	public function register(): void {
		register_setting(
			'mrcwc_group',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);

		add_settings_section( 'mrcwc_core', __( 'Google Customer Reviews', 'merchant-review-connect-for-woocommerce' ), array( $this, 'core_intro' ), 'mrcwc' );
		$this->add_field( 'merchant_id', __( 'Merchant Center ID', 'merchant-review-connect-for-woocommerce' ), 'field_merchant_id', 'mrcwc_core' );
		$this->add_field( 'survey_enabled', __( 'Survey opt-in', 'merchant-review-connect-for-woocommerce' ), 'field_survey_enabled', 'mrcwc_core' );
		$this->add_field( 'survey_style', __( 'Opt-in position', 'merchant-review-connect-for-woocommerce' ), 'field_survey_style', 'mrcwc_core' );
		$this->add_field( 'language', __( 'Language', 'merchant-review-connect-for-woocommerce' ), 'field_language', 'mrcwc_core' );

		add_settings_section( 'mrcwc_delivery', __( 'Estimated delivery date', 'merchant-review-connect-for-woocommerce' ), array( $this, 'delivery_intro' ), 'mrcwc' );
		$this->add_field( 'delivery_mode', __( 'Day calculation', 'merchant-review-connect-for-woocommerce' ), 'field_delivery_mode', 'mrcwc_delivery' );
		$this->add_field( 'delivery_days', __( 'Estimated delivery days', 'merchant-review-connect-for-woocommerce' ), 'field_delivery_days', 'mrcwc_delivery' );
		$this->add_field( 'delivery_date_meta_key', __( 'Exact delivery-date order meta', 'merchant-review-connect-for-woocommerce' ), 'field_delivery_meta', 'mrcwc_delivery' );

		add_settings_section( 'mrcwc_products', __( 'Product reviews', 'merchant-review-connect-for-woocommerce' ), array( $this, 'products_intro' ), 'mrcwc' );
		$this->add_field( 'include_product_gtins', __( 'Send product GTINs', 'merchant-review-connect-for-woocommerce' ), 'field_gtins_enabled', 'mrcwc_products' );
		$this->add_field( 'custom_gtin_meta_keys', __( 'Additional GTIN meta keys', 'merchant-review-connect-for-woocommerce' ), 'field_gtin_meta_keys', 'mrcwc_products' );

		add_settings_section( 'mrcwc_widget', __( 'Google store widget', 'merchant-review-connect-for-woocommerce' ), array( $this, 'widget_intro' ), 'mrcwc' );
		$this->add_field( 'widget_enabled', __( 'Store widget', 'merchant-review-connect-for-woocommerce' ), 'field_widget_enabled', 'mrcwc_widget' );
		$this->add_field( 'widget_position', __( 'Widget position', 'merchant-review-connect-for-woocommerce' ), 'field_widget_position', 'mrcwc_widget' );
		$this->add_field( 'widget_margins', __( 'Widget margins', 'merchant-review-connect-for-woocommerce' ), 'field_widget_margins', 'mrcwc_widget' );
	}

	private function add_field( string $id, string $title, string $method, string $section ): void {
		add_settings_field( $id, $title, array( $this, $method ), 'mrcwc', $section );
	}

	public function sanitize( $input ): array {
		$input    = is_array( $input ) ? $input : array();
		$defaults = self::defaults();
		$output   = $defaults;

		$merchant_id = isset( $input['merchant_id'] ) ? preg_replace( '/\D+/', '', (string) $input['merchant_id'] ) : '';
		$output['merchant_id'] = is_string( $merchant_id ) ? substr( $merchant_id, 0, 30 ) : '';

		$output['survey_enabled']        = $this->yes_no( $input, 'survey_enabled' );
		$output['include_product_gtins'] = $this->yes_no( $input, 'include_product_gtins' );
		$output['widget_enabled']         = $this->yes_no( $input, 'widget_enabled' );

		$styles = array( 'CENTER_DIALOG', 'BOTTOM_RIGHT_DIALOG', 'BOTTOM_LEFT_DIALOG', 'TOP_RIGHT_DIALOG', 'TOP_LEFT_DIALOG', 'BOTTOM_TRAY' );
		$output['survey_style'] = isset( $input['survey_style'] ) && in_array( $input['survey_style'], $styles, true ) ? $input['survey_style'] : $defaults['survey_style'];

		$languages = array( '', 'af', 'ar', 'cs', 'da', 'de', 'en', 'en-AU', 'en-GB', 'en-US', 'es', 'es-419', 'fil', 'fr', 'ga', 'id', 'it', 'ja', 'ms', 'nl', 'no', 'pl', 'pt-BR', 'pt-PT', 'ru', 'sv', 'tr', 'zh-CN', 'zh-TW' );
		$output['language'] = isset( $input['language'] ) && in_array( $input['language'], $languages, true ) ? $input['language'] : '';

		$output['delivery_mode'] = isset( $input['delivery_mode'] ) && 'business' === $input['delivery_mode'] ? 'business' : 'calendar';
		$output['delivery_days'] = isset( $input['delivery_days'] ) ? min( 90, max( 0, absint( $input['delivery_days'] ) ) ) : $defaults['delivery_days'];
		$output['delivery_date_meta_key'] = isset( $input['delivery_date_meta_key'] ) ? sanitize_key( $input['delivery_date_meta_key'] ) : '';

		$meta_keys = isset( $input['custom_gtin_meta_keys'] ) ? sanitize_text_field( $input['custom_gtin_meta_keys'] ) : '';
		$output['custom_gtin_meta_keys'] = implode( ', ', $this->sanitize_meta_key_list( $meta_keys ) );

		$output['widget_position'] = isset( $input['widget_position'] ) && 'LEFT_BOTTOM' === $input['widget_position'] ? 'LEFT_BOTTOM' : 'RIGHT_BOTTOM';
		foreach ( array( 'widget_side_margin', 'widget_bottom_margin', 'widget_mobile_side_margin', 'widget_mobile_bottom_margin' ) as $margin_key ) {
			$output[ $margin_key ] = isset( $input[ $margin_key ] ) ? min( 500, max( 0, absint( $input[ $margin_key ] ) ) ) : $defaults[ $margin_key ];
		}

		return $output;
	}

	private function yes_no( array $input, string $key ): string {
		return isset( $input[ $key ] ) && 'yes' === $input[ $key ] ? 'yes' : 'no';
	}

	private function sanitize_meta_key_list( string $value ): array {
		$parts = preg_split( '/[,\r\n]+/', $value );
		$keys  = array();
		foreach ( (array) $parts as $part ) {
			$key = sanitize_key( trim( $part ) );
			if ( '' !== $key ) {
				$keys[] = $key;
			}
		}
		return array_values( array_unique( $keys ) );
	}

	public function core_intro(): void {
		echo '<p>' . esc_html__( 'Enter the Merchant Center account used by your verified store. The survey appears only on secure WooCommerce order-confirmation pages.', 'merchant-review-connect-for-woocommerce' ) . '</p>';
	}

	public function delivery_intro(): void {
		echo '<p>' . esc_html__( 'Google requires an estimated delivery date for each order. You can calculate one from the order date or read an exact date from order metadata.', 'merchant-review-connect-for-woocommerce' ) . '</p>';
	}

	public function products_intro(): void {
		echo '<p>' . esc_html__( 'When enabled, valid GTIN-8, GTIN-12, GTIN-13 and GTIN-14 values are added for distinct products in the order.', 'merchant-review-connect-for-woocommerce' ) . '</p>';
	}

	public function widget_intro(): void {
		echo '<p>' . esc_html__( 'The optional store widget displays your Google store rating/quality widget when Google considers the store eligible.', 'merchant-review-connect-for-woocommerce' ) . '</p>';
	}

	private function name( string $key ): string {
		return self::OPTION . '[' . $key . ']';
	}

	public function field_merchant_id(): void {
		printf(
			'<input type="text" inputmode="numeric" pattern="[0-9]*" class="regular-text" name="%1$s" value="%2$s" autocomplete="off" />',
			esc_attr( $this->name( 'merchant_id' ) ),
			esc_attr( (string) self::get( 'merchant_id' ) )
		);
		echo '<p class="description">' . esc_html__( 'Digits only. Find this ID in Google Merchant Center.', 'merchant-review-connect-for-woocommerce' ) . '</p>';
	}

	public function field_survey_enabled(): void {
		$this->checkbox( 'survey_enabled', __( 'Show the Google Customer Reviews survey opt-in after checkout', 'merchant-review-connect-for-woocommerce' ) );
	}

	public function field_gtins_enabled(): void {
		$this->checkbox( 'include_product_gtins', __( 'Include valid GTINs for product-review collection', 'merchant-review-connect-for-woocommerce' ) );
	}

	public function field_widget_enabled(): void {
		$this->checkbox( 'widget_enabled', __( 'Load the Google store widget on storefront pages', 'merchant-review-connect-for-woocommerce' ) );
	}

	private function checkbox( string $key, string $label ): void {
		printf(
			'<label><input type="checkbox" name="%1$s" value="yes" %2$s /> %3$s</label>',
			esc_attr( $this->name( $key ) ),
			checked( 'yes', self::get( $key ), false ),
			esc_html( $label )
		);
	}

	public function field_survey_style(): void {
		$options = array(
			'CENTER_DIALOG'       => __( 'Center dialog', 'merchant-review-connect-for-woocommerce' ),
			'BOTTOM_RIGHT_DIALOG' => __( 'Bottom right dialog', 'merchant-review-connect-for-woocommerce' ),
			'BOTTOM_LEFT_DIALOG'  => __( 'Bottom left dialog', 'merchant-review-connect-for-woocommerce' ),
			'TOP_RIGHT_DIALOG'    => __( 'Top right dialog', 'merchant-review-connect-for-woocommerce' ),
			'TOP_LEFT_DIALOG'     => __( 'Top left dialog', 'merchant-review-connect-for-woocommerce' ),
			'BOTTOM_TRAY'         => __( 'Bottom tray', 'merchant-review-connect-for-woocommerce' ),
		);
		$this->select( 'survey_style', $options );
	}

	public function field_language(): void {
		$options = array(
			''      => __( 'Automatic (browser language)', 'merchant-review-connect-for-woocommerce' ),
			'en'    => 'English',
			'en-US' => 'English (United States)',
			'en-GB' => 'English (United Kingdom)',
			'en-AU' => 'English (Australia)',
			'es'    => 'Español',
			'es-419'=> 'Español (Latinoamérica)',
			'fr'    => 'Français',
			'de'    => 'Deutsch',
			'it'    => 'Italiano',
			'pt-BR' => 'Português (Brasil)',
			'pt-PT' => 'Português',
			'nl'    => 'Nederlands',
			'pl'    => 'Polski',
			'ru'    => 'Русский',
			'ja'    => '日本語',
			'zh-CN' => '中文（简体）',
			'zh-TW' => '中文（繁體）',
			'ar'    => 'العربية',
			'cs'    => 'Čeština',
			'da'    => 'Dansk',
			'no'    => 'Norsk',
			'sv'    => 'Svenska',
			'tr'    => 'Türkçe',
			'id'    => 'Bahasa Indonesia',
			'ms'    => 'Bahasa Melayu',
			'fil'   => 'Filipino',
			'ga'    => 'Gaeilge',
			'af'    => 'Afrikaans',
		);
		$this->select( 'language', $options );
	}

	public function field_delivery_mode(): void {
		$this->select(
			'delivery_mode',
			array(
				'calendar' => __( 'Calendar days', 'merchant-review-connect-for-woocommerce' ),
				'business' => __( 'Business days (Monday–Friday)', 'merchant-review-connect-for-woocommerce' ),
			)
		);
	}

	public function field_delivery_days(): void {
		printf(
			'<input type="number" min="0" max="90" name="%1$s" value="%2$d" />',
			esc_attr( $this->name( 'delivery_days' ) ),
			absint( self::get( 'delivery_days', 7 ) )
		);
	}

	public function field_delivery_meta(): void {
		printf(
			'<input type="text" class="regular-text code" name="%1$s" value="%2$s" placeholder="estimated_delivery_date" />',
			esc_attr( $this->name( 'delivery_date_meta_key' ) ),
			esc_attr( (string) self::get( 'delivery_date_meta_key' ) )
		);
		echo '<p class="description">' . esc_html__( 'Optional. If this order meta field contains a parseable date, it overrides the calculated estimate.', 'merchant-review-connect-for-woocommerce' ) . '</p>';
	}

	public function field_gtin_meta_keys(): void {
		printf(
			'<input type="text" class="regular-text code" name="%1$s" value="%2$s" placeholder="_gtin, _ean" />',
			esc_attr( $this->name( 'custom_gtin_meta_keys' ) ),
			esc_attr( (string) self::get( 'custom_gtin_meta_keys' ) )
		);
		echo '<p class="description">' . esc_html__( 'Comma-separated product meta keys checked in addition to WooCommerce Global Unique ID and common GTIN/EAN fields.', 'merchant-review-connect-for-woocommerce' ) . '</p>';
	}

	public function field_widget_position(): void {
		$this->select(
			'widget_position',
			array(
				'RIGHT_BOTTOM' => __( 'Bottom right', 'merchant-review-connect-for-woocommerce' ),
				'LEFT_BOTTOM'  => __( 'Bottom left', 'merchant-review-connect-for-woocommerce' ),
			)
		);
	}

	public function field_widget_margins(): void {
		$fields = array(
			'widget_side_margin'          => __( 'Desktop side', 'merchant-review-connect-for-woocommerce' ),
			'widget_bottom_margin'        => __( 'Desktop bottom', 'merchant-review-connect-for-woocommerce' ),
			'widget_mobile_side_margin'   => __( 'Mobile side', 'merchant-review-connect-for-woocommerce' ),
			'widget_mobile_bottom_margin' => __( 'Mobile bottom', 'merchant-review-connect-for-woocommerce' ),
		);
		echo '<div class="mrcwc-margin-grid">';
		foreach ( $fields as $key => $label ) {
			printf(
				'<label>%1$s <input type="number" min="0" max="500" name="%2$s" value="%3$d" /> px</label>',
				esc_html( $label ),
				esc_attr( $this->name( $key ) ),
				absint( self::get( $key ) )
			);
		}
		echo '</div>';
	}

	private function select( string $key, array $options ): void {
		$current = (string) self::get( $key );
		printf( '<select name="%s">', esc_attr( $this->name( $key ) ) );
		foreach ( $options as $value => $label ) {
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( (string) $value ),
				selected( $current, (string) $value, false ),
				esc_html( (string) $label )
			);
		}
		echo '</select>';
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage these settings.', 'merchant-review-connect-for-woocommerce' ) );
		}
		$merchant_id = (string) self::get( 'merchant_id' );
		?>
		<div class="wrap mrcwc-wrap">
			<h1><?php esc_html_e( 'Merchant Review Connect', 'merchant-review-connect-for-woocommerce' ); ?></h1>
			<p class="mrcwc-lead"><?php esc_html_e( 'A direct WooCommerce integration for Google Customer Reviews. No account connection, license key, telemetry, or third-party dashboard is required.', 'merchant-review-connect-for-woocommerce' ); ?></p>

			<div class="mrcwc-status">
				<strong><?php esc_html_e( 'Readiness', 'merchant-review-connect-for-woocommerce' ); ?></strong>
				<span class="<?php echo is_ssl() ? 'ok' : 'bad'; ?>"><?php echo is_ssl() ? esc_html__( 'HTTPS detected', 'merchant-review-connect-for-woocommerce' ) : esc_html__( 'HTTPS required on checkout confirmation', 'merchant-review-connect-for-woocommerce' ); ?></span>
				<span class="<?php echo '' !== $merchant_id ? 'ok' : 'warn'; ?>"><?php echo '' !== $merchant_id ? esc_html__( 'Merchant ID configured', 'merchant-review-connect-for-woocommerce' ) : esc_html__( 'Merchant ID still needed', 'merchant-review-connect-for-woocommerce' ); ?></span>
				<span class="ok"><?php echo esc_html( sprintf( __( 'WooCommerce %s', 'merchant-review-connect-for-woocommerce' ), defined( 'WC_VERSION' ) ? WC_VERSION : '' ) ); ?></span>
			</div>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'mrcwc_group' );
				do_settings_sections( 'mrcwc' );
				submit_button();
				?>
			</form>

			<div class="mrcwc-notes">
				<h2><?php esc_html_e( 'Implementation notes', 'merchant-review-connect-for-woocommerce' ); ?></h2>
				<ul>
					<li><?php esc_html_e( 'The survey integration runs only on a valid WooCommerce order-confirmation request.', 'merchant-review-connect-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'Customer email, delivery country, order number and estimated delivery date are generated from the WooCommerce order at checkout completion.', 'merchant-review-connect-for-woocommerce' ); ?></li>
					<li><?php esc_html_e( 'The only remote scripts loaded by this plugin are Google scripts needed for the survey or store widget when those features are enabled.', 'merchant-review-connect-for-woocommerce' ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}
}
