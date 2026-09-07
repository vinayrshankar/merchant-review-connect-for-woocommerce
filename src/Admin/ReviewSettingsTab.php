<?php
/**
 * Merchant Review Connect for WooCommerce.
 * Copyright (C) 2026 Vinay Shankar. https://tfaworld.org/
 */

namespace VinayShankar\MerchantReviewConnect\Admin;

use VinayShankar\MerchantReviewConnect\Config\OptionRepository;

defined( 'ABSPATH' ) || exit;

final class ReviewSettingsTab {
	const TAB = 'merchant-review-connect';

	private $options;

	public function __construct( OptionRepository $options ) {
		$this->options = $options;
	}

	public function register() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_tab' ), 80 );
		add_action( 'woocommerce_settings_tabs_' . self::TAB, array( $this, 'render' ) );
		add_action( 'woocommerce_update_options_' . self::TAB, array( $this, 'save' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( MRC_PLUGIN_FILE ), array( $this, 'plugin_link' ) );
	}

	public function add_tab( $tabs ) {
		$tabs[ self::TAB ] = __( 'Merchant Reviews', 'merchant-review-connect-for-woocommerce' );
		return $tabs;
	}

	public function plugin_link( $links ) {
		$url = admin_url( 'admin.php?page=wc-settings&tab=' . self::TAB );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'merchant-review-connect-for-woocommerce' ) . '</a>' );
		return $links;
	}

	public function render() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		\WC_Admin_Settings::output_fields( $this->fields() );
	}

	public function save() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		\WC_Admin_Settings::save_fields( $this->fields() );
	}

	private function fields() {
		return array(
			array(
				'title' => __( 'Google Customer Reviews', 'merchant-review-connect-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Connect completed WooCommerce orders directly to Google Customer Reviews. No license key, telemetry, or account broker is used.', 'merchant-review-connect-for-woocommerce' ),
				'id'    => 'mrc_section_reviews',
			),
			array(
				'title'             => __( 'Merchant Center ID', 'merchant-review-connect-for-woocommerce' ),
				'id'                => $this->options->option_name( 'merchant_id' ),
				'type'              => 'text',
				'default'           => $this->options->default_value( 'merchant_id' ),
				'css'               => 'width:260px;',
				'custom_attributes' => array( 'inputmode' => 'numeric', 'pattern' => '[0-9]*' ),
				'desc_tip'          => __( 'The numeric account ID shown in Google Merchant Center.', 'merchant-review-connect-for-woocommerce' ),
			),
			array(
				'title'   => __( 'Survey invitation', 'merchant-review-connect-for-woocommerce' ),
				'id'      => $this->options->option_name( 'survey_enabled' ),
				'type'    => 'checkbox',
				'default' => $this->options->default_value( 'survey_enabled' ),
				'desc'    => __( 'Offer the Google Customer Reviews opt-in on valid order-confirmation pages.', 'merchant-review-connect-for-woocommerce' ),
			),
			array(
				'title'   => __( 'Survey placement', 'merchant-review-connect-for-woocommerce' ),
				'id'      => $this->options->option_name( 'survey_position' ),
				'type'    => 'select',
				'default' => $this->options->default_value( 'survey_position' ),
				'options' => array(
					'CENTER_DIALOG'       => __( 'Center dialog', 'merchant-review-connect-for-woocommerce' ),
					'BOTTOM_RIGHT_DIALOG' => __( 'Bottom right dialog', 'merchant-review-connect-for-woocommerce' ),
					'BOTTOM_LEFT_DIALOG'  => __( 'Bottom left dialog', 'merchant-review-connect-for-woocommerce' ),
					'TOP_RIGHT_DIALOG'    => __( 'Top right dialog', 'merchant-review-connect-for-woocommerce' ),
					'TOP_LEFT_DIALOG'     => __( 'Top left dialog', 'merchant-review-connect-for-woocommerce' ),
					'BOTTOM_TRAY'         => __( 'Bottom tray', 'merchant-review-connect-for-woocommerce' ),
				),
			),
			array(
				'title'   => __( 'Survey language', 'merchant-review-connect-for-woocommerce' ),
				'id'      => $this->options->option_name( 'locale' ),
				'type'    => 'select',
				'default' => '',
				'options' => $this->locale_options(),
			),
			array( 'type' => 'sectionend', 'id' => 'mrc_section_reviews' ),

			array(
				'title' => __( 'Delivery promise', 'merchant-review-connect-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Google requires an estimated delivery date with each opt-in request.', 'merchant-review-connect-for-woocommerce' ),
				'id'    => 'mrc_section_delivery',
			),
			array(
				'title'   => __( 'Calculation', 'merchant-review-connect-for-woocommerce' ),
				'id'      => $this->options->option_name( 'eta_strategy' ),
				'type'    => 'select',
				'default' => $this->options->default_value( 'eta_strategy' ),
				'options' => array(
					'calendar' => __( 'Calendar days', 'merchant-review-connect-for-woocommerce' ),
					'weekdays' => __( 'Monday-Friday weekdays', 'merchant-review-connect-for-woocommerce' ),
				),
			),
			array(
				'title'             => __( 'Days after order', 'merchant-review-connect-for-woocommerce' ),
				'id'                => $this->options->option_name( 'eta_days' ),
				'type'              => 'number',
				'default'           => $this->options->default_value( 'eta_days' ),
				'custom_attributes' => array( 'min' => '0', 'max' => '90', 'step' => '1' ),
			),
			array(
				'title'    => __( 'Exact-date order meta key', 'merchant-review-connect-for-woocommerce' ),
				'id'       => $this->options->option_name( 'eta_meta_key' ),
				'type'     => 'text',
				'default'  => '',
				'desc_tip' => __( 'Optional. A parseable order-meta date overrides the calculated date.', 'merchant-review-connect-for-woocommerce' ),
			),
			array( 'type' => 'sectionend', 'id' => 'mrc_section_delivery' ),

			array(
				'title' => __( 'Product identifiers', 'merchant-review-connect-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'Send valid GTINs with the order so Google can associate eligible product reviews.', 'merchant-review-connect-for-woocommerce' ),
				'id'    => 'mrc_section_products',
			),
			array(
				'title'   => __( 'Product GTINs', 'merchant-review-connect-for-woocommerce' ),
				'id'      => $this->options->option_name( 'product_ids_enabled' ),
				'type'    => 'checkbox',
				'default' => $this->options->default_value( 'product_ids_enabled' ),
				'desc'    => __( 'Include validated GTIN-8, GTIN-12, GTIN-13, or GTIN-14 values.', 'merchant-review-connect-for-woocommerce' ),
			),
			array(
				'title'    => __( 'Extra product meta keys', 'merchant-review-connect-for-woocommerce' ),
				'id'       => $this->options->option_name( 'product_meta_keys' ),
				'type'     => 'text',
				'default'  => '',
				'desc_tip' => __( 'Comma-separated metadata keys checked after WooCommerce product identifiers.', 'merchant-review-connect-for-woocommerce' ),
			),
			array( 'type' => 'sectionend', 'id' => 'mrc_section_products' ),

			array(
				'title' => __( 'Google store widget', 'merchant-review-connect-for-woocommerce' ),
				'type'  => 'title',
				'desc'  => __( 'The optional Google store widget can display store-rating and store-quality information when your store is eligible.', 'merchant-review-connect-for-woocommerce' ),
				'id'    => 'mrc_section_widget',
			),
			array(
				'title'   => __( 'Store widget', 'merchant-review-connect-for-woocommerce' ),
				'id'      => $this->options->option_name( 'widget_enabled' ),
				'type'    => 'checkbox',
				'default' => $this->options->default_value( 'widget_enabled' ),
				'desc'    => __( 'Load Google\'s store widget on storefront pages.', 'merchant-review-connect-for-woocommerce' ),
			),
			array(
				'title'   => __( 'Desktop position', 'merchant-review-connect-for-woocommerce' ),
				'id'      => $this->options->option_name( 'widget_position' ),
				'type'    => 'select',
				'default' => $this->options->default_value( 'widget_position' ),
				'options' => array(
					'RIGHT_BOTTOM' => __( 'Bottom right', 'merchant-review-connect-for-woocommerce' ),
					'LEFT_BOTTOM'  => __( 'Bottom left', 'merchant-review-connect-for-woocommerce' ),
				),
			),
			$this->number_field( 'widget_side', __( 'Desktop side margin', 'merchant-review-connect-for-woocommerce' ) ),
			$this->number_field( 'widget_bottom', __( 'Desktop bottom margin', 'merchant-review-connect-for-woocommerce' ) ),
			$this->number_field( 'widget_mobile_side', __( 'Mobile side margin', 'merchant-review-connect-for-woocommerce' ) ),
			$this->number_field( 'widget_mobile_bottom', __( 'Mobile bottom margin', 'merchant-review-connect-for-woocommerce' ) ),
			array( 'type' => 'sectionend', 'id' => 'mrc_section_widget' ),
		);
	}

	private function number_field( $key, $title ) {
		return array(
			'title'             => $title,
			'id'                => $this->options->option_name( $key ),
			'type'              => 'number',
			'default'           => $this->options->default_value( $key ),
			'desc'              => __( 'pixels', 'merchant-review-connect-for-woocommerce' ),
			'custom_attributes' => array( 'min' => '0', 'max' => '500', 'step' => '1' ),
		);
	}

	private function locale_options() {
		return array(
			''      => __( 'Automatic (browser language)', 'merchant-review-connect-for-woocommerce' ),
			'af'    => 'Afrikaans',
			'ar'    => 'العربية',
			'cs'    => 'Čeština',
			'da'    => 'Dansk',
			'de'    => 'Deutsch',
			'en'    => 'English',
			'en-AU' => 'English (Australia)',
			'en-GB' => 'English (United Kingdom)',
			'en-US' => 'English (United States)',
			'es'    => 'Español',
			'es-419'=> 'Español (Latinoamérica)',
			'fil'   => 'Filipino',
			'fr'    => 'Français',
			'ga'    => 'Gaeilge',
			'id'    => 'Bahasa Indonesia',
			'it'    => 'Italiano',
			'ja'    => '日本語',
			'ms'    => 'Bahasa Melayu',
			'nl'    => 'Nederlands',
			'no'    => 'Norsk',
			'pl'    => 'Polski',
			'pt-BR' => 'Português (Brasil)',
			'pt-PT' => 'Português',
			'ru'    => 'Русский',
			'sv'    => 'Svenska',
			'tr'    => 'Türkçe',
			'zh-CN' => '中文（简体）',
			'zh-TW' => '中文（繁體）',
		);
	}
}
