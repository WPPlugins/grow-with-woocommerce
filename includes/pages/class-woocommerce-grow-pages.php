<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Pages class.
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WooCommerce_Grow_Pages {

	protected $id;
	protected $title;
	private $monthly_targets = array();
	private $targets_type = '';
	private $current_target_months = array();
	private $stored_sessions = null;

	public function __construct() {
		add_filter( 'woocommerce_grow_page_tabs_array', array( $this, 'add_page' ), 10 );
		add_action( 'woocommerce_grow_save_page_settings_' . $this->id, array( $this, 'save_page_settings' ) );
		add_action( 'woocommerce_grow_page_content_' . $this->id, array( $this, 'page_content' ) );
	}

	/**
	 * Add pages
	 *
	 * @since 1.0
	 *
	 * @param $pages
	 *
	 * @return mixed
	 */
	public function add_page( $pages ) {
		$pages[ $this->id ] = $this->title;

		return $pages;
	}

	/**
	 * Generic content. Should be extended to load the page content
	 *
	 * @since 1.0
	 */
	public function page_content() {
		echo '<p>' . __( 'Generic page content', 'woocommerce-grow' ) . '</p>';
	}

	/**
	 * Validate the page before loading
	 *
	 * @since 1.0.2
	 */
	public function validate_page_before_loading() {
	}

	/**
	 * Save page settings
	 *
	 * @since 1.0
	 */
	public function save_page_settings() {
	}

	/**
	 * Loading page scripts
	 *
	 * @since 1.0
	 */
	public function load_scripts() {
	}

	/**
	 * Verify Request origins
	 *
	 * @since 1.0
	 *
	 * @param string $wpnonce
	 * @param string $action
	 */
	public function verify_request( $wpnonce, $action ) {
		if ( ! wp_verify_nonce( $wpnonce, $action ) ) {
			die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-grow' ) );
		}
	}

	/**
	 * Get monthly targets
	 *
	 * @since 1.0
	 *
	 * @return mixed|void
	 */
	public function get_monthly_targets() {
		if ( empty( $this->monthly_targets ) ) {
			$this->monthly_targets = WooCommerce_Grow_Helpers::get_option( 'monthly_targets', array() );
		}

		return $this->monthly_targets;
	}

	/**
	 * Returns the set targets type.
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_targets_type() {
		if ( '' === $this->targets_type ) {
			$this->targets_type = WooCommerce_Grow_Helpers::get_option( 'targets_type', 'month_on_month' );
		}

		return $this->targets_type;
	}

	/**
	 * Get target months
	 *
	 * @since 1.0
	 *
	 * @return mixed|void
	 */
	public function get_target_months() {
		if ( empty( $this->current_target_months ) ) {
			$this->current_target_months = WooCommerce_Grow_Helpers::get_option( 'current_target_months', array() );
		}

		return $this->current_target_months;
	}

	/**
	 * Get passed target months.
	 *
	 * @since 1.0
	 *
	 * @param array $target_months
	 * @param int   $current_month
	 * @param int   $current_year
	 *
	 * @return array
	 */
	public function get_passed_target_months( $target_months, $current_month, $current_year ) {
		return WooCommerce_Grow_Helpers::get_passed_target_months( $target_months, $current_month, $current_year );
	}

	/**
	 * Get currency symbol
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_currency_symbol() {
		return get_woocommerce_currency_symbol();
	}

	/**
	 * @return mixed
	 */
	public function get_stored_sessions() {
		if ( null === $this->stored_sessions ) {
			$this->stored_sessions = WooCommerce_Grow_Helpers::get_transient( 'stored_sessions' );
		}

		return $this->stored_sessions;
	}
}