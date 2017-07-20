<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register and enqueue the plugin scripts
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Scripts {
	/**
	 * Register admin scripts.
	 * Here should be registered scripts that go to all plugin core pages.
	 *
	 * @since 1.0
	 */
	public function register_admin_scripts() {
		wp_register_script( 'wc-grow-admin', WooCommerce_Grow::get_plugin_url() . '/assets/js/admin.js', array( 'jquery' ), WooCommerce_Grow::VERSION, true );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @since 1.0
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'wc-grow-admin' );
	}

	/**
	 * Register Dashboard page scripts.
	 *
	 * @since 1.0
	 */
	public function register_dashboard_scripts() {
		wp_register_script( 'wc-grow-dashboard', WooCommerce_Grow::get_plugin_url() . '/assets/js/dashboard.js', array( 'jquery' ), WooCommerce_Grow::VERSION, true );
	}

	/**
	 * Enqueue Dashboard page scripts.
	 *
	 * @since 1.0
	 */
	public function enqueue_dashboard_scripts() {
		wp_enqueue_script( 'wc-grow-dashboard' );
	}

	/**
	 * Register Dashboard page localized scripts.
	 *
	 * @since 1.0
	 */
	public function register_dashboard_localized_scripts() {
		$data = array(
			'security'     => wp_create_nonce( 'woocommerce-grow-dashboard' ),
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'firstLoad' => 'new' === $first_timer ? '1' : '1',
			
		);
		wp_localize_script( 'wc-grow-dashboard', 'wcGrow', $data );
	}

	/**
	 * Register Dashboard page localized scripts.
	 *
	 * @since 1.0.2
	 */
	public function register_targets_localized_scripts() {
		$first_timer = WooCommerce_Grow_Helpers::get_option( 'user_activation_type', 'new' );

		$data = array(
			'firstLoad' => 'new' === $first_timer ? '1' : '0',
		);

		wp_localize_script( 'wc-grow-targets', 'wcGrow', $data );
	}

	/**
	 * Register Targets page scripts
	 *
	 * @since 1.0
	 */
	public function register_targets_scripts() {
		wp_register_script( 'wc-grow-targets', WooCommerce_Grow::get_plugin_url() . '/assets/js/targets.js', array( 'jquery' ), WooCommerce_Grow::VERSION, true );
	}

	/**
	 * Enqueue Targets page scripts.
	 *
	 * @since 1.0
	 */
	public function enqueue_target_scripts() {
		wp_enqueue_script( 'wc-grow-targets' );
	}

	/**
	 * Register settings page scripts.
	 *
	 * @since 1.0
	 */
	public function register_settings_scripts() {
		wp_register_script( 'wc-grow-settings', WooCommerce_Grow::get_plugin_url() . '/assets/js/settings.js', array( 'jquery' ), WooCommerce_Grow::VERSION, true );
	}

	/**
	 * Add admin styles.
	 * Here should be registered styles that go to all plugin core pages.
	 *
	 * @since 1.0
	 */
	public function add_admin_styles() {
		wp_register_style( 'wc-grow-styles', WooCommerce_Grow::get_plugin_url() . '/assets/css/admin.css', array(), WooCommerce_Grow::VERSION );
		wp_enqueue_style( 'wc-grow-styles' );
	}
}