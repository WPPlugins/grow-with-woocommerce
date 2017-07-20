<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  1.1
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Cron {
	public function __construct() {
		$this->is_report_enabled = 'yes' == WooCommerce_Grow_Helpers::get_setting( 'email_enabled', 'old', 'yes' ) ? true : false;
		$this->day_of_the_week   = WooCommerce_Grow_Helpers::get_setting( 'email_day_of_the_week', 'old', 'monday' );
	}

	/**
	 * Initiates the class hooks
	 */
	public function hooks() {
		// Add sync custom schedule
		add_filter( 'cron_schedules', array( $this, 'add_sync_schedules' ) );

		// Schedule
		add_action( 'init', array( $this, 'add_scheduled_syncs' ) );
	}

	/**
	 * Adds custom schedule from admin setting
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param array $schedules - existing WP recurring schedules
	 *
	 * @return array
	 */
	public function add_sync_schedules( $schedules ) {

		if ( $this->is_report_enabled ) {
			$schedules[ WooCommerce_Grow::PREFIX . 'email_report_schedule' ] = array(
				'interval' => 7 * $this->get_time_in_seconds( 'day' ),
				'display'  => __( 'Once Weekly', 'edd' )
			);
		}

		return $schedules;
	}

	/**
	 * Add scheduled events to wp-cron if not already added
	 *
	 * @access public
	 * @since  1.1
	 * @return array
	 */
	public function add_scheduled_syncs() {
		if ( $this->is_report_enabled ) {
			// Schedule inventory update
			if ( ! wp_next_scheduled( WooCommerce_Grow::PREFIX . 'email_report' ) ) {
				wp_schedule_event(
					strtotime( 'next ' . $this->day_of_the_week ),
					WooCommerce_Grow::PREFIX . 'email_report_schedule',
					WooCommerce_Grow::PREFIX . 'email_report'
				);
			}
		} else {
			// If sync is disabled then clear the cron schedule
			wp_clear_scheduled_hook( WooCommerce_Grow::PREFIX . 'email_report' );
		}
	}

	/**
	 * Returns the given period in seconds.
	 * Possibles:
	 * 	- hour
	 * 	- day
	 * 	- minute - default
	 *
	 * @since 1.1
	 * @param string $period
	 *
	 * @return int
	 */
	private function get_time_in_seconds( $period ) {
		if ( 'hour' == $period ) {
			$time_in_seconds = HOUR_IN_SECONDS;
		} elseif ( 'day' == $period ) {
			$time_in_seconds = DAY_IN_SECONDS;
		} else {
			$time_in_seconds = MINUTE_IN_SECONDS;
		}

		return $time_in_seconds;

	}
}