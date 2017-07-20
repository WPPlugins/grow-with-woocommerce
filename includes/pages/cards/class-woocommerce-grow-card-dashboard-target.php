<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prepares and displays Targets card data
 *
 * @since  1.1
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Card_Dashboard_Target extends WooCommerce_Grow_Card_Dashboard {

	/**
	 * Outputs the monthly card html
	 *
	 * @since 1.1
	 */
	public function display_card() {
		$monthly_targets = $this->get_monthly_targets();

		if ( empty( $monthly_targets ) ) {
			if ( $this->check_is_current_month() ) {
				include('views/html-empty-targets-card.php');
			}
		} else {
			include( 'views/html-monthly-card.php' );
		}
	}

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
		$value = 0;
		$data  = $this->get_monthly_targets();
		if ( isset( $data['sessions_target'][ $this->year ][ $this->month ] ) ) {
			$value = $data['sessions_target'][ $this->year ][ $this->month ];
		}

		return $this->no_zero_value( $value, 1 );
	}

	/**
	 * Get the target CR data for the given month from the saved monthly targets
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_target_cr() {
		$value = 0;
		$data  = $this->get_monthly_targets();
		if ( isset( $data['cr_target'][ $this->year ][ $this->month ] ) ) {
			$value = $data['cr_target'][ $this->year ][ $this->month ];
		}

		return $this->no_zero_value( $value, 0.01 );
	}

	/**
	 * Get the target AOV data for the given month from the saved monthly targets
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_target_aov() {
		$value = 0;
		$data  = $this->get_monthly_targets();
		if ( isset( $data['aov_target'][ $this->year ][ $this->month ] ) ) {
			$value = $data['aov_target'][ $this->year ][ $this->month ];
		}

		return $this->no_zero_value( $value, 1 );
	}

	/**
	 * Get the target Revenue data for the given month from the saved monthly targets
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_target_revenue() {
		$value = 0;
		$data  = $this->get_monthly_targets();
		if ( isset( $data['revenue_target'][ $this->year ][ $this->month ] ) ) {
			$value = $data['revenue_target'][ $this->year ][ $this->month ];
		}

		return $this->no_zero_value( $value, 1 );
	}

	/**
	 * Get the target Orders data for the given month from the saved monthly targets
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_target_orders() {
		$value = 0;
		$data  = $this->get_monthly_targets();
		if ( isset( $data['orders_target'][ $this->year ][ $this->month ] ) ) {
			$value = $data['orders_target'][ $this->year ][ $this->month ];
		}

		return $this->no_zero_value( $value, 1 );
	}
}