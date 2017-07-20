<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Page_Dashboard extends WooCommerce_Grow_Pages {
	public function __construct() {
		$this->id    = 'dashboard';
		$this->title = 'Dashboard';

		add_filter( 'woocommerce_grow_page_tabs_array', array( $this, 'add_page' ), 10 );
		add_action( 'woocommerce_grow_page_content_' . $this->id, array( $this, 'page_content' ) );
	}

	/**
	 * Outputs page content
	 *
	 * @since 1.0
	 */
	public function page_content() {
		$current_month         = date( 'm', time() );
		$current_year          = date( 'Y', time() );
		$currency_symbol       = $this->get_currency_symbol();
		$passed_months         = WooCommerce_Grow_Helpers::get_history_months();
		$passed_months_display = array_slice( $passed_months, 0, $this->get_display_history_months() );

		try {
			$this->validate_page_before_loading();

			$this->load_scripts();
			include 'views/html-page-dashboard.php';
		}
		catch ( Exception $e ) {
			echo '<div class="not-available_notice">
				<img src="' . WooCommerce_Grow::get_plugin_url() . '/assets/img/growl_med.png"/>
				<p>
					<div class="grow-welcome-text">
						<h2>'. __('Welcome to Grow with WooCommerce', 'woocommerce-grow') .'</h2>
						' . $e->getMessage() . '
					</div>
				</p>
			</div>';
		}
	}

	/**
	 * Validate the page to make sure we can load it safely.
	 *
	 * @since 1.0.2
	 *
	 * @throws Exception
	 */
	public function validate_page_before_loading() {
		$auth_code       = WooCommerce_Grow_Helpers::get_setting( 'authorization_code' );
		$ga              = WooCommerce_Grow_Google_Analytics::get_instance();

		if ( empty( $auth_code ) ) {
			// Checked that we have an Auth Code set in the settings
			throw new Exception (
				sprintf(
					__( 'This page is not available. Please %sclick here%s to integrate your Google Analytics account.', 'woocommerce-grow' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=woocommerce_grow' ) . '">',
					'</a>'
				)
			);
		}

		try {
			// Checked that we have authentication token from Google.
			$ga->is_app_authenticated();
		}
		catch ( Exception $e ) {
			throw new Exception (
				sprintf(
					__( 'This page is not available. There was a problem with the Google authentication token. Error Message: %s.', 'woocommerce-grow' ),
					$e->getMessage()
				) . '
			<br/>
			' . sprintf(
					__( 'Please %sclick here%s double check your Google Authentication Token.', 'woocommerce-grow' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=woocommerce_grow' ) . '">',
					'</a>'
				)
			);
		}

		$ga_uid = WooCommerce_Grow_Helpers::get_setting( 'ga_api_uid' );
		if ( empty( $ga_uid ) ) {
			throw new Exception(
				sprintf(
					__( 'This page is not available. The Google Account User ID is not set. Please visit %ssettings page%s to set the Google UID.', 'woocommerce-grow' ),
					'<a href="' . WooCommerce_Grow_Helpers::get_plugin_settings_page() . '" target="_blank">',
					'</a>'
				)
			);
		}

		try {
			// Make sure we have a stored ga sessions, as we can't work without them
			if ( ! $ga->has_stored_sessions() ) {
				if ( false === $ga->set_sessions() ) {
					throw new Exception ( __( 'Could not save the sessions in the database.', 'woocommerce-grow' ) );
				};
			}
		}
		catch ( Exception $e ) {
			throw new Exception (
				sprintf(
					__( 'This page is not available. There was a retrievil your GA Sessions. Error Message: %s.', 'woocommerce-grow' ),
					$e->getMessage()
				) . '
			<br/>
			' . sprintf(
					__( 'Please %sclick here%s double check your Google Authentication Token.', 'woocommerce-grow' ),
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=woocommerce_grow' ) . '">',
					'</a>'
				)
			);
		}
	}

	/**
	 * Load Page scripts
	 *
	 * @since 1.0
	 */
	public function load_scripts() {
		$scripts = new WooCommerce_Grow_Scripts();
		$scripts->enqueue_admin_scripts();
		$scripts->enqueue_dashboard_scripts();
	}

	/**
	 * Sets how many months we should display in the history section of the Dashboard
	 *
	 * Can be modified with the woocommerce_grow_dashboard_history_months filter
	 *
	 * @since 1.0
	 *
	 * @return mixed|void
	 */
	public function get_display_history_months() {
		return apply_filters( 'woocommerce_grow_dashboard_history_months', 3 );
	}
}