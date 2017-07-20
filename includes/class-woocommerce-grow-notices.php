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
class WooCommerce_Grow_Notices {

	private $is_shown_dismissible_notice = false;
	private static $is_notices_js_added = false;

	public function __construct() {
		// Add the notices JS only once
		if ( false === self::$is_notices_js_added ) {
			add_action( 'admin_notices', array( $this, 'add_notices_js' ), 15 );
			self::$is_notices_js_added = true;
		}
	}

	/**
	 * Add dismissible notice javascript
	 *
	 * @since 1.0
	 */
	public function add_notices_js() {
		if ( ! $this->is_shown_dismissible_notice ) {
			return;
		}

		wp_enqueue_script( 'wc-grow-dismiss-notices' );
	}

	/**
	 * Adds the given $message as a dismissible notice identified by $message_id
	 *
	 * @since 1.0
	 *
	 * @param string $message_id
	 *
	 * @return string
	 */
	public function add_dismissible_link( $message_id ) {
		$dismiss_link = sprintf(
			'<a href="#" class="wc-grow-dismiss-notice" data-message-id="%s">%s</a>',
			$message_id,
			__( 'Dismiss', 'woocommerce-grow' )
		);

		$this->is_shown_dismissible_notice = true;

		return $dismiss_link;
	}

	/**
	 * Returns true if the identified admin message has been dismissed for the
	 * given user
	 *
	 * @since 1.0
	 *
	 * @param string $message_id the message identifier
	 * @param int    $user_id    optional user identifier, defaults to current user
	 *
	 * @return boolean true if the message has been dismissed by the admin user
	 */
	public function is_message_dismissed( $message_id, $user_id = null ) {

		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		// Message is dismissed for the user and plugin version,
		// so if the same case appears in the later version,
		// the message will be shown again.
		$dismissed_messages = get_user_meta( $user_id, '_' . WooCommerce_Grow::PREFIX . 'dismissed_notice' . '_' . WooCommerce_Grow::VERSION, true );

		return isset( $dismissed_messages[ $message_id ] ) && $dismissed_messages[ $message_id ];
	}

	/**
	 * Add error notice
	 *
	 * @param $error
	 */
	public function add_error_notice( $error ) {
	    	echo sprintf( '<div id="message" class="error"><p>%s</p></div>', $error );
	}

	/**
	 * Add message notice
	 *
	 * @param $message
	 */
	public function add_message_notice( $message ) {
	    	echo sprintf( '<div id="message" class="updated fade"><p>%s</p></div>', $message );
	}
}