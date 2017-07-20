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
class WooCommerce_Grow_Google_Analytics {

	/**
	 * @var null|Google_Service_Analytics
	 */
	private $analytics;

	/**
	 * @var null|Google_Client
	 */
	private $client = null;

	/**
	 * @var null|WooCommerce_Grow_Google_Analytics
	 */
	private static $instance = null;

	private $sessions = null;

	public function __construct() {

		if ( is_null( self::$instance ) ) {
			self::$instance = $this;
		}

		add_action( 'woocommerce_api_' . strtolower( get_class( $this ) ), array( $this, 'catch_ga_authenitication_code' ) );

		$this->load_google_analytics();
	}

	/**
	 * Instantiate the class
	 *
	 * @return WooCommerce_Grow_Google_Analytics
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function load_google_analytics() {
		WooCommerce_Grow::load_google_analytics();

		// We setup the Google Client object
		$this->client = new Google_Client();
		$this->client->setAuthConfigFile( WooCommerce_Grow::get_plugin_path() . '/ga-config.json' );
		$this->client->addScope( Google_Service_Analytics::ANALYTICS_READONLY );
		$this->client->setAccessType( 'offline' );

		$this->analytics = new Google_Service_Analytics( $this->client );
	}

	public function catch_ga_authenitication_code() {

	}

	/**
	 * Returns the Google authentication url.
	 * The users are sent to this URL to allow access to their Google account.
	 *
	 * @return string
	 */
	public function get_authentication_url() {
		return $this->client->createAuthUrl();
	}

	public function get_month_sessions_from_storage( $month, $year ) {
		if ( ! $this->has_stored_sessions() ) {
			$this->set_sessions();
		}

		return WooCommerce_Grow_Helpers::get_field( $month, WooCommerce_Grow_Helpers::get_field( $year, $this->sessions, array() ), 0 );
	}

	/**
	 * Returns Google Sessions for the given month.
	 *
	 * @param $month
	 * @param $year
	 *
	 * @return array|mixed
	 * @throws Exception
	 */
	public function get_sessions_for_month( $month, $year ) {
		try {
			if ( $this->is_app_authenticated() ) {
				$start_date = WooCommerce_Grow_Helpers::get_first_of_the_month( $month, $year );
				$end_date   = WooCommerce_Grow_Helpers::get_last_of_the_month( $month, $year );
				$id         = $this->get_single_ga_profile();
				$data       = $this->get_metrics( $id, 'ga:sessions', $start_date, $end_date );
				$rows       = $data->getRows();

				// REMOVE
				WooCommerce_Grow_Helpers::add_debug_log( 'Month Rows: ' . print_r( $rows, true ) );

				if ( $rows ) {
					$rows = array_shift( $rows );
					$rows = array_shift( $rows );
				}

				return $rows;
			}
		}
		catch ( Exception $e ) {
			echo sprintf( '<div class="error"><p>%s</p></div>', $e->getMessage() );
		}

	}

	public function get_sessions_for_time_range( $start_date, $end_date ) {
		if ( $this->is_app_authenticated() ) {
			$id   = $this->get_single_ga_profile();
			$data = $this->get_metrics( $id, 'ga:sessions', $start_date, $end_date, 'ga:nthMonth,ga:year,ga:month', 'ga:nthMonth' );
			$rows = $data->getRows();

			// REMOVE
			WooCommerce_Grow_Helpers::add_debug_log( 'Time Range Rows: ' . print_r( $rows, true ) );

			return $rows;
		}
	}

	/**
	 * Check, if the analytics account is authenticated and has an access token.
	 * Authenticate it, if it is not.
	 *
	 * TODO: have the access token in a transient and check if the token expired before continuing
	 *
	 * @throws Exception
	 * @return bool
	 */
	function is_app_authenticated() {
		$access_token = WooCommerce_Grow_Helpers::get_option( 'access_token' );

		// REMOVE
		WooCommerce_Grow_Helpers::add_debug_log( 'Access Token: ' . $access_token );

		if ( ! empty( $access_token ) ) {

			// Set the token we have
			$this->ga_set_access_token( $access_token );

			// If the token is expired
			if ( $this->client->isAccessTokenExpired() ) { // TODO: ADD isAccessTokenExpired to the access token check
				$this->ga_refresh_token( $access_token );
			}
		} else {
			$auth_code = WooCommerce_Grow_Helpers::get_setting( 'authorization_code', 'new' );

			if ( empty( $auth_code ) ) {
				throw new Exception( __( 'No Authorization Code Found' ), 501 );
			}

			$access_token = $this->ga_authenticate( $auth_code );

			// Set the access token
			$this->ga_set_access_token( $access_token );

			// Save the token to the options
			$this->save_access_token( $access_token );
		}

		return true;
	}

	/**
	 * Returns the profile ID from the set Google UID
	 *
	 * TODO: move the use of this method to the UID setting. So we can have the setting value in an easy to get the Profile ID format
	 * TODO: Preferred format: UID:ProfileID
	 *
	 * @since 1.0
	 * @return array
	 * @throws Exception
	 */
	function get_single_ga_profile() {
		$ga_uid = WooCommerce_Grow_Helpers::get_setting( 'ga_api_uid' );
		if ( empty( $ga_uid ) ) {
			// TODO: TEST. HTML is escaped for error messages, so we may need to add a link in text form.
			throw new Exception(
				sprintf(
					__( 'The Google Account User ID is not set. Please visit %ssettings page%s to set the Google UID.', 'woocommerce-grow' ),
					'<a href="' . WooCommerce_Grow_Helpers::get_plugin_settings_page() . '" target="_blank">',
					'</a>'
				)
			);
		}

		list( $pre, $account_id, $post ) = explode( '-', $ga_uid );

		try {
			$profiles = $this->analytics->management_profiles->listManagementProfiles( $account_id, $ga_uid );
		}
		catch ( Exception $e ) {
			throw new Exception(
				sprintf(
					__( 'Analytics API error occurred. Error Code: %s. Error Message: %s  ', 'woocommerce-grow' ),
					$e->getCode(),
					$e->getMessage()
				)
			);
		}

		$profile_id = $profiles->items[0]->id;

		// If the profile is empty or it does not match the UID we chose to use.
		if ( empty( $profile_id ) || $ga_uid != $profiles->items[0]->webPropertyId ) {
			throw new Exception(
				sprintf(
					__( 'There is no Google Profile ID found from your Google UID. Please visit %ssettings page%s to set the Google UID.', 'woocommerce-grow' ),
					'<a href="' . WooCommerce_Grow_Helpers::get_plugin_settings_page() . '" target="_blank">',
					'</a>'
				)
			);
		}

		return $profile_id;
	}

	/**
	 * Returns all profiles associated with the Google account
	 *
	 * @throws Exception
	 * @return array
	 */
	function get_all_ga_profiles() {
		$profile_array = array();

		try {
			$profiles = $this->analytics->management_webproperties->listManagementWebproperties( '~all' );

			// REMOVE
			WooCommerce_Grow_Helpers::add_debug_log( 'Retrieved Profiles ALL: '.print_r( $profiles, true ) );
		}
		catch ( Exception $e ) {
			$message = sprintf( __( 'Analytics API error occurred. Error Code: %s. Error Message: %s  ', 'woocommerce-grow' ), $e->getCode(), $e->getMessage() );

			WooCommerce_Grow_Helpers::add_debug_log( 'All Profiles Exception error: '.$message );

			throw new Exception( $message );
		}

		if ( ! empty( $profiles->items ) ) {
			foreach ( $profiles->items as $profile ) {
				$profile_array[ $profile->id ] = str_replace( 'http://', '', $profile->name );
			}
		}

		// REMOVE
		WooCommerce_Grow_Helpers::add_debug_log( ' Formatted profiles: ' . print_r( $profile_array, true ) );

		return $profile_array;
	}

	/**
	 * Get a specific data metrics
	 *
	 * @since 1.0
	 *
	 * @param        string $account_id - The Account to query against
	 * @param        string $metrics    - The metrics to get
	 * @param        string $start_date - The start date to get
	 * @param        string $end_date   - The end date to get
	 * @param        string $dimensions - The dimensions to grab
	 * @param        string $sort       - The properties to sort on
	 * @param        string $filter     - The property to filter on
	 * @param        string $limit      - The number of items to get
	 *
	 * @throws Exception
	 *
	 * @return the specific metrics in array form
	 **/
	function get_metrics( $account_id, $metrics, $start_date, $end_date, $dimensions = null, $sort = null, $filter = null, $limit = null ) {
		$params = array();

		if ( $dimensions ) {
			$params['dimensions'] = $dimensions;
		}

		if ( $sort ) {
			$params['sort'] = $sort;
		}

		if ( $filter ) {
			$params['filters'] = $filter;
		}

		if ( $limit ) {
			$params['max-results'] = $limit;
		}

		$account_id = str_replace( 'ga:', '', $account_id );

		if ( ! $account_id ) {
			throw new Exception( __( 'GA account ID is not set.', 'woocommerce-grow' ) );
		}

		return $this->analytics->data_ga->get(
			'ga:' . $account_id,
			$start_date,
			$end_date,
			$metrics,
			$params
		);
	}

	/**
	 * @param $auth_code
	 *
	 * @throws Exception
	 * @return string
	 */
	private function ga_authenticate( $auth_code ) {
		try {
			// The authenticate method gets an access token, sets the access token
			// and returns the access token all at once.
			$access_token = $this->client->authenticate( $auth_code );

			return $access_token;
		}
		catch ( Exception $e ) {
			throw new Exception (
				sprintf(
					__(
						'Google Analytics was unable to authenticate you.
							Please refresh and try again. If the problem persists, please obtain a new authorizations token.
							Error Code: %s. Error Message: %s  ', 'woocommerce-grow'
					),
					$e->getCode(),
					$e->getMessage()
				)
			);
		}
	}

	/**
	 * @param $access_token
	 */
	private function save_access_token( $access_token ) {
		// Save the access token
		WooCommerce_Grow_Helpers::update_option( 'access_token', $access_token );

		// TODO: may be move this to a more appropriate place
		$this->save_refresh_token( $access_token );
	}

	/**
	 * @param $access_token
	 *
	 * @throws Exception
	 */
	private function ga_set_access_token( $access_token ) {
		try {
			$this->client->setAccessToken( $access_token );
		}
		catch ( Google_Auth_Exception $e ) {
			// TODO: Add error handling.
			$message = sprintf( __( 'Error: Unable to set Google Analytics access token. Error Code: %s. Error Message: %s  ', 'woocommerce-grow' ), $e->getCode(), $e->getMessage() );

			WooCommerce_Grow_Helpers::add_debug_log( 'Get Access token Error message: ' . $message );

			throw new Exception( $message );
		}
	}

	/**
	 * @param $access_token
	 *
	 * @throws Exception
	 */
	private function ga_refresh_token( $access_token ) {
		try {
			// Get the refresh token
			$refresh_token = $this->client->getRefreshToken();

			// Get a new access token
			$this->client->refreshToken( $refresh_token );

			// Get the new access token
			$refreshed_token = $this->client->getAccessToken();

			// REMOVE
			WooCommerce_Grow_Helpers::add_debug_log( 'Refreshed Access Token: ' . $refreshed_token );

			// Set the new token
			$this->ga_set_access_token( $refreshed_token );

			// Save the token to the options
			$this->save_access_token( $refreshed_token );
		}
		catch ( Google_Auth_Exception $e ) {
			// TODO: Add error handling.
			$message = sprintf( __( 'Error: Unable to refresh the Google Analytics access token. Error Code: %s. Error Message: %s ', 'woocommerce-grow' ), $e->getCode(), $e->getMessage() );

			WooCommerce_Grow_Helpers::add_debug_log( 'Get refresh token error message: '.$message );

			throw new Exception( $message );
		}
	}

	/**
	 * @param $access_token
	 */
	private function save_refresh_token( $access_token ) {
		// Save the refresh token
		$token = json_decode( $access_token, true );

		WooCommerce_Grow_Helpers::update_option( 'refresh_token', WooCommerce_Grow_Helpers::get_field( 'refresh_token', $token, '' ) );
	}

	/**
	 * Retrieves sessions from GA for tha passed months.
	 * Then stores the results in a transient option.
	 *
	 * Use __'grow-sessions-transient-expire-time'__ filter to modify how long the transient is valid for.
	 *
	 * @param $months_back
	 *
	 * @throws Exception
	 */
	private function get_sessions_months_back( $months_back ) {
		$start = date( 'Y-m-d', strtotime( '- ' . (int) $months_back . ' months', time() ) );
		$end   = date( 'Y-m-d', strtotime( 'midnight', time() ) );

		// Get the sessions for the time period
		$results = $this->get_sessions_for_time_range( $start, $end );

		// If we have an array as a result, we do have a result.
		if ( is_array( $results ) ) {
			foreach ( $results as $result ) {
				if ( ! is_array( $result ) ) {
					continue;
				}
				// 0 - Index
				// 1 - Year
				// 2 - Month
				// 3 - Sessions
				$this->sessions[ $result[1] ][ $result[2] ] = $result[3];
			}

			return WooCommerce_Grow_Helpers::set_transient(
				'stored_sessions',
				$this->sessions,
				apply_filters( 'grow-sessions-transient-expire-time', 24 * HOUR_IN_SECONDS )
			);
		} else {
			throw new Exception(
				sprintf(
					__( 'Call for sessions retrieval returned invalid results. Results: %s', 'woocommerce-grow' ),
					print_r( $results, true )
				)
			);
		}
	}

	/**
	 * Checks, if we have stored sessions in the database.
	 *
	 * @return bool
	 */
	public function has_stored_sessions() {
		$this->get_sessions();
		if ( false === $this->sessions ) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the stored sessions from the transient option
	 * and sets the result to the sessions variable
	 *
	 * @since 1.1
	 */
	private function get_sessions() {
		$this->sessions = WooCommerce_Grow_Helpers::get_transient( 'stored_sessions' );
	}

	/**
	 * Attempts to retrieve GA sessions for 24 months back.
	 *
	 * Use __"grow-retrieve-sessions-for-months"__ filter to modify how many months of sessions to retrieve.
	 *
	 * @since 1.1
	 *
	 * @throws Exception
	 */
	public function set_sessions() {
		$this->sessions = $this->get_sessions_months_back( apply_filters( 'grow-retrieve-sessions-for-months', 24 ) );
	}
}