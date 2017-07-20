<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Targets page display and fields storing.
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Page_Targets extends WooCommerce_Grow_Pages {
	public function __construct() {
		$this->id    = 'targets';
		$this->title = 'Targets';

		add_filter( 'woocommerce_grow_page_tabs_array', array( $this, 'add_page' ), 10 );
		add_action( 'woocommerce_grow_page_content_' . $this->id, array( $this, 'page_content' ) );
		add_action( 'woocommerce_grow_save_page_settings_' . $this->id, array( $this, 'save' ) );
		add_action( 'woocommerce_grow_page_tabs', array( $this, 'add_help_button' ) );

	}

	/**
	 * Add the help message and information icon to top right of the targets page.
	 */
	public function add_help_button() {
		if ( 'targets' == WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, '' ) ) {
			?>
			<a href="#" class="wc-grow-need-help wc-grow-help-tooltip" >
				<?php echo esc_html( __( 'Need Help?', 'woocommerce-grow' ) ); ?>
				<span class="dashicons dashicons-info"></span>
			</a>
			<?php
		}
	}

	/**
	 * Outputs page content
	 *
	 * @since 1.0
	 */
	public function page_content() {
		// Check and set initial targets on first Targets page load
		// This will ensure that targets page is loaded with initial data already in place,
		// so users can see example of the data before they start targets setup.
		$this->maybe_set_target_months();
		$this->maybe_set_initial_targets();

		$this->load_scripts();

		include 'views/html-page-targets.php';
	}

	/**
	 * Load Page scripts
	 *
	 * @since 1.0
	 */
	public function load_scripts() {
		$scripts = new WooCommerce_Grow_Scripts();
		$scripts->enqueue_admin_scripts();
		$scripts->enqueue_target_scripts();
		$scripts->register_targets_localized_scripts();
	}

	/**
	 * Save the Targets settings
	 *
	 * @since 1.0
	 */
	public function save() {
		try {
			$this->verify_request( WooCommerce_Grow_Helpers::get_field( '_wpnonce', $_REQUEST ), 'woocommerce-grow-targets' );
			$is_calculate_growth = null !== WooCommerce_Grow_Helpers::get_field( 'calculate_growth', $_POST ) ? true : false;
			$this->save_and_calculate_targets( $is_calculate_growth );
		}
		catch ( Exception $e ) {
			WC_Admin_Settings::add_error( $e->getMessage() );
		}
	}

	/**
	 * Save the initial and calculate targets, based on the $is_calculate_growth action.
	 *
	 * @since 1.0
	 *
	 * @param bool $is_calculate_growth Are we calculating new targets or we are simply saving the targets
	 *
	 * @throws Exception
	 */
	private function save_and_calculate_targets( $is_calculate_growth ) {
		$targets = array();
		$data = array();

		// Calculate and Growth settings
		if ( $is_calculate_growth ) {
			$previous_month         = date( 'm', strtotime( 'first day of previous month' ) );
			$year_of_previous_month = date( 'Y' );
			if ( 12 == $previous_month ) {
				$year_of_previous_month = $year_of_previous_month - 1;
			}

			// Load Revenue Reports
			$reports = new WooCommerce_Grow_Report_Revenue();
			$reports->load_wc_reports_for_month( $previous_month, $year_of_previous_month );
			$initial_revenue = $reports->get_wc_total_sales();
			$initial_orders  = $reports->get_wc_total_orders();

			if ( 100 >= $initial_revenue ) {
				$initial_revenue  = 1250;
				$initial_orders   = 25;
				$initial_sessions = 500;
			} else {
				$auth_code = WooCommerce_Grow_Helpers::get_setting( 'authorization_code' );
				if ( '' === $auth_code ) {
					// If GA is not set, we'll set the default to 500
					$initial_sessions = 500;
				} else {
					// Call to GA to get sessions for the last month
					$ga               = WooCommerce_Grow_Google_Analytics::get_instance();
					$initial_sessions = $ga->get_month_sessions_from_storage( $previous_month, $year_of_previous_month );

				}
			}

			$initial_cr  = WooCommerce_Grow_Helpers::calculate_cr( $initial_orders, $initial_sessions );
			$initial_aov = WooCommerce_Grow_Helpers::calculate_aov( $initial_revenue, $initial_orders );
			$posted_rate = WooCommerce_Grow_Helpers::get_field( 'growth_rate', $_POST, '' );
			$growth_rate = '' !== $posted_rate ? $posted_rate : 5;

			$this->save_comparison_month( $initial_revenue, $initial_orders, $initial_sessions, $initial_cr, $initial_aov, $growth_rate );

			$months = WooCommerce_Grow_Helpers::get_twelve_months_ahead();

			foreach ( $months as $month ) {
				$target_sessions = WooCommerce_Grow_Helpers::calculate_growth( $initial_sessions, $growth_rate );
				$target_cr       = WooCommerce_Grow_Helpers::calculate_growth( $initial_cr, $growth_rate );
				$target_aov      = WooCommerce_Grow_Helpers::calculate_growth( $initial_aov, $growth_rate );

				$targets['sessions_target'][ $month['year'] ][ $month['month'] ]             = ceil( $target_sessions );
				$targets['sessions_target']['modified'][ $month['year'] ][ $month['month'] ] = 0;
				$targets['sessions_growth_target_rate'][ $month['year'] ][ $month['month'] ] = $growth_rate;

				$targets['cr_target'][ $month['year'] ][ $month['month'] ]             = $target_cr;
				$targets['cr_target']['modified'][ $month['year'] ][ $month['month'] ] = 0;
				$targets['cr_growth_target_rate'][ $month['year'] ][ $month['month'] ] = $growth_rate;

				$targets['aov_target'][ $month['year'] ][ $month['month'] ]             = $target_aov;
				$targets['aov_target']['modified'][ $month['year'] ][ $month['month'] ] = 0;
				$targets['aov_growth_target_rate'][ $month['year'] ][ $month['month'] ] = $growth_rate;

				$targets['revenue_target'][ $month['year'] ][ $month['month'] ] = WooCommerce_Grow_Helpers::calculate_target_growth_revenue_rate( $target_sessions, $target_cr, $target_aov );
				$targets['orders_target'][ $month['year'] ][ $month['month'] ]  = WooCommerce_Grow_Helpers::calculate_target_growth_orders_rate( $target_sessions, $target_cr );
			}

			// Save the target months data and the months we are targeting
			WooCommerce_Grow_Helpers::update_option( 'monthly_targets', $targets );
			WooCommerce_Grow_Helpers::update_option( 'current_target_months', $months );
		} else {
			// Remove the first timer flag
			WooCommerce_Grow_Helpers::update_option( 'user_activation_type', 'old' );

			// Validate the comparison month values
			$this->validate_initial_month();

			// Validate Target months data
			$this->validate_targets_data();

			$initial_revenue  = WooCommerce_Grow_Helpers::get_field( 'initial_revenue_number', $_POST );
			$initial_orders   = WooCommerce_Grow_Helpers::get_field( 'initial_orders_number', $_POST );
			$initial_sessions = WooCommerce_Grow_Helpers::get_field( 'initial_sessions_number', $_POST );
			$initial_cr       = WooCommerce_Grow_Helpers::get_field( 'initial_cr_number', $_POST );
			$initial_aov      = WooCommerce_Grow_Helpers::get_field( 'initial_aov_number', $_POST );
			$growth_rate      = WooCommerce_Grow_Helpers::get_field( 'growth_rate', $_POST );

			// Save the comparison month
			$this->save_comparison_month( $initial_revenue, $initial_orders, $initial_sessions, $initial_cr, $initial_aov, $growth_rate );

			$months = WooCommerce_Grow_Helpers::get_option( 'current_target_months' );

			foreach ( $months as $month ) {
				list( $data['sessions_target'], $data['sessions_target_modified'], $data['sessions_growth_rate'] ) = $this->get_sessions_data( $month );
				list( $data['cr_target'], $data['cr_target_modified'], $data['cr_growth_rate'] ) = $this->get_conversion_rate_data( $month );
				list( $data['aov_target'], $data['aov_target_modified'], $data['aov_growth_rate'] ) = $this->get_aov_data( $month );
				$data['revenue_target'] = WooCommerce_Grow_Helpers::calculate_target_growth_revenue_rate( $data['sessions_target'], $data['cr_target'], $data['aov_target'] );
				$data['orders_target']  = WooCommerce_Grow_Helpers::calculate_target_growth_orders_rate( $data['sessions_target'], $data['cr_target'] );

				// Debug
				WooCommerce_Grow_Helpers::add_debug_log( 'Each Months: ' . print_r( $data, true ) );

				$this->build_target_months( $data, $month, $targets );
			}

			// Debug
			WooCommerce_Grow_Helpers::add_debug_log( 'Target Months: ' . print_r( $targets, true ) );

			// Save all Target Months
			WooCommerce_Grow_Helpers::update_option( 'monthly_targets', $targets );
		}
	}

	/**
	 * Validate that all posted fields are in the correct format
	 *
	 * @since 1.0
	 *
	 * @throws Exception
	 */
	public function validate_initial_month() {
		$initial_revenue  = WooCommerce_Grow_Helpers::get_field( 'initial_revenue_number', $_POST );
		$initial_orders   = WooCommerce_Grow_Helpers::get_field( 'initial_orders_number', $_POST );
		$initial_sessions = WooCommerce_Grow_Helpers::get_field( 'initial_sessions_number', $_POST );
		$initial_cr       = WooCommerce_Grow_Helpers::get_field( 'initial_cr_number', $_POST );
		$initial_aov      = WooCommerce_Grow_Helpers::get_field( 'initial_aov_number', $_POST );
		$growth_rate      = WooCommerce_Grow_Helpers::get_field( 'growth_rate', $_POST );

		if ( ! is_numeric( $initial_revenue ) ) {
			throw new Exception( __( 'Comparison Month Revenue is not a valid value. Please refresh the page and try again.' ) );
		}

		if ( ! is_numeric( $initial_orders ) ) {
			throw new Exception( __( 'Comparison Month Orders is not a valid value. Please refresh the page and try again.' ) );
		}

		if ( ! is_numeric( $initial_sessions ) ) {
			throw new Exception( __( 'Comparison Month Sessions is not a valid value. Please enter a valid number (i.e. 100) and try again.' ) );
		}

		if ( ! is_numeric( $initial_cr ) ) {
			throw new Exception( __( 'Comparison Month CR is not a valid value. Please enter a percentage number ( i.e 5 for 5% ) and try again.' ) );
		}

		if ( ! is_numeric( $initial_aov ) ) {
			throw new Exception( __( 'Comparison Month AOV is not a valid value. Please enter a valid number (i.e. 50.00) and try again.' ) );
		}

		if ( ! is_numeric( $growth_rate ) ) {
			throw new Exception( __( 'Growth Rate is not a valid value. Please enter a valid number (i.e. 5 for 5%) and try again.' ) );
		}
	}

	/**
	 * Save the data for the Comparison month
	 *
	 * @since 1.0
	 *
	 * @param $initial_revenue
	 * @param $initial_orders
	 * @param $initial_sessions
	 * @param $initial_cr
	 * @param $initial_aov
	 * @param $growth_rate
	 */
	public function save_comparison_month( $initial_revenue, $initial_orders, $initial_sessions, $initial_cr, $initial_aov, $growth_rate ) {
		// Save the values for the Comparison Month
		WooCommerce_Grow_Helpers::update_option( 'initial_revenue_number', wc_clean( (float) $initial_revenue ) );
		WooCommerce_Grow_Helpers::update_option( 'initial_orders_number', wc_clean( (int) $initial_orders ) );
		WooCommerce_Grow_Helpers::update_option( 'initial_sessions_number', wc_clean( (int) $initial_sessions ) );
		WooCommerce_Grow_Helpers::update_option( 'initial_cr_number', wc_clean( (float) $initial_cr ) );
		WooCommerce_Grow_Helpers::update_option( 'initial_aov_number', wc_clean( (float) $initial_aov ) );
		WooCommerce_Grow_Helpers::update_option( 'growth_rate', wc_clean( (float) $growth_rate ) );
	}

	/**
	 * Get the Sessions data from the POSTed fields
	 *
	 * @since 1.0
	 *
	 * @param $month
	 *
	 * @return array
	 */
	private function get_sessions_data( $month ) {
		$sessions_target          = isset( $_POST['sessions_target']['target'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['sessions_target']['target'][ $month['year'] ][ $month['month'] ] :
			'N/A'; // TODO: Think of a way to handle missing data
		$sessions_target_modified = isset( $_POST['sessions_target']['growth_rate']['modified'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['sessions_target']['growth_rate']['modified'][ $month['year'] ][ $month['month'] ] :
			'0';
		$sessions_growth_rate     = isset( $_POST['sessions_target']['growth_rate'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['sessions_target']['growth_rate'][ $month['year'] ][ $month['month'] ] :
			'N/A';

		return array( $sessions_target, $sessions_target_modified, $sessions_growth_rate );
	}

	/**
	 * Get the CR data from the POSTed fields
	 *
	 * @since 1.0
	 *
	 * @param array $month
	 *
	 * @return array
	 */
	private function get_conversion_rate_data( $month ) {
		$cr_target          = isset( $_POST['cr_target']['target'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['cr_target']['target'][ $month['year'] ][ $month['month'] ] :
			'N/A'; // TODO: Think of a way to handle missing data
		$cr_target_modified = isset( $_POST['cr_target']['growth_rate']['modified'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['cr_target']['growth_rate']['modified'][ $month['year'] ][ $month['month'] ] :
			'0';
		$cr_growth_rate     = isset( $_POST['cr_target']['growth_rate'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['cr_target']['growth_rate'][ $month['year'] ][ $month['month'] ] :
			'N/A';

		return array( $cr_target, $cr_target_modified, $cr_growth_rate );
	}

	/**
	 * Get the AOV data from the POSTed fields
	 *
	 * @since 1.0
	 *
	 * @param array $month
	 *
	 * @return array
	 */
	private function get_aov_data( $month ) {
		$aov_target          = isset( $_POST['aov_target']['target'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['aov_target']['target'][ $month['year'] ][ $month['month'] ] :
			'N/A'; // TODO: Think of a way to handle missing data
		$aov_target_modified = isset( $_POST['aov_target']['growth_rate']['modified'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['aov_target']['growth_rate']['modified'][ $month['year'] ][ $month['month'] ] :
			'0';
		$aov_growth_rate     = isset( $_POST['aov_target']['growth_rate'][ $month['year'] ][ $month['month'] ] ) ?
			$_POST['aov_target']['growth_rate'][ $month['year'] ][ $month['month'] ] :
			'N/A';

		return array( $aov_target, $aov_target_modified, $aov_growth_rate );
	}

	/**
	 * Add the targets months data into the targets array
	 *
	 * @since 1.0
	 *
	 * @param $data
	 * @param $month
	 * @param $targets
	 *
	 * @return mixed
	 */
	private function build_target_months( $data, $month, &$targets ) {
		$targets['sessions_target'][ $month['year'] ][ $month['month'] ]             = WooCommerce_Grow_Helpers::sanitize( ceil( $data['sessions_target'] ) );
		$targets['sessions_target']['modified'][ $month['year'] ][ $month['month'] ] = 1 == $data['sessions_target_modified'] ? 1 : 0;
		$targets['sessions_growth_target_rate'][ $month['year'] ][ $month['month'] ] = WooCommerce_Grow_Helpers::sanitize( $data['sessions_growth_rate'] );

		$targets['cr_target'][ $month['year'] ][ $month['month'] ]             = WooCommerce_Grow_Helpers::sanitize( $data['cr_target'] );
		$targets['cr_target']['modified'][ $month['year'] ][ $month['month'] ] = 1 == $data['cr_target_modified'] ? 1 : 0;
		$targets['cr_growth_target_rate'][ $month['year'] ][ $month['month'] ] = WooCommerce_Grow_Helpers::sanitize( $data['cr_growth_rate'] );

		$targets['aov_target'][ $month['year'] ][ $month['month'] ]             = WooCommerce_Grow_Helpers::sanitize( $data['aov_target'] );
		$targets['aov_target']['modified'][ $month['year'] ][ $month['month'] ] = 1 == $data['aov_target_modified'] ? 1 : 0;
		$targets['aov_growth_target_rate'][ $month['year'] ][ $month['month'] ] = WooCommerce_Grow_Helpers::sanitize( $data['aov_growth_rate'] );

		$targets['revenue_target'][ $month['year'] ][ $month['month'] ] = WooCommerce_Grow_Helpers::sanitize( $data['revenue_target'] );
		$targets['orders_target'][ $month['year'] ][ $month['month'] ]  = WooCommerce_Grow_Helpers::sanitize( $data['orders_target'] );

		return $targets;
	}

	/**
	 * Validate the target months input
	 *
	 * @since 1.0
	 *
	 * @throws Exception
	 */
	private function validate_targets_data() {
		if ( true != $this->validate_data( WooCommerce_Grow_Helpers::get_field( 'sessions_target', $_POST, array() ) ) ||
			true != $this->validate_data( WooCommerce_Grow_Helpers::get_field( 'cr_target', $_POST, array() ) ) ||
			true != $this->validate_data( WooCommerce_Grow_Helpers::get_field( 'aov_target', $_POST, array() ) )
		) {
			throw new Exception ( __( 'The data in the Target Months is not valid. Please refresh the page and try again.', 'woocommerce-grow' ) );
		}
	}

	/**
	 * Recursively crawl an array and validate that all values are numerical
	 *
	 * @since 1.0
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public function validate_data( array $array ) {
		$valid = false;

		foreach ( $array as $value ) {
			if ( is_array( $value ) ) {
				$valid = $this->validate_data( $value );
			} else {
				$valid = is_numeric( $value );
			}
		}

		return $valid;
	}

	/**
	 * Check that we have a target months setup.
	 * Set them up, if they are not.
	 *
	 * @since 1.0
	 */
	public function maybe_set_target_months() {
		$target_months = WooCommerce_Grow_Helpers::get_option( 'current_target_months', array() );
		if ( empty( $target_months ) ) {
			$months = WooCommerce_Grow_Helpers::get_twelve_months_ahead();
			WooCommerce_Grow_Helpers::update_option( 'current_target_months', $months );
		}
	}

	/**
	 * Check that we have monthly targets calculated and set,
	 * on Targets page load. Set them, if we don't.
	 *
	 * @since 1.0
	 */
	public function maybe_set_initial_targets() {
		$initial_targets = WooCommerce_Grow_Helpers::get_option( 'monthly_targets', array() );
		if ( empty( $initial_targets ) ) {
			$is_calculate_growth = true;
			try {
				$this->save_and_calculate_targets( $is_calculate_growth );
			}
			catch ( Exception $e ) {
				WC_Admin_Settings::add_error( $e->getMessage() );
			}
		}
	}
}