<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Grow email reports class
 *
 * @since  1.1
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class  WooCommerce_Grow_Email_Report extends WooCommerce_Grow_Email {
	public $header_template;
	public $footer_template;
	public $report;
	public $feed;

	/**
	 * Class constructor sets all needed variables.
	 */
	public function __construct() {
		$this->id = 'grow-weekly-report';
		$this->set_subject();
		$this->set_recipients();
		$this->set_sender();

		parent::__construct();
	}

	/**
	 * Sets the email subject.
	 *
	 * @since 1.1
	 */
	public function set_subject() {
		$this->subject = __( 'Weekly Grow Report', 'woocommerce-grow' );
	}

	/**
	 * Sets the sender email.
	 *
	 * @since 1.1
	 */
	public function set_sender() {
	    	$this->sender = WooCommerce_Grow_Helpers::get_setting( 'email_sender', 'old', get_option( 'admin_email' ) );
	}

	/**
	 * Sets the report objects to a class variable
	 *
	 * @since 1.1
	 *
	 * @param $report
	 */
	public function set_report( $report ) {
		$this->report = $report;
	}

	/**
	 * Sets the feed content to a class variable.
	 *
	 * @since 1.1
	 *
	 * @param $feed
	 */
	public function set_feeds( $feed ) {
		$this->feed = $feed;
	}

	/**
	 * Sets the recipient emails.
	 *
	 * @since 1.1
	 */
	public function set_recipients() {
		$recipients = explode( "\n", trim( WooCommerce_Grow_Helpers::get_setting( 'email_recipients', 'old', get_option( 'admin_email' ) ) ) );

		$this->recipients = implode( ', ', $recipients );
	}

	/**
	 * Returns the set report objects.
	 *
	 * @since 1.1
	 *
	 * @return mixed
	 */
	public function get_report() {
		return $this->report;
	}

	/**
	 * Returns the set article feeds.
	 *
	 * @since 1.1
	 *
	 * @return mixed
	 */
	public function get_feeds() {
		return $this->feed;
	}

	/**
	 * Sets the email heading. Inside header content.
	 *
	 * Use 'woocommerce_grow_email_template_file_heading' to overwrite the email heading template.
	 *
	 * @since 1.1
	 */
	public function set_heading() {
		$this->heading_template = apply_filters( 'woocommerce_grow_email_template_file_heading', 'views/grow-report/heading.php', $this->id );

		if ( $this->heading_template ) {
			ob_start();

			include( $this->heading_template );

			$this->heading = ob_get_clean();
		}
	}

	/**
	 * Sets the email header.
	 *
	 * Use 'woocommerce_grow_email_template_file_header' to overwrite the email header template.
	 *
	 * @since 1.1
	 */
	public function set_header() {
		$this->header_template = apply_filters( 'woocommerce_grow_email_template_file_header', 'views/header.php', $this->id );

		if ( $this->header_template ) {
			ob_start();

			include( $this->header_template );

			$this->header = ob_get_clean();
		}
	}

	/**
	 * Sets the email content.
	 *
	 * Use 'woocommerce_grow_email_template_file_content' to overwrite the email content template.
	 *
	 * @since 1.1
	 */
	public function set_content() {
		$this->content_template = apply_filters( 'woocommerce_grow_email_template_file_content', 'views/grow-report/content.php', $this->id );

		if ( $this->content_template ) {
			ob_start();

			include( $this->content_template );

			$this->content = ob_get_clean();
		}
	}

	/**
	 * Sets the email footer.
	 *
	 * Use 'woocommerce_grow_email_template_file_footer' to overwrite the email footer template.
	 *
	 * @since 1.1
	 */
	public function set_footer() {
		$this->footer_template = apply_filters( 'woocommerce_grow_email_template_file_footer', 'views/footer.php', $this->id );

		if ( $this->footer_template ) {
			ob_start();

			include( $this->footer_template );

			$this->footer = ob_get_clean();
		}
	}

	/**
	 * Returns the email content.
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function get_email_content() {
		ob_start();

		echo $this->get_header();
		echo $this->get_content();
		echo $this->get_footer();

		return ob_get_clean();
	}

	/**
	 * Send the email.
	 *
	 * @since 1.1
	 *
	 * @return bool
	 */
	public function send_report() {
		$this->set_heading();
		$this->set_header();
		$this->set_content();
		$this->set_footer();

		return $this->send_email( $this->get_recipients(), $this->get_subject(), $this->get_email_content(), $this->get_headers(), $this->get_attachments() );
	}


}