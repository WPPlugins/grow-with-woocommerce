<?php
/*
 * Plugin Name: Grow with WooCommerce
 * Plugin URI:
 * Description: Grow your WooCommerce business with WooCommerce Grow
 * Version: 1.2
 * Author: Raison | Ivan Andreev
 * Author URI:
 *
 *	Copyright: (c) 2015 Raison | Ivan Andreev
 *	License: GNU General Public License v3.0
 *	License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// plugin update info

$file   = basename( __FILE__ );
$folder = basename( dirname( __FILE__ ) );
$hook = "in_plugin_update_message-{$folder}/{$file}";
add_action( $hook, 'update_message_wpse_87051', 10, 2 ); 

function update_message_wpse_87051( $plugin_data, $r )
{
    echo 'Plugin can now be found at YoGrow.co';
}


// notice

function wc_grow_admin_notice() {
    ?>
    <div class="error">
        <p><strong>Important:</strong> Grow with WooCommerce is now an online service. We've made it more awesome! Let's grow your WooCommerce store. <strong><a href="https://yogrow.co">Get your Free Trial with YoGrow Now</a></strong></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'wc_grow_admin_notice' );



/**
 * Check if WooCommerce is active
 **/
if ( ! function_exists( 'wc_grow_is_wc_active' ) ) {
	function wc_grow_is_wc_active() {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		if ( in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) {
			return true;
		}

		return false;
	}
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
class WooCommerce_Grow {
	/**
	 * Plugin path
	 * @var string
	 */
	private static $plugin_dir_path = null;

	/**
	 * Plugin url
	 * @var string
	 */
	private static $plugin_url = null;

	const VERSION = '1.0';

	const PREFIX = 'woocommerce_grow_';

	public function __construct() {
		$this->add_autoloader();

		$this->load_essentials();

		// Check, if WooCommerce is active before initializing the plugin
		if ( ! wc_grow_is_wc_active() ) {
			$notify = new WooCommerce_Grow_Notifications();
			$notify->no_wc_notice_hook();
		} else {
			$this->load_main();
		}
	}

	/**
	 * Load main plugin parts.
	 *
	 * @since 1.0
	 */
	public function load_main() {
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );

		add_filter( 'woocommerce_integrations', array( $this, 'load_plugin_settings' ) );

		if ( is_admin() ) {
			$this->load_pages();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->load_ajax();
		}

		$this->load_cron();
		$this->load_email_reports();

		add_action( 'admin_notices', array( $this, 'admin_notices_checks' ), 0 );
		add_action( 'admin_init', array( $this, 'maybe_display_email_template' ), 10 );
	}

	/**
	 * Check for plugin dependencies.
	 *
	 * @since 1.1
	 */
	public function admin_notices_checks() {
		$auth_code = WooCommerce_Grow_Helpers::get_setting( 'authorization_code' );
		$tab = WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, '' );

		if ( empty( $auth_code ) && ( '' === $tab || 'integration' !== $tab ) ) {
			$notify = new WooCommerce_Grow_Notifications();

			$notify->missing_ga_settings_notice_hook();
		}

		if ( 'wc-settings' == WooCommerce_Grow_Helpers::get_field( 'page', $_GET, '' )
			&& 'integration' === $tab
		) {
			$notify = new WooCommerce_Grow_Notifications();

			$notify->email_report_notice_hook();
		}
	}

	/**
	 * Returns the email template content.
	 * Prepares the email report for the current month and outputs it to the page.
	 *
	 * @since 1.1
	 */
	public function maybe_display_email_template() {
		if ( 'true' === WooCommerce_Grow_Helpers::get_field( 'preview_woocommerce_grow_mail', $_GET, '' ) ) {
			if ( ! wp_verify_nonce( WooCommerce_Grow_Helpers::get_field( '_wpnonce', $_REQUEST, '' ), 'grow-preview-mail') ) {
				die( 'Security check' );
			}

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
			$report_email->set_heading();
			$report_email->set_header();
			$report_email->set_content();
			$report_email->set_footer();

			echo $report_email->get_email_content();
			exit;
		}
	}

	/**
	 * Load Essential part of the plugin. Only the basics.
	 *
	 * @since 1.0
	 */
	public function load_essentials() {
		$this->load_text_domain();

		// Add a 'Settings' link to the plugin action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settings_support_link' ), 10, 4 );
	}

	/**
	 * Add the plugin autoloader for class files
	 *
	 * @since 1.0
	 */
	public function add_autoloader() {
		require_once( 'includes/class-woocommerce-grow-autoloader.php' );
		$loader = new WooCommerce_Grow_Autoloader( self::get_plugin_path() );
		spl_autoload_register( array( $loader, 'load_classes' ) );
	}

	/**
	 * Load Text domains
	 *
	 * @since 1.0
	 */
	public function load_text_domain() {
		// Add localization on init so WPML to be able to use it.
		load_textdomain( 'woocommerce-grow', WP_LANG_DIR . '/woocommerce-grow/woocommerce-grow-' . get_locale() . '.mo' );
		load_plugin_textdomain( 'woocommerce-grow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add Integrations settings page
	 *
	 * @since 1.0
	 *
	 * @param $integrations The current integrations
	 *
	 * @return array
	 */
	public function load_plugin_settings( $integrations ) {
		$integrations[] = 'WooCommerce_Grow_Settings';

		return $integrations;
	}

	/**
	 * Load the Google SDK
	 *
	 * @since 1.0
	 */
	public static function load_google_analytics() {
		require_once( 'includes/vendor/google-api-php-client/src/Google/autoload.php' );
	}

	/**
	 * Load Pages
	 *
	 * @since 1.0
	 */
	public function load_pages() {
		$pages = new WooCommerce_Grow_Menu();
		$pages->hooks();
	}

	/**
	 * Loads Ajax
	 *
	 * @since 1.0
	 */
	public function load_ajax() {
		$ajax = new WooCommerce_Grow_Ajax();
		$ajax->hooks();
	}

	/**
	 * Loads Grow Cron class
	 *
	 * @since 1.1
	 */
	public function load_cron() {
		$cron = new WooCommerce_Grow_Cron();
		$cron->hooks();
	}

	/**
	 * Loads Grow Cron class
	 *
	 * @since 1.1
	 */
	public function load_email_reports() {
		$cron = new WooCommerce_Grow_Emails();
		$cron->email_report_hook();
	}

	/**
	 * Add Gateway admin scripts
	 *
	 * @since 1.0
	 */
	public function add_scripts() {
		$screen = get_current_screen();
		if ( 'woocommerce_page_woocommerce-grow' == $screen->id ) {
			$scripts = new WooCommerce_Grow_Scripts();
			$scripts->register_admin_scripts();

			if ( 'woocommerce-grow' == WooCommerce_Grow_Helpers::get_field( 'page', $_GET, '' ) ) {
				if ( in_array( WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, '' ), array( '', 'dashboard' ) ) ) {
					$scripts->register_dashboard_scripts();
					$scripts->register_dashboard_localized_scripts();
				}

				if ( 'targets' == WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, 'targets' ) ) {
					$scripts->register_targets_scripts();
				}

				if ( in_array( WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, '' ), array( '', 'dashboard', 'targets' ) ) ) {
					$scripts->add_admin_styles();
				}
			}
		}

		if ( 'wc-settings' == WooCommerce_Grow_Helpers::get_field( 'page', $_GET, '' )
			&& 'integration' == WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, '' )
		) {
			$scripts = new WooCommerce_Grow_Scripts();
			$scripts->register_settings_scripts();
		}
	}

	/**
	 * Get plugin url
	 *
	 * @since 1.0
	 * @return string
	 */
	public static function get_plugin_url() {
		if ( null === self::$plugin_url ) {
			self::$plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		return self::$plugin_url;
	}

	/**
	 * Get plugin path
	 *
	 * @since 1.0
	 * @return string
	 */
	public static function get_plugin_path() {
		if ( null === self::$plugin_dir_path ) {
			self::$plugin_dir_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		return self::$plugin_dir_path;
	}

	/** Add 'Settings' link to the plugin actions links
	 *
	 * @since 1.0
	 * @return array associative array of plugin action links
	 */
	public function settings_support_link( $actions, $plugin_file, $plugin_data, $context ) {
		return array_merge(
			array( 'settings' => sprintf( '<a href="%s">' . __( 'Settings', 'woocommerce-grow' ) . '</a>', WooCommerce_Grow_Helpers::get_plugin_settings_page() ) ),
			array( 'dashboard' => sprintf( '<a href="%s">' . __( 'Dashboard', 'woocommerce-grow' ) . '</a>', WooCommerce_Grow_Helpers::get_grow_page_url() ) ),
			array( 'targets' => sprintf( '<a href="%s">' . __( 'Targets', 'woocommerce-grow' ) . '</a>', WooCommerce_Grow_Helpers::get_grow_page_url( 'targets' ) ) ),
			$actions
		);
	}
}

/**
 * Load the plugin
 */
add_action( 'plugins_loaded', 'load_wc_grow_plugin' );
function load_wc_grow_plugin() {
	new WooCommerce_Grow();
}