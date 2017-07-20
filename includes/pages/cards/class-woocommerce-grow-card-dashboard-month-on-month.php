<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prepares and displays Month on Month card data
 *
 * @since  1.1
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Card_Dashboard_Month_On_Month extends WooCommerce_Grow_Card_Dashboard {

	/**
	 * Sets the targets data for the given month-year.
	 *
	 * @since 1.1
	 */
	public function set_targets_data() {
		$this->set_target_month( $this->month, $this->year );
		$this->set_target_year( $this->month, $this->year );

		$this->target_sessions = $this->get_target_sessions();
		$this->target_cr       = $this->get_target_cr();
		$this->target_aov      = $this->get_target_aov();
		$this->target_revenue  = $this->get_target_revenue();
		$this->target_orders   = $this->get_target_orders();
	}

	/**
	 * Returns the target Sessions.
	 *
	 * @since 1.1
	 *
	 * @return float|int
	 */
	public function get_target_sessions() {
		$sessions = $this->get_stored_sessions();
		$value    = WooCommerce_Grow_Helpers::get_field( $this->target_month, WooCommerce_Grow_Helpers::get_field( $this->target_year, $sessions, array() ), 1 );

		return $this->no_zero_value( $value, 1 );
	}

	/**
	 * Returns the target CR.
	 *
	 * @since 1.1
	 *
	 * @return float|int
	 */
	public function get_target_cr() {
		$stored_sessions = $this->get_stored_sessions();
		$sessions        = WooCommerce_Grow_Helpers::get_field( $this->target_month, WooCommerce_Grow_Helpers::get_field( $this->target_year, $stored_sessions, array() ), 1 );
		$reports         = $this->get_reports_data_for_target_month();
		$orders          = WooCommerce_Grow_Helpers::get_wc_total_orders( $reports );
		$value           = WooCommerce_Grow_Helpers::calculate_cr( $orders, $sessions );

		return $this->no_zero_value( $value, 0.01 );
	}

	/**
	 * Returns the target AOV.
	 *
	 * @since 1.1
	 *
	 * @return float|int
	 */
	public function get_target_aov() {
		// Load Revenue Reports
		$reports = $this->get_reports_data_for_target_month();
		$orders  = WooCommerce_Grow_Helpers::get_wc_total_orders( $reports );
		$revenue = $reports->get_wc_total_sales();
		$value   = WooCommerce_Grow_Helpers::calculate_aov( $revenue, $orders );

		return $this->no_zero_value( $value, 0.01 );
	}

	/**
	 * Returns the target revenue.
	 *
	 * @since 1.1
	 *
	 * @return float|int
	 */
	public function get_target_revenue() {
		// Load Revenue Reports
		$reports = $this->get_reports_data_for_target_month();
		$value   = $reports->get_wc_total_sales();

		return $this->no_zero_value( $value, 1 );
	}

	/**
	 * Returns the target orders.
	 *
	 * @since 1.1
	 *
	 * @return float|int
	 */
	public function get_target_orders() {
		// Load Revenue Reports
		$reports = $this->get_reports_data_for_target_month();
		$value   = WooCommerce_Grow_Helpers::get_wc_total_orders( $reports );

		return $this->no_zero_value( $value, 1 );
	}

	/**
	 * Sets the target month.
	 *
	 * @since 1.1
	 *
	 * @param $month
	 * @param $year
	 *
	 * @return string
	 */
	public function set_target_month( $month, $year ) {
		if ( '' === $this->target_month ) {
			$this->target_month = $this->get_month_minus_one_month( $month, $year );
		}

		return $this->target_month;
	}

	/**
	 * Sets the target year.
	 *
	 * @since 1.1
	 *
	 * @param $month
	 * @param $year
	 *
	 * @return string
	 */
	public function set_target_year( $month, $year ) {
		if ( '' === $this->target_year ) {
			$this->target_year = $this->get_year_minus_one_month( $month, $year );
		}

		return $this->target_year;
	}

	/**
	 * Gets the comparison month for Month on Month data.
	 *
	 * @since 1.1
	 *
	 * @param $month
	 * @param $year
	 *
	 * @return bool|string
	 */
	public function get_month_minus_one_month( $month, $year ) {
		return date( 'm', mktime( 0, 0, 0, $month - 1, 1, $year ) );
	}

	/**
	 * Gets the comparison year for Month on Month data.
	 *
	 * @since 1.1
	 *
	 * @param $month
	 * @param $year
	 *
	 * @return bool|string
	 */
	public function get_year_minus_one_month( $month, $year ) {
		return date( 'Y', mktime( 0, 0, 0, $month - 1, 1, $year ) );
	}
}