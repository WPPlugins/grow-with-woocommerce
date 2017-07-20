<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prepare and display Revenue report graph
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Report_Revenue extends WooCommerce_Grow_Report {

	private $report_data;

	/**
	 * Get the graph of revenue/target revenue, in the specified range
	 *
	 * @param string $range The range of the graph, it can be 'monthly', 'quarterly', 'yearly'.
	 */
	public function output_graph( $range = 'quarterly' ) {
		$this->chart_colours = array(
			'revenue_amount'        => '#CA0103',
			'target_revenue_amount' => '#6699cc',
		);
		$this->setup_chart_data( $range );
		$targets_type        = WooCommerce_Grow_Helpers::get_targets_type();
		$target_type_options = WooCommerce_Grow_Helpers::get_target_type_options();

		include( 'views/html-report-wrapper.php' );
	}

	/**
	 * Queries the WC report data and returns the results
	 *
	 * @since 1.0
	 * @return mixed
	 */
	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}

		return $this->report_data;
	}

	/**
	 * Performs all processes needed to prepare and retrieve the report data.
	 * Sets:
	 *        1. The start and end date, based on the range given
	 *        2. Retrieves and sets the WC report data
	 *        3. Retrieves and sets the Target revenue data
	 *
	 * @since 1.0
	 *
	 * @param string $range Time range for the graph
	 */
	public function setup_chart_data( $range ) {
		$range_dates = $this->get_start_and_end_dates_from_range( $range );
		$this->load_wc_reports( $range_dates['start_date'], $range_dates['end_date'] );
		$this->get_report_data();
		$this->get_target_revenue_data( $range );
	}

	/**
	 * Retrieves the saved target revenue for the given month
	 *
	 * @since 1.0
	 *
	 * @param int $month Month (i.e. 06)
	 * @param int $year  Year (i.e. 2015)
	 *
	 * @return string The target revenue
	 */
	public function get_target_revenue_for_month( $month, $year ) {
		$targets = WooCommerce_Grow_Helpers::get_option( 'monthly_targets', array() );

		$target_revenue = isset( $targets['revenue_target'][ $year ][ $month ] ) ? $targets['revenue_target'][ $year ][ $month ] : wc_price( 0 );

		return $target_revenue;
	}

	/**
	 * Sets the WC report data results
	 *
	 * @since 1.0
	 */
	private function query_report_data() {
		$this->report_data = new stdClass;

		$this->report_data->orders = (array) $this->get_order_report_data(
			array(
				'data'                => array(
					'_order_total' => array(
						'type'     => 'meta',
						'function' => 'SUM',
						'name'     => 'total_sales'
					),
					'post_date'    => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_date'
					),
				),
				'group_by'            => $this->group_by_query,
				'order_by'            => 'post_date ASC',
				'query_type'          => 'get_results',
				'filter_range'        => true,
				'order_types'         => array_merge( array( 'shop_order_refund' ), wc_get_order_types( 'sales-reports' ) ),
				'order_status'        => array( 'completed', 'processing', 'on-hold' ),
				'parent_order_status' => array( 'completed', 'processing', 'on-hold' ),
				'nocache'             => true
			)
		);

		$this->report_data->order_counts = (array) $this->get_order_report_data(
			array(
				'data'         => array(
					'ID'        => array(
						'type'     => 'post_data',
						'function' => 'COUNT',
						'name'     => 'count',
						'distinct' => true,
					),
					'post_date' => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_date'
					)
				),
				'group_by'     => $this->group_by_query,
				'order_by'     => 'post_date ASC',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_types'  => wc_get_order_types( 'order-count' ),
				'order_status' => array( 'completed', 'processing', 'on-hold' ),
				'nocache'      => true
			)
		);
	}

	/**
	 * Outputs the chart after all data is retrieved and set to the object
	 *
	 * @since 1.0
	 */
	public function get_main_chart() {
		global $wp_locale;

		$revenue_amounts        = $this->get_revenue_amount_chart_data();
		$target_revenue_amounts = $this->get_revenue_target_amount_chart_data();

		// Encode in json format
		$chart_data = json_encode(
			array(
				'revenue_amounts'        => array_map( array( $this, 'round_chart_totals' ), array_values( $revenue_amounts ) ),
				'target_revenue_amounts' => array_map( array( $this, 'round_chart_totals' ), array_values( $target_revenue_amounts ) ),
			)
		);

		include( 'views/html-chart.php' );
	}

	/**
	 * Determines the start and end data of the report data from the given range.
	 *
	 * @since 1.0
	 *
	 * @param string $range The Range
	 *
	 * @return array
	 */
	public function get_start_and_end_dates_from_range( $range ) {
		// Today's timestamp
		$today_timestamp = strtotime( 'midnight', current_time( 'timestamp' ) );

		// Set defaults
		$range_dates = array(
			'start_date' => date( 'Y-m-d', $today_timestamp ),
			'end_date'   => date( 'Y-m-d', $today_timestamp )
		);

		$passed_target_months = WooCommerce_Grow_Helpers::get_twelve_months_back();
		if ( 'yearly' == $range ) {
			// Get the first month of the monthly targets, this makes it the month furthest from today
			$first_target_month = array_pop( $passed_target_months );

			// Format the start date
			$range_dates['start_date'] = date( 'Y-m-01', strtotime( $first_target_month['year'] . '-' . $first_target_month['month'] ) );
		} elseif ( 'monthly' == $range ) {
			// Start date is the first of the month
			$range_dates['start_date'] = date( 'Y-m-01', $today_timestamp );
		} else {
			// Take the last of the first three months (01 three months ago)
			$last_three       = array_slice( $passed_target_months, 0, 3 );
			$start_date_month = array_pop( $last_three );

			$range_dates['start_date'] = date( 'Y-m-01', strtotime( $start_date_month['year'] . '-' . $start_date_month['month'] ) );
		}

		return $range_dates;
	}

	/**
	 * Sets the target revenue data to the object.
	 * It run all needed processes, calculate the daily or monthly targets and
	 * set the arranged data to the appropriate object variable
	 *
	 * @since 1.0
	 *
	 * @param string $range
	 */
	public function get_target_revenue_data( $range ) {
		$today_timestamp      = strtotime( 'midnight', current_time( 'timestamp' ) );
		$monthly_targets      = WooCommerce_Grow_Helpers::get_option( 'monthly_targets', array() );
		$passed_target_months = WooCommerce_Grow_Helpers::get_twelve_months_back();

		if ( 'yearly' == $range ) {
			foreach ( $passed_target_months as $passed_month ) {
				$month          = WooCommerce_Grow_Helpers::get_field( 'month', $passed_month );
				$year           = WooCommerce_Grow_Helpers::get_field( 'year', $passed_month );
				$target_revenue = $this->get_target_revenue( $month, $year );

				$this->report_data->target->revenue[] = $this->assign_value_and_date_to_object(
					$target_revenue, date( 'Y-m-d', strtotime( $year . '-' . $month ) )
				);
			}
		} elseif ( 'monthly' == $range ) {
			$month          = date( 'm', $today_timestamp );
			$year           = date( 'Y', $today_timestamp );
			$target_revenue = $this->get_target_revenue( $month, $year );

			if ( 'day' == $this->chart_groupby ) {
				$this->prepare_daily_target_revenue_data( $target_revenue, $year, $month );
			} else {
				$this->report_data->target->revenue[] = $this->assign_value_and_date_to_object(
					$target_revenue, date( 'Y-m-d', strtotime( $year . '-' . $month ) )
				);
			}
		} else {
			// Take the last of the first three months (01 three months ago)
			$last_three_months = array_slice( $passed_target_months, 0, 3 );

			foreach ( $last_three_months as $date ) {
				$month          = WooCommerce_Grow_Helpers::get_field( 'month', $date );
				$year           = WooCommerce_Grow_Helpers::get_field( 'year', $date );
				$target_revenue = $this->get_target_revenue( $month, $year );

				if ( 'day' == $this->chart_groupby ) {
					$this->prepare_daily_target_revenue_data( $target_revenue, $year, $month );
				} else {
					$this->report_data->target->revenue[] = $this->assign_value_and_date_to_object(
						$target_revenue, date( 'Y-m-d', strtotime( $year . '-' . $month ) )
					);
				}
			}
		}
	}

	/**
	 * Sets the daily target revenue to the object
	 *
	 * @since 1.0
	 *
	 * @param float $target_revenue
	 * @param int   $year
	 * @param int   $month
	 */
	public function prepare_daily_target_revenue_data( $target_revenue, $year, $month ) {
		$days_of_the_month = date( 't', strtotime( $year . '-' . $month ) );
		$daily_target      = wc_format_decimal( $target_revenue / $days_of_the_month, 2 );

		for ( $i = 1; $i <= $days_of_the_month; $i ++ ) {
			$date                                 = date( 'Y-m-d', strtotime( $year . '-' . $month . '-' . $i ) );
			$this->report_data->target->revenue[] = $this->assign_value_and_date_to_object( $daily_target, $date );
		}
	}

	/**
	 * Returns the prepared revenue chart data.
	 * The data is structured in the manner to be loaded by the graph
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_revenue_amount_chart_data() {
		return $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby );
	}

	/**
	 * Returns the prepared target revenue chart data.
	 * The data is structured in the manner to be loaded by the graph
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_revenue_target_amount_chart_data() {
		return $this->prepare_chart_data( $this->report_data->target->revenue, 'target_date', 'target_revenue', $this->chart_interval, $this->start_date, $this->chart_groupby );
	}

	/**
	 * Round the amounts and structure the array in the correct chart manner
	 *
	 * @since 1.0
	 *
	 * @param float|array $amount
	 *
	 * @return string
	 */
	public function round_chart_totals( $amount ) {
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Returns the WC total sales for the set time period
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_wc_total_sales() {
		$report_data = $this->get_report_data();

		return round( array_sum( wp_list_pluck( $report_data->orders, 'total_sales' ) ) );
	}

	/**
	 * Returns the WC total orders for the set time period
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_wc_total_orders() {
		$report_data = $this->get_report_data();

		return absint( array_sum( wp_list_pluck( $report_data->order_counts, 'count' ) ) );
	}

	/**
	 * Assign the target revenue and the date to an object and return the object
	 *
	 * @since 1.0
	 *
	 * @param float  $target_revenue
	 * @param string $date
	 *
	 * @return stdClass
	 */
	public function assign_value_and_date_to_object( $target_revenue, $date ) {
		$target                 = new stdClass();
		$target->target_revenue = $target_revenue;
		$target->target_date    = $date;

		return $target;
	}

	/**
	 * Returns the target month revenue.
	 *
	 * @param $month Base Month
	 * @param $year  Base Year
	 *
	 * @return string
	 */
	public function get_target_revenue( $month, $year ) {
		$targets_type = WooCommerce_Grow_Helpers::get_targets_type();

		// Get the targets object
		$dashboard    = WooCommerce_Grow_Helpers::get_card( $month, $year, $targets_type );

		// Set the target month and year
		$dashboard->set_target_month( $month, $year );
		$dashboard->set_target_year( $month, $year );

		// Get the target month/year revenue amount
		$revenue = $dashboard->get_target_revenue();

		return $revenue;
	}
}