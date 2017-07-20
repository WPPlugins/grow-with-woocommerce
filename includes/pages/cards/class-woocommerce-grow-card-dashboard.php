<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for generating Dashboard cards
 *
 * @since  1.1
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WooCommerce_Grow_Card_Dashboard extends WooCommerce_Grow_Card {

	public $is_current_month = false;
	public $target_month = '';
	public $target_year = '';
	public $target_sessions = '';
	public $target_cr = '';
	public $target_aov = '';
	public $target_revenue = '';
	public $target_orders = '';
	public $sessions_percentage = '';
	public $sessions_growth_color = '';
	public $month_name = '';
	public $revenue = '';
	public $revenue_percentage = '';
	public $revenue_percentage_bar = '';
	public $orders = '';
	public $orders_percentage = '';
	public $orders_percentage_bar = '';
	public $cr = '';
	public $cr_percentage = '';
	public $cr_growth_color = '';
	public $aov = '';
	public $aov_percentage = '';
	public $aov_growth_color = '';
	public $track_ratio = '';
	public $track_message = '';
	public $track_icon = '';
	public $track_color = '';
	public $currency_symbol = '';
	public $string_actual_metric = '';
	public $string_target_metric = '';
	public $reports;
	public $sessions;

	public function __construct( $month, $year ) {
		parent::__construct( $month, $year );

		$this->is_current_month = $this->check_is_current_month();
	}

	/**
	 * Sets the targets data for the dashboard cards
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
	 * Sets card data for the dashboard cards
	 *
	 * @since 1.1
	 * @throws Exception
	 */
	public function set_card_data() {
		// Get the Days in text
		$days_till_end_of_month = WooCommerce_Grow_Helpers::get_days_till_end_of_month( $this->month, $this->year );
		if ( $this->is_current_month ) {
			$this->month_name = $this->get_days_in_text( $days_till_end_of_month );
		} else {
			$this->month_name = date( 'M, Y', strtotime( $this->year . '-' . $this->month ) );
		}

		// Get sessions data
		$ga             = WooCommerce_Grow_Google_Analytics::get_instance();
		$this->sessions = $ga->get_month_sessions_from_storage( $this->month, $this->year );

		// Calculate sessions growth percentage
		$this->sessions_percentage   = abs( WooCommerce_Grow_Helpers::calculate_percentage_of( $this->sessions, $this->target_sessions ) );
		$this->sessions_growth_color = 0 < $this->sessions_percentage ? 'wc-grow-color-green' : 'wc-grow-color-red';

		// Load Revenue Reports
		$reports = new WooCommerce_Grow_Report_Revenue();
		$reports->load_wc_reports_for_month( $this->month, $this->year );

		$this->currency_symbol        = $this->get_currency_symbol();
		$this->revenue                = $reports->get_wc_total_sales();
		$this->revenue_percentage     = abs( WooCommerce_Grow_Helpers::calculate_percentage_of( $this->revenue, $this->target_revenue ) );
		$this->revenue_percentage_bar = 100 < $this->revenue_percentage ? 100 : $this->revenue_percentage;
		$this->orders                 = WooCommerce_Grow_Helpers::get_wc_total_orders( $reports );
		$this->orders_percentage      = abs( WooCommerce_Grow_Helpers::calculate_percentage_of( $this->orders, $this->target_orders ) );
		$this->orders_percentage_bar  = 100 < $this->orders_percentage ? 100 : $this->orders_percentage;
		$this->cr                     = WooCommerce_Grow_Helpers::calculate_cr( $this->orders, $this->sessions );
		$this->cr_percentage          = abs( WooCommerce_Grow_Helpers::calculate_percentage_of( $this->cr, $this->target_cr ) );
		$this->cr_growth_color        = 0 < $this->cr_percentage ? 'wc-grow-color-green' : 'wc-grow-color-red';
		$this->aov                    = WooCommerce_Grow_Helpers::calculate_aov( $this->revenue, $this->orders );
		$this->aov_percentage         = abs( WooCommerce_Grow_Helpers::calculate_percentage_of( $this->aov, $this->target_aov ) );
		$this->aov_growth_color       = 0 < $this->aov_percentage ? 'wc-grow-color-green' : 'wc-grow-color-red';

		$this->track_ratio = WooCommerce_Grow_Helpers::calculate_track_ratio( $this->year, $this->month, $this->target_revenue, $days_till_end_of_month, $this->revenue );
		if ( 1 < $this->track_ratio ) {
			$this->track_message = $this->string_on_target_message();
			$this->track_icon    = 'dashicons-arrow-up-alt';
			$this->track_color   = 'wc-grow-color-green';
		} else {
			$this->track_message = $this->string_missed_target_message();
			$this->track_icon    = 'dashicons-arrow-down-alt';
			$this->track_color   = 'wc-grow-color-red';
		}

		$this->string_actual_metric = __( 'Actual Metric', 'woocommerce-grow' );
		$this->string_target_metric = __( 'Target Metric', 'woocommerce-grow' );
	}

	/**
	 * Default value for sessions.
	 * Extend to return the card sessions.
	 *
	 * @since 1.1
	 *
	 * @return int|float
	 */
	public function get_target_sessions() {
		return 1;
	}

	/**
	 * Default value for CR.
	 * Extend to return the card CR.
	 *
	 * @since 1.1
	 *
	 * @return int|float
	 */
	public function get_target_cr() {
		return 0.01;
	}

	/**
	 * Default value for AOV.
	 * Extend to return the card AOV.
	 *
	 * @since 1.1
	 *
	 * @return int|float
	 */
	public function get_target_aov() {
		return 0.01;
	}

	/**
	 * Default value for Revenue.
	 * Extend to return the card Revenue.
	 *
	 * @since 1.1
	 *
	 * @return int|float
	 */
	public function get_target_revenue() {
		return 1;
	}

	/**
	 * Default value for Orders.
	 * Extend to return the card Orders.
	 *
	 * @since 1.1
	 *
	 * @return int|float
	 */
	public function get_target_orders() {
		return 1;
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
			$this->target_month = $this->month;
		}

		return $this->target_month;
	}

	/**
	 * Sets the target year
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
			$this->target_year = $this->year;
		}

		return $this->target_year;
	}

	/**
	 * Returns the current month days into the month text
	 *
	 * @since 1.1
	 *
	 * @param $days_till_end_of_month
	 *
	 * @return string
	 */
	function get_days_in_text( $days_till_end_of_month ) {
		if ( 0 === $days_till_end_of_month ) {
			$date_in_text = __( 'Last day', 'woocommerce-grow' );
		} else {
			$date_in_text = $days_till_end_of_month . ' ' . __( 'days left', 'woocommerce-grow' );
		}

		return $date_in_text;
	}

	/**
	 * Returns the string for on target message, on the card.
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	private function string_on_target_message() {
		if ( $this->is_current_month ) {
			$string = __( 'On Target!', 'woocommerce-grow' );
		} else {
			$string = __( 'Hit Target!', 'woocommerce-grow' );
		}

		return $string;
	}

	/**
	 * Returns the string for the missed target message on the card
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function string_missed_target_message() {
		if ( $this->is_current_month ) {
			$string = __( 'Missing Target', 'woocommerce-grow' );
		} else {
			$string = __( 'Missed Target', 'woocommerce-grow' );
		}

		return $string;
	}

	/**
	 * Checks, if the processing month is the current month
	 *
	 * @since 1.1
	 *
	 * @return bool
	 */
	public function check_is_current_month() {
		$current_month    = date( 'm', time() );
		$current_year     = date( 'Y', time() );
		$is_current_month = $this->month == $current_month && $this->year == $current_year;

		return $is_current_month;
	}

	/**
	 * Sets the report data for the target month.
	 * Loads the data once for the month and saves DB calls.
	 *
	 * @since 1.1
	 *
	 * @return WooCommerce_Grow_Report_Revenue
	 */
	public function get_reports_data_for_target_month() {
		if ( null === $this->reports ) {
			// Load Revenue Reports
			$this->reports = new WooCommerce_Grow_Report_Revenue();
			$this->reports->load_wc_reports_for_month( $this->target_month, $this->target_year );
		}

		return $this->reports;
	}
}