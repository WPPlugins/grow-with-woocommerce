<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Ajax requests
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Ajax {
	public function hooks() {
		add_action( 'wp_ajax_get_remaining_history_months', array( $this, 'get_remaining_history_months' ) );
		add_action( 'wp_ajax_get_dashboard_graph_data', array( $this, 'get_dashboard_graph_data' ) );
		add_action( 'wp_ajax_change_targets_type', array( $this, 'change_targets_type' ) );
		add_action( 'wp_ajax_grow_report_email_send', array( $this, 'send_report_email' ) );
	}

	/**
	 * Retrieves the remaining dashboard history months
	 *
	 * @since 1.0
	 */
	public function get_remaining_history_months() {
		try {
			$validate = $this->verify_request( 'woocommerce-grow-dashboard' );
			if ( true !== $validate ) {
				throw new Exception( $validate );
			}

			$dashboard             = new WooCommerce_Grow_Page_Dashboard();
			$passed_months         = WooCommerce_Grow_Helpers::get_history_months();
			$passed_months_display = array_slice( $passed_months, $dashboard->get_display_history_months() );

			ob_start();
			foreach ( $passed_months_display as $passed ) {
				$card = WooCommerce_Grow_Helpers::get_card( $passed['month'], $passed['year'], $dashboard->get_targets_type() );
				$card->set_targets_data();
				$card->set_card_data();
				$card->display_card();
			}
			$output = ob_get_clean();

			echo json_encode( array( 'months' => $output ) );
		}
		catch ( Exception $e ) {
			echo json_encode( array( 'error' => $e->getMessage() ) );
		}

		exit;
	}

	/**
	 * Loads and returns the revenue / target revenue data for the chart
	 *
	 * @since  1.0
	 */
	public function get_dashboard_graph_data() {
		try {
			$validate = $this->verify_request( 'woocommerce-grow-dashboard' );
			if ( true !== $validate ) {
				throw new Exception( $validate );
			}

			$range_field = WooCommerce_Grow_Helpers::get_field( 'revenueGraphRange', $_GET );
			if ( 'yearly' == $range_field ) {
				$range = 'yearly';
			} elseif ( 'monthly' == $range_field ) {
				$range = 'monthly';
			} else {
				$range = 'quarterly';
			}

			$revenue_chart = new WooCommerce_Grow_Report_Revenue();
			$revenue_chart->setup_chart_data( $range );

			$revenue_amounts        = $revenue_chart->get_revenue_amount_chart_data();
			$target_revenue_amounts = $revenue_chart->get_revenue_target_amount_chart_data();

			// Encode in json format
			$chart_data = json_encode(
				array(
					'revenue_amounts'        => array_map( array( $revenue_chart, 'round_chart_totals' ), array_values( $revenue_amounts ) ),
					'target_revenue_amounts' => array_map( array( $revenue_chart, 'round_chart_totals' ), array_values( $target_revenue_amounts ) ),
				)
			);

			echo $chart_data;
		}
		catch ( Exception $e ) {
			echo json_encode( array( 'error' => $e->getMessage() ) );
		}

		exit;
	}

	/**
	 * Perform a change of the targets type.
	 * Month on Month, Year on Year, Set Targets
	 *
	 * @since  1.1
	 */
	public function change_targets_type() {
		try {
			$validate = $this->verify_request( 'woocommerce-grow-dashboard' );
			if ( true !== $validate ) {
				throw new Exception( $validate );
			}

			// Get the passed targets type
			$targets_type = WooCommerce_Grow_Helpers::get_field( 'targetsType', $_GET, 'month_on_month' );

			// Save the targets type
			$update = WooCommerce_Grow_Helpers::update_option( 'targets_type', $targets_type );

			echo json_encode(
				array(
					'result' => $update,
				)
			);
		}
		catch ( Exception $e ) {
			echo json_encode( array( 'error' => $e->getMessage() ) );
		}

		exit;
	}

	/**
	 * Perform a change of the targets type.
	 * Month on Month, Year on Year, Set Targets
	 *
	 * @since  1.1
	 */
	public function send_report_email() {
		try {
			$validate = $this->verify_request( 'woocommerce-grow-send-report-email' );
			if ( true !== $validate ) {
				throw new Exception( $validate );
			}

			$email = new WooCommerce_Grow_Emails();
			$sent  = $email->email_report();

			$url = wp_get_referer();
			$url = remove_query_arg( array( 'grow-emailed', 'grow-failed-reason' ), $url );
			if ( $sent ) {
				$url = add_query_arg( 'grow-emailed', 'success', $url );

				wp_safe_redirect( $url );
			} else {
				throw new Exception ( 'Email Unsucessful' );
			}
		}
		catch ( Exception $e ) {
			$url = add_query_arg( 'grow-emailed', 'failed', add_query_arg( 'grow-failed-reason', $e->getMessage(), $url ) );

			wp_safe_redirect( $url );
		}

		exit;
	}

	/**
	 * Check if the request is from an admin or a user that with sufficient rights. <br/>
	 * Verify the _wpnonce.
	 *
	 * @access private
	 * @since  1.0
	 *
	 * @param string $action - Nonce the ajax action is performed with
	 *
	 * @return void
	 */
	private function verify_request( $action ) {
		$valid = true;

		if ( ! is_admin() || ! current_user_can( 'manage_woocommerce' ) ) {
			$valid = __( 'You do not have sufficient permissions to access this page.', 'woocommerce-grow' );
		}

		if ( ! wp_verify_nonce( ( WooCommerce_Grow_Helpers::get_field( '_wpnonce', $_GET ) ), $action ) ) {
			$valid = __( 'Cannot verify the request, please go back and try again.', 'woocommerce-grow' );
		}

		return $valid;
	}
}