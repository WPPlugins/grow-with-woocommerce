<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Admin_Report' ) ) {
	include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );
}

/**
 * Helper class to aid in setting up reports.
 * Extends WC_Admin_Report
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Report extends WC_Admin_Report {

	public $chart_colours = array();

	/**
	 * Prepares month dates and parameter, then requests the WC_Report_Sales_By_Date reports object
	 *
	 * @param int $month
	 * @param int $year
	 *
	 * @return WC_Report_Sales_By_Date
	 */
	public function load_wc_reports_for_month( $month, $year ) {
		$start_date = $this->get_first_of_the_month( $month, $year );
		$end_date   = $this->get_last_of_the_month( $month, $year );

		$filters = array(
			'date_min' => $start_date,
			'date_max' => $end_date
		);

		$this->setup_wc_reports( $filters );
	}

	/**
	 * Load WC Reports given the start and end date
	 *
	 * @since 1.0
	 *
	 * @param string $start_date
	 * @param string $end_date
	 */
	public function load_wc_reports( $start_date, $end_date ) {
		$filters = array(
			'date_min' => $start_date,
			'date_max' => $end_date
		);

		$this->setup_wc_reports( $filters );
	}

	/**
	 * Setup and return the WC_Admin_Report object for a set period of time
	 *
	 * @param $filter
	 */
	public function setup_wc_reports( $filter ) {
		$default_args = array(
			'period'   => '',
			'date_min' => date( 'Y-m-d', strtotime( '-1 week' ) ),
			'date_max' => date( 'Y-m-d', time() )
		);

		$filter = wp_parse_args( $filter, $default_args );

		if ( empty( $filter['period'] ) ) {
			$filter['period'] = 'custom';

			if ( ! empty( $filter['date_min'] ) || ! empty( $filter['date_max'] ) ) {
				$_GET['start_date'] = isset( $filter['date_min'] ) ? $filter['date_min'] : null;
				$_GET['end_date']   = isset( $filter['date_max'] ) ? $filter['date_max'] : null;

			} else {
				$_GET['start_date'] = $_GET['end_date'] = date( 'Y-m-d', current_time( 'timestamp' ) );
			}
		} else {
			if ( ! in_array( $filter['period'], array( 'week', 'month', 'last_month', 'year' ) ) ) {
				$filter['period'] = 'week';
			}

			if ( 'week' === $filter['period'] ) {
				$filter['period'] = '7day';
			}
		}

		$this->calculate_current_range( $filter['period'] );
	}

	/**
	 * Returns the first date of the given month
	 *
	 * @since 1.0
	 *
	 * @param int $month
	 * @param int $year
	 *
	 * @return bool|string
	 */
	public function get_first_of_the_month( $month, $year ) {
		return date( 'Y-m-01', mktime( 0, 0, 0, $month, 1, $year ) );
	}

	/**
	 * Returns the last date of the given month
	 *
	 * @since 1.0
	 *
	 * @param int $month
	 * @param int $year
	 *
	 * @return bool|string
	 */
	public function get_last_of_the_month( $month, $year ) {
		return date( 'Y-m-t', mktime( 0, 0, 0, $month, 1, $year ) );
	}

}