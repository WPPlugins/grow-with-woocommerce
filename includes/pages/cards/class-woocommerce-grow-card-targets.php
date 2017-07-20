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
class WooCommerce_Grow_Card_Targets extends WooCommerce_Grow_Card {

	public $growth_rate;
	public $initial_revenue;
	public $initial_orders;
	public $initial_sessions;
	public $initial_cr;
	public $initial_aov;
	public $modded = false;

	public $currency_symbol;
	public $month_in_text;
	public $sessions_target;
	public $sessions_modified;
	public $sessions_growth_target_rate;
	public $sessions_display_modified;
	public $sessions_rate_color;
	public $cr_target;
	public $cr_modified;
	public $cr_growth_target_rate;
	public $cr_display_modified;
	public $cr_rate_color;
	public $aov_target;
	public $aov_modified;
	public $aov_growth_target_rate;
	public $aov_display_modified;
	public $aov_rate_color;
	public $revenue_target;
	public $revenue_target_percentage;
	public $orders_target;
	public $orders_target_percentage;

	public function display_card() {
		include( 'views/html-monthly-card-targets.php' );
	}

	public function set_targets_data() {
		$this->set_targets();
	}

	public function set_targets() {
		$this->currency_symbol = $this->get_currency_symbol();
		$monthly_targets = $this->get_monthly_targets();
		$this->month_in_text = date( 'F', strtotime( $this->year . '-' . $this->month ) );

		$this->sessions_target           = isset( $monthly_targets['sessions_target'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['sessions_target'][ $this->year ][ $this->month ] :
			'N/A';
		$this->sessions_modified         = isset( $monthly_targets['sessions_target']['modified'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['sessions_target']['modified'][ $this->year ][ $this->month ] :
			'0';
		$this->sessions_growth_target_rate    = isset( $monthly_targets['sessions_growth_target_rate'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['sessions_growth_target_rate'][ $this->year ][ $this->month ] :
			'N/A';
		$this->sessions_display_modified = '1' == $this->sessions_modified ? 'inline' : 'none';
		$this->sessions_rate_color = 0 < $this->sessions_growth_target_rate ? 'wc-grow-color-green': 'wc-grow-color-red';

		$this->cr_target           = isset( $monthly_targets['cr_target'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['cr_target'][ $this->year ][ $this->month ] :
			'N/A';
		$this->cr_modified         = isset( $monthly_targets['cr_target']['modified'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['cr_target']['modified'][ $this->year ][ $this->month ] :
			'0';
		$this->cr_growth_target_rate    = isset( $monthly_targets['cr_growth_target_rate'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['cr_growth_target_rate'][ $this->year ][ $this->month ] :
			'N/A';
		$this->cr_display_modified = '1' == $this->cr_modified ? 'inline' : 'none';
		$this->cr_rate_color = 0 < $this->cr_growth_target_rate ? 'wc-grow-color-green': 'wc-grow-color-red';

		$this->aov_target           = isset( $monthly_targets['aov_target'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['aov_target'][ $this->year ][ $this->month ] :
			'N/A';
		$this->aov_modified         = isset( $monthly_targets['aov_target']['modified'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['aov_target']['modified'][ $this->year ][ $this->month ] :
			'0';
		$this->aov_growth_target_rate    = isset( $monthly_targets['aov_growth_target_rate'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['aov_growth_target_rate'][ $this->year ][ $this->month ] :
			'N/A';
		$this->aov_display_modified = '1' == $this->aov_modified ? 'inline' : 'none';
		$this->aov_rate_color = 0 < $this->aov_growth_target_rate ? 'wc-grow-color-green': 'wc-grow-color-red';

		$this->revenue_target = isset( $monthly_targets['revenue_target'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['revenue_target'][ $this->year ][ $this->month ] :
			'N/A';
		$this->revenue_target_percentage = WooCommerce_Grow_Helpers::calculate_percentage_to( $this->revenue_target, $this->initial_revenue );
		$this->orders_target  = isset( $monthly_targets['orders_target'][ $this->year ][ $this->month ] ) ?
			$monthly_targets['orders_target'][ $this->year ][ $this->month ] :
			'N/A';
		$this->orders_target_percentage = WooCommerce_Grow_Helpers::calculate_percentage_to( $this->orders_target, $this->initial_orders );

		if ( $this->is_card_modified() && false == $this->modded ) {
			$this->modded = true;
		}
	}

	public function set_growth_rate($growth_rate) {
		$this->growth_rate = $growth_rate;
	}

	public function set_initials_data( $initial_revenue, $initial_orders, $initial_sessions, $initial_cr, $initial_aov ) {
		$this->initial_revenue  = $initial_revenue;
		$this->initial_orders   = $initial_orders;
		$this->initial_sessions = $initial_sessions;
		$this->initial_cr       = $initial_cr;
		$this->initial_aov      = $initial_aov;
	}

	public function string_target_metric() {
	    	return __( 'Target Metric', 'woocommerce-grow' );
	}

	public function string_comparison_metric() {
	    	return __( 'Comparison Metric', 'woocommerce-grow' );
	}

	/**
	 * Checks, if the card has manually modified data.
	 * Should always be called after the card data is set.
	 *
	 * @since 1.1
	 *
	 * @return bool
	 */
	public function is_card_modified() {
		return ( '1' == $this->cr_modified || '1' == $this->sessions_modified || '1' == $this->aov_modified );
	}
}