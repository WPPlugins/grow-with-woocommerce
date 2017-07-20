<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Grow email class.
 *
 * @since  1.1
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
abstract class WooCommerce_Grow_Email {
	public $id;
	public $subject;
	public $content;
	public $heading;
	public $email_type;
	public $recipients;
	public $sender;
	public $template;
	public $attachments;
	public $footer;
	public $header;

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->email_type = 'html';
	}

	/**
	 * Returns the email headers
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_headers() {
		return apply_filters( 'woocommerce_grow_email_headers', "Content-Type: " . $this->get_content_type() . "\r\n", $this->id );
	}

	/**
	 * Returns the email header
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_header() {
		return apply_filters( 'woocommerce_grow_email_headers', $this->header, $this->id );
	}

	/**
	 * Returns the email subject
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_subject() {
		return apply_filters( 'woocommerce_grow_email_subject', $this->subject, $this->id );
	}

	/**
	 * Returns the email content
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_content() {
		return apply_filters( 'woocommerce_grow_email_content', $this->content, $this->id );
	}

	/**
	 * Returns the email heading
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_heading() {
		return apply_filters( 'woocommerce_grow_email_heading', $this->heading, $this->id );
	}

	/**
	 * Returns the email footer
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_footer() {
		return apply_filters( 'woocommerce_grow_email_footer', $this->footer, $this->id );
	}

	/**
	 * Returns the email set attachments.
	 *
	 * @since 1.1
	 *
	 * @return mixed
	 */
	public function get_attachments() {
	    	return $this->attachments;
	}

	/**
	 * Returns the email type.
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_email_type() {
		return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
	}

	/**
	 * Returns the email content type.
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_content_type() {
		if ( 'html' == $this->get_email_type() ) {
			return 'text/html';
		} else {
			return 'text/plain';
		}
	}

	/**
	 * Returns the email sender.
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_sender() {
		return apply_filters( 'woocommerce_grow_email_sender', $this->sender, $this->id );
	}

	/**
	 * Returns the email recipients.
	 *
	 * @since 1.1
	 *
	 * @return mixed|void
	 */
	public function get_recipients() {
		return apply_filters( 'woocommerce_grow_email_recipients', $this->recipients, $this->id );
	}

	/**
	 * Sends the generated email.
	 *
	 * @since 1.1
	 *
	 * @param $recipient
	 * @param $subject
	 * @param $message
	 * @param $headers
	 * @param $attachments
	 *
	 * @return bool
	 */
	public function send_email( $recipient, $subject, $message, $headers, $attachments ) {
		add_filter( 'wp_mail_from', array( $this, 'get_sender' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$return  = wp_mail( $recipient, $subject, $message, $headers, $attachments );

		remove_filter( 'wp_mail_from', array( $this, 'get_sender' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		return $return;
	}
}