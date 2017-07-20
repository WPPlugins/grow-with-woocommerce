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
class WooCommerce_Grow_Helpers {

	/**
	 * WC Logger object
	 * @var object
	 */
	private static $log;

	/**
	 * Do we have debug mode enabled
	 * @var bool
	 */
	private static $is_debug_enabled;

	/**
	 * Holds the plugins settings
	 *
	 * @var null
	 */
	private static $settings;

	private static $targets_type;

	/**
	 * Returns an array of the next 12 months in the format [M, Y] Month, Year
	 *
	 * @return array
	 */
	public static function get_twelve_months_ahead() {
		$current_month = date( 'n' );
		for ( $x = 0; $x < 12; $x ++ ) {
			$months[] = array(
				'month' => date( 'm', mktime( 0, 0, 0, $current_month + $x, 1 ) ),
				'year'  => date( 'Y', mktime( 0, 0, 0, $current_month + $x, 1 ) )
			);
		}

		return $months;
	}

	public static function get_twelve_months_back() {
		$current_month = date( 'n' );
		for ( $x = 1; $x < 12; $x ++ ) {
			$months[] = array(
				'month' => date( 'm', mktime( 0, 0, 0, $current_month - $x, 1 ) ),
				'year'  => date( 'Y', mktime( 0, 0, 0, $current_month - $x, 1 ) )
			);
		}

		return $months;
	}

	/**
	 * Return the value of a given array key
	 *
	 * @since 1.0
	 *
	 * @param string $name    Name of the field.
	 * @param array  $array   The array we are looking for the field in.
	 * @param mixed  $default (Optional) Default value, if the field was not found. Defaults to NULL.
	 *
	 * @return mixed The value of the matched field OR the default value
	 */
	public static function get_field( $name, array $array, $default = null ) {
		return isset( $array[ $name ] ) ? $array[ $name ] : $default;
	}

	/**
	 * Add debug log message
	 *
	 * @since 1.0
	 *
	 * @param string $message
	 */
	public static function add_debug_log( $message ) {
		if ( ! is_object( self::$log ) ) {
			self::$log = new WC_Logger();
		}

		if ( self::is_debug_enabled() ) {
			self::$log->add( 'wc_grow', $message );
		}
	}

	/**
	 * Check, if debug logging is enabled
	 *
	 * @since 1.0
	 * @return bool
	 */
	public static function is_debug_enabled() {
		if ( null === self::$is_debug_enabled ) {
			self::$is_debug_enabled = 'yes' == WooCommerce_Grow_Helpers::get_setting( 'debug_enabled', 'old', 'no' ) ? true : false;
		}

		return self::$is_debug_enabled;
	}

	/**
	 * Update option with the Grow prefix
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @param null   $autoload
	 *
	 * @return bool
	 */
	public static function update_option( $name, $value, $autoload = null ) {
		return update_option( WooCommerce_Grow::PREFIX . $name, $value, $autoload );
	}

	/**
	 * Get option from the WP database.
	 * Will use the plugin options prefix to retrieve the option
	 *
	 * @since 1.0
	 *
	 * @param string $name    The option name
	 * @param mixed  $default Default value returned, if the option is not set
	 *
	 * @return mixed|void
	 */
	public static function get_option( $name, $default = false ) {
		return get_option( WooCommerce_Grow::PREFIX . $name, $default );
	}

	/**
	 * Sets transient with the Grow prefix.
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @param int    $time
	 *
	 * @return bool
	 */
	public static function set_transient( $name, $value, $time = 0 ) {
		return set_transient( WooCommerce_Grow::PREFIX . $name, $value, $time );
	}

	/**
	 * Gets a transient with the Grow prefix
	 *
	 * @since 1.0
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function get_transient( $name ) {
		return get_transient( WooCommerce_Grow::PREFIX . $name );
	}

	/**
	 * Deletes a transient with the Grow prefix
	 *
	 * @since 1.1
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public static function delete_transient( $name ) {
		return delete_transient( WooCommerce_Grow::PREFIX . $name );
	}

	/**
	 * Returns the settings page url, under WooCommerce > Settings > Integrations
	 *
	 * @return string|void
	 */
	public static function get_plugin_settings_page() {
		return admin_url( 'admin.php?page=wc-settings&tab=integration&section=woocommerce_grow' );
	}

	/**
	 * Returns the Grow page under the WooCommerce menu.
	 *
	 * @param string $tab The tab of the page needed. Default: dashboard
	 *
	 * @return string|void
	 */
	public static function get_grow_page_url( $tab = 'dashboard' ) {
		return admin_url( sprintf( 'admin.php?page=woocommerce-grow&tab=%s', $tab ) );
	}

	/**
	 * Get the total sales from the Revenue Reports
	 *
	 * @param WooCommerce_Grow_Report_Revenue $reports
	 *
	 * @return string
	 */
	public static function get_wc_total_sales( WooCommerce_Grow_Report_Revenue $reports ) {
		$report_data = $reports->get_report_data();

		return wc_format_decimal( array_sum( wp_list_pluck( $report_data->orders, 'total_sales' ) ), 2 );
	}

	/**
	 * Get the total orders from the Revenue Reports
	 *
	 * @param WooCommerce_Grow_Report_Revenue $reports
	 *
	 * @return int
	 */
	public static function get_wc_total_orders( WooCommerce_Grow_Report_Revenue $reports ) {
		$report_data = $reports->get_report_data();

		return absint( array_sum( wp_list_pluck( $report_data->order_counts, 'count' ) ) );;
	}

	/**
	 * Calculate CR from orders and sessions
	 *
	 * @param $orders
	 * @param $sessions
	 *
	 * @return float
	 */
	public static function calculate_cr( $orders, $sessions ) {
		$orders   = 0 == $orders ? 1 : $orders;
		$sessions = 0 == $sessions ? 1 : $sessions;

		return wc_format_decimal( ( $orders / $sessions ) * 100, 1 );
	}

	/**
	 * Calculate AOV from revenue and orders
	 *
	 * @param $revenue
	 * @param $orders
	 *
	 * @return string
	 */
	public static function calculate_aov( $revenue, $orders ) {
		$orders  = 0 == $orders ? 1 : $orders;
		$revenue = 0.00 == $revenue ? 1 : $revenue;

		return round( $revenue / $orders );
	}

	/**
	 * Returns the first and last date of the month
	 *
	 * @since 1.0
	 *
	 * @param string $month
	 * @param string $year
	 *
	 * @return array
	 */
	public static function get_first_and_last_of_the_month( $month, $year ) {
		$start_date = date( 'Y-m-01', mktime( 0, 0, 0, $month, 1, $year ) );
		$end_date   = date( 'Y-m-t', mktime( 0, 0, 0, $month, 1, $year ) );

		return array( $start_date, $end_date );
	}

	public static function get_first_of_the_month( $month, $year ) {
		return date( 'Y-m-01', mktime( 0, 0, 0, $month, 1, $year ) );
	}

	public static function get_last_of_the_month( $month, $year ) {
		return date( 'Y-m-t', mktime( 0, 0, 0, $month, 1, $year ) );
	}

	public static function get_week_of_the_month() {
		$today                               = time();
		$current_of_the_year                 = date( 'W', $today );
		$first_of_the_month_week_of_the_year = date( 'W', strtotime( date( 'Y-m-01', $today ) ) );

		return ( $current_of_the_year - $first_of_the_month_week_of_the_year ) + 1;
	}

	public static function get_weeks_in_the_month() {
		$today = time();
		$weekNum = date('W', $today) - date('W', strtotime(date('Y-m-01', $today))) + 1;
		return $weekNum;
	}

	/**
	 * Calculates the increase by $rate percentage
	 *
	 * @param float $base The base amount that we are going to increase
	 * @param float $rate Percentage number to increase the base by. Should be given as the real percentage 5 for 5%, 10 for 10% increase.
	 *
	 * @return mixed
	 */
	public static function calculate_growth( $base, $rate ) {
		return wc_format_decimal( $base + ( $base * ( $rate / 100 ) ), 2 );
	}

	/**
	 * Calculates the percentage is the target to the base
	 *
	 * @param $target
	 * @param $base
	 *
	 * @return string
	 */
	public static function calculate_percentage_to( $target, $base ) {
		return round( ( $target / $base ) * 100 ) - 100;
	}

	/**
	 * Calculates the percentage the target of the base
	 *
	 * @param $target
	 * @param $base
	 *
	 * @return string
	 */
	public static function calculate_percentage_of( $target, $base ) {
		return round( ( $target / $base ) * 100 );
	}

	public static function sanitize( $input ) {
		return wc_clean( $input );
	}

	/**
	 * Get the number of days till the next month
	 *
	 * @since 1.0
	 *
	 * @param string $current_month Current month (i.e. 01)
	 * @param string $current_year  Current year (i.e. 2014)
	 *
	 * @return int
	 */
	public static function get_days_till_end_of_month( $current_month, $current_year ) {
		if ( 12 == $current_month ) {
			$first_day_of_next_month = mktime( 0, 0, 0, 0, 0, $current_year + 1 );
		} else {
			$first_day_of_next_month = mktime( 0, 0, 0, $current_month + 1, 1 );
		}

		return (int) floor( ( $first_day_of_next_month - time() ) / ( 24 * 3600 ) );
	}

	public static function calculate_target_growth_revenue_rate( $sessions, $cr_rate, $aov ) {
		return round( $sessions * ( $cr_rate / 100 ) * $aov );
	}

	public static function calculate_target_growth_orders_rate( $sessions, $cr_rate ) {
		return ceil( $sessions * ( $cr_rate / 100 ) );
	}

	/**
	 * Go through the target months and get all months before the current month
	 *
	 * @param array  $target_months Target months
	 * @param string $month         Current month
	 * @param string $year          Current year
	 *
	 * @since 1.0
	 *
	 * @return array All passed months in the same format as $target_months
	 */
	public static function get_passed_target_months( array $target_months, $month, $year ) {
		$passed = array();
		foreach ( $target_months as $target_month ) {
			if ( $target_month['month'] . $target_month['year'] == $month . $year ) {
				break;
			}
			$passed[] = $target_month;
		}

		return array_reverse( $passed );
	}

	/**
	 * Return the calculated track ratio
	 *
	 * @param $current_year
	 * @param $current_month
	 * @param $target_revenue
	 * @param $days_till_end_of_month
	 * @param $revenue
	 *
	 * @since 1.0
	 *
	 * @return float
	 */
	public static function calculate_track_ratio( $current_year, $current_month, $target_revenue, $days_till_end_of_month, $revenue ) {
		$days_of_the_month  = date( 't', strtotime( $current_year . '-' . $current_month ) );
		$rev_target_to_date = ( $target_revenue / $days_of_the_month ) * ( $days_of_the_month - $days_till_end_of_month );
		$track_ratio        = ( $rev_target_to_date - $revenue ) / $rev_target_to_date;

		return abs( $track_ratio );
	}

	/**
	 * Retrieve a setting from the plugin settings
	 *
	 * @since 1.0.2
	 *
	 * @param string $name               Name of the setting
	 * @param string $settings_freshness Should we force call DB or stick with the what we have.
	 * @param mixed  $default            Default value, if the setting is not found
	 *
	 * @return mixed
	 */
	public static function get_setting( $name, $settings_freshness = 'old', $default = '' ) {
		if ( null == self::$settings || 'new' == $settings_freshness ) {
			self::$settings = get_option( 'woocommerce_woocommerce_grow_settings', array() );
		}

		return self::get_field( $name, self::$settings, $default );
	}

	public static function get_history_months() {
		$targets_type = self::get_targets_type();
		if ( 'targets' == $targets_type ) {
			$months               = WooCommerce_Grow_Helpers::get_option( 'current_target_months', array() );
			$today_timestamp      = strtotime( 'midnight', current_time( 'timestamp' ) );
			$passed_target_months = WooCommerce_Grow_Helpers::get_passed_target_months( $months, date( 'm', $today_timestamp ), date( 'Y', $today_timestamp ) );
		} else {
			$passed_target_months = self::get_twelve_months_back();
		}

		return $passed_target_months;
	}

	/**
	 * Returns the appropriate card class to initiate, based on the targets type
	 *
	 * __Filter:__ 'woocommerce_row_cards_class'
	 *        can include own class to use for a card.
	 *                Classes should be defined in: slug => class name format.
	 *                Slug should be the same slug defined in 'woocommerce-grow-target-type-options' filter.
	 *
	 * __Action:__ 'woocommerce_grow_include_cards_class' -
	 *                can be used for action before the class is loaded, like including the class file.
	 *
	 * @since 1.1
	 *
	 * @param $month
	 * @param $year
	 * @param $targets_type
	 *
	 * @return WooCommerce_Grow_Card_Dashboard
	 */
	public static function get_card( $month, $year, $targets_type = 'month_on_month' ) {
		$map = apply_filters(
			'woocommerce_row_cards_class',
			array(
				// Classes should be defined in: slug => class name format
				'targets'        => 'WooCommerce_Grow_Card_Dashboard_Target',
				'month_on_month' => 'WooCommerce_Grow_Card_Dashboard_Month_On_Month',
				'year_on_year'   => 'WooCommerce_Grow_Card_Dashboard_Year_On_Year',
			),
			$targets_type,
			$month,
			$year
		);

		$class_name = WooCommerce_Grow_Helpers::get_field( $targets_type, $map, 'WooCommerce_Grow_Card_Dashboard_Month_On_Month' );

		// Can be used to run any actions before the class in loaded (i.e. include the class file)
		do_action( 'woocommerce_grow_include_cards_class', $class_name, $targets_type, $month, $year );

		if ( class_exists( $class_name ) ) {
			return new $class_name( $month, $year );
		}
	}

	/**
	 * Returns the target types for the dashboard dropdown.
	 *
	 * __Filter:__ 'woocommerce_grow_target_type_options'
	 *                Add a targets type to the dashboard dropdown.
	 *                The new types should be defined in a slug => Display name format
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public static function get_target_type_options() {
		return apply_filters(
			'woocommerce_grow_target_type_options',
			array(
				// slug => Display Name
				'targets'        => __( 'Targets', 'woocommerce-grow' ),
				'month_on_month' => __( 'Month on Month', 'woocommerce-grow' ),
				'year_on_year'   => __( 'Year on Year', 'woocommerce-grow' ),
			)
		);
	}

	/**
	 * Returns the targets type.
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public static function get_targets_type() {
		if ( null === self::$targets_type ) {
			self::$targets_type = WooCommerce_Grow_Helpers::get_option( 'targets_type', 'month_on_month' );
		}

		return self::$targets_type;
	}

	public static function get_ajax_url( $action, $nonce_name = null, array $query_args = array() ) {
		$nonce = wp_create_nonce( $nonce_name ? $nonce_name : $action );

		$query = array(
			'action'   => $action,
			'_wpnonce' => $nonce,
		);

		if ( ! empty( $query_args ) ) {
			$query = array_merge( $query, $query_args );
		}

		$url_query = http_build_query(
			$query, 'arg-'
		);

		$url = admin_url( 'admin-ajax.php?' ) . $url_query;

		return $url;
	}
}