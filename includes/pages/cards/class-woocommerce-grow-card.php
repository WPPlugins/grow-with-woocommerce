<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to generate monthly cards
 *
 * @since  1.1
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WooCommerce_Grow_Card {

	private $targets_type = '';
	public $month;
	public $year;
	public $monthly_targets = array();
	public $stored_sessions;

	public function __construct( $month, $year ) {
		$this->month = $month;
		$this->year  = $year;
	}

	/**
	 * Outputs the monthly card html
	 *
	 * @since 1.1
	 */
	public function display_card() {
		include( 'views/html-monthly-card.php' );
	}

	/**
	 * Sets targets type to method variable
	 *
	 * @since 1.1
	 */
	public function set_targets_type() {
		$this->targets_type = WooCommerce_Grow_Helpers::get_option( 'targets_type', 'month_on_month' );
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
			$this->set_targets_type();
		}

		return $this->targets_type;
	}

	/**
	 * Returns the stored GA sessions.
	 *
	 * @since 1.1
	 *
	 * @return mixed
	 */
	public function get_stored_sessions() {
		if ( null === $this->stored_sessions ) {
			$this->stored_sessions = WooCommerce_Grow_Helpers::get_transient( 'stored_sessions' );
		}

		return $this->stored_sessions;
	}

	/**
	 * Replaces a zero value with the passed default or 1.
	 *
	 * @since 1.1
	 *
	 * @param     $value
	 * @param int $default
	 *
	 * @return int|float
	 */
	public function no_zero_value( $value, $default = 1 ) {
		if ( 0 < $value ) {
			return $value;
		}

		return $default;
	}

	/**
	 * Returns the saved monthly targets
	 *
	 * @since 1.1
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
	 * Get currency symbol
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_currency_symbol() {
		return get_woocommerce_currency_symbol();
	}

	/**
	 * Returns the Revenue string for the card line title.
	 *
	 * @since 1.1
	 *
	 * @return string|void
	 */
	public function string_revenue_title() {
	    	return __( 'Revenue', 'woocommerce-grow' );
	}

	/**
	 * Returns the Orders string for the card line title.
	 *
	 * @since 1.1
	 *
	 * @return string|void
	 */
	public function string_orders_title() {
	    	return __( 'Orders', 'woocommerce-grow' );
	}

	/**
	 * Returns the Sessions string for the card line title.
	 *
	 * @since 1.1
	 *
	 * @return string|void
	 */
	public function string_sessions_title() {
	    	return __( 'Sessions', 'woocommerce-grow' );
	}

	/**
	 * Returns the CR string for the card line title.
	 *
	 * @since 1.1
	 *
	 * @return string|void
	 */
	public function string_cr_title() {
	    	return __( 'CR', 'woocommerce-grow' );
	}

	/**
	 * Returns the AOV string for the card line title.
	 *
	 * @since 1.1
	 *
	 * @return string|void
	 */
	public function string_aov_title() {
	    	return __( 'AOV', 'woocommerce-grow' );
	}


}