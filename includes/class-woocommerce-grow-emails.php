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
class WooCommerce_Grow_Emails {

	/**
	 * Loads the emails reports hook.
	 * This hook is called by the cron schedule.
	 *
	 * @since 1.1
	 */
	public function email_report_hook() {
		add_action( WooCommerce_Grow::PREFIX . 'email_report', array( $this, 'email_report' ) );
	}

	/**
	 * Generates and emails the email report.
	 *
	 * @since 1.1
	 *
	 * @return bool
	 */
	public function email_report() {
		// Set sessions in case they are missing.
		$ga = new WooCommerce_Grow_Google_Analytics();
		if ( ! $ga->has_stored_sessions() ) {
			$ga->set_sessions();
		}

		$month  = date( 'm', time() );
		$year   = date( 'Y', time() );
		$report = WooCommerce_Grow_Helpers::get_card( $month, $year, WooCommerce_Grow_Helpers::get_setting( 'email_report_type', 'old', 'month_on_month' ) );
		$report->set_targets_data();
		$report->set_card_data();
		$email_report['current'] = $report;

		if ( 1 == WooCommerce_Grow_Helpers::get_week_of_the_month() ) {
			$last_month        = date( 'm', strtotime( 'first day -1 month' ) );
			$last_month_year   = date( 'Y', strtotime( 'first day -1 month' ) );
			$last_month_report = WooCommerce_Grow_Helpers::get_card( $last_month, $last_month_year, WooCommerce_Grow_Helpers::get_setting( 'email_report_type', 'old', 'month_on_month' ) );
			$last_month_report->set_targets_data();
			$last_month_report->set_card_data();
			$email_report['last'] = $last_month_report;
		}

		$rss = new DOMDocument();
		$rss->load( 'https://raison.co/feed/' );

		$report_email = new WooCommerce_Grow_Email_Report();
		$report_email->set_report( $email_report );
		$report_email->set_feeds( $rss->getElementsByTagName('item') );
		$sent = $report_email->send_report();

		return $sent;
	}
}