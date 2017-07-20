<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce_Grow_Notices' ) ) {
	include_once( 'class-woocommerce-grow-notices.php' );
}

/**
 * Handles notification notices
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Notifications extends WooCommerce_Grow_Notices {
	public function missing_ga_settings_notice_hook() {
		add_action( 'admin_notices', array( $this, 'notice_ga_settings_not_set' ), 10 );
	}

	public function no_wc_notice_hook() {
		add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
	}

	public function email_report_notice_hook() {
		add_action( 'admin_notices', array( $this, 'email_report_notice' ), 11 );
	}

	/**
	 * Display WooCommerce not active notice
	 *
	 * @since  1.0
	 * @access public
	 */
	public function notice_activate_wc() {
		$message = sprintf(
			__(
				'Grow with WooCommerce plugin requires %1$sWooCommerce%2$s to function.
			 Please Install and Activate %1$sWooCommerce%2$s.', 'woocommerce-grow'
			),
			'<a href="' . admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) . '">',
			'</a>'
		);

		$look_for = '<br/>' . __( 'If you have WooCommerce installed and activated, but you still see this message, please make sure that the WooCommerce folder is called "woocommerce" and not something else.', 'woocommerce-grow' );

		$this->add_error_notice( $message . $look_for );
	}

	/**
	 * Settings are not set
	 *
	 * @since 1.0
	 */
	public function notice_ga_settings_not_set() {
		$error = sprintf(
			__( 'Thanks for installing Grow with WooCommerce! %sClick here%s to integrate your Google Analytics account', 'woocommerce-grow' ),
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=woocommerce_grow' ) . '">',
			'</a>'
		);

		$this->add_error_notice( $error );
	}

	/**
	 * Returns the ajax email report notice message.
	 *
	 * @since 1.1
	 */
	public function email_report_notice() {
		if ( 'failed' == WooCommerce_Grow_Helpers::get_field( 'grow-emailed', $_GET, '' ) ) {
			$error_message = '';
			if ( '' !== WooCommerce_Grow_Helpers::get_field( 'grow-failed-reason', $_GET, '' ) ) {
				$error_message = sprintf( __( 'Error message: %s', 'woocommerce-grow' ), WooCommerce_Grow_Helpers::get_field( 'grow-emailed', $_GET, '' ) );
			}

			$error = sprintf( __( 'Report email was not send. %s', 'woocommerce-grow' ), $error_message );

			$this->add_error_notice( $error );
		} elseif ( 'success' == WooCommerce_Grow_Helpers::get_field( 'grow-emailed', $_GET, '' ) ) {
			$message = __( 'Report email successfully sent.', 'woocommerce-grow' );

			$this->add_message_notice( $message );
		}
	}
}