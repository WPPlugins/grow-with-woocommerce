<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add/Manage plugin menu and all pages under it.
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Menu {
	/**
	 * Load hooks to add plugin pages
	 *
	 * @since 1.0
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 11 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'screen_ids' ) );
		add_filter( 'woocommerce_reports_screen_ids', array( $this, 'screen_ids' ) );
	}

	/**
	 * Add Grow menu under WooCommerce one.
	 *
	 * @since 1.0
	 */
	public function admin_menu() {
		$page = add_submenu_page(
			'woocommerce',
			__( 'Grow', 'woocommerce-grow' ),
			__( 'Grow', 'woocommerce-grow' ),
			'manage_woocommerce',
			'woocommerce-grow',
			array( $this, 'pages' )
		);

		add_action( 'admin_print_styles-' . $page, array( $this, 'scripts' ) );
	}

	/**
	 * Adds a filter to allow for external scripts to be loaded.
	 * Scripts need to be registered and then their handle added to the scripts array.
	 * 'woocommerce_grow_scripts_to_enqueue' filter will load the scripts.
	 *
	 * @since 1.0
	 */
	public function scripts() {
		// Load scripts for the Grow Pages
		$scripts = apply_filters( 'woocommerce_grow_scripts_to_enqueue', array() );

		if ( empty( $scripts ) ) {
			return;
		}

		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script );
		}
	}

	/**
	 * Add screen ID to the WooCommerce screen IDs.
	 * This will allow to add all available scripts to the Grow pages, too.
	 *
	 * @since 1.0
	 */
	public function screen_ids( $ids ) {
		$screen_id = strtolower( _x( 'WooCommerce', 'Pages screen id', 'woocommerce-grow' ) );
		$ids[]     = $screen_id . '_page_woocommerce-grow';

		return $ids;
	}

	/**
	 * Manage pages
	 *
	 * @since 1.0
	 */
	public function pages() {
		// Anything you need to do before a page is loaded
		do_action( 'woocommerce_grow_pages_start' );

		// Get current tab
		$current_tab = '' == WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, '' ) ? 'dashboard' : sanitize_title( WooCommerce_Grow_Helpers::get_field( 'tab', $_GET, '' ) );

		$this->add_woocommerce_grow_pages();

		// Save settings if data has been posted
		if ( ! empty( $_POST ) ) {
			$this->save( $current_tab );
		}

		// Add any posted messages
		if ( '' != WooCommerce_Grow_Helpers::get_field( 'wc_grow_error', $_GET, '' ) ) {
			WC_Admin_Settings::add_error( stripslashes( WooCommerce_Grow_Helpers::get_field( 'wc_error', $_GET, '' ) ) );
		}

		if ( '' != WooCommerce_Grow_Helpers::get_field( 'wc_grow_message', $_GET, '' ) ) {
			WC_Admin_Settings::add_message( stripslashes( WooCommerce_Grow_Helpers::get_field( 'wc_message', $_GET,'' ) ) );
		}

		WC_Admin_Settings::show_messages();

		// Add tabs on the Grow page
		$tabs = apply_filters( 'woocommerce_grow_page_tabs_array', array() );

		include 'views/html-grow-pages.php';
	}

	/**
	 * Allow for the settings to be saved against a particular tab
	 *
	 * @since 1.0
	 *
	 * @param $current_tab
	 */
	public function save( $current_tab ) {
		do_action( 'woocommerce_grow_save_page_settings_' . $current_tab );
	}

	/**
	 * Add the Grow pages.
	 * Use 'woocommerce_grow_settings_page_classes_array' filter to add your own pages.
	 * All you need to do is add your Page class name to the 'pages' array.
	 *
	 * @since 1.0
	 */
	private function add_woocommerce_grow_pages() {
		// Add all settings page Classes
		$pages   = array();
		$pages[] = 'WooCommerce_Grow_Page_Dashboard';
		$pages[] = 'WooCommerce_Grow_Page_Targets';

		$pages = apply_filters( 'woocommerce_grow_settings_page_classes_array', $pages );

		foreach ( $pages as $page ) {
			if ( class_exists( $page ) ) {
				new $page();
			}
		}
	}
}