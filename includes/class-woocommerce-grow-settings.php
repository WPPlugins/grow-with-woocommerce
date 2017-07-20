<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin settings page
 *
 * @since  1.0
 * @author VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class WooCommerce_Grow_Settings extends WC_Integration {

	public function __construct() {
		$this->id                 = 'woocommerce_grow';
		$this->method_title       = __( 'Grow', 'woocommerce-grow' );
		$this->method_description = __(
			'Here you will setup your Google Analytics
			Credentials and allow WooCommerce Grow to use your GA account.', 'woocommerce-grow'
		);

		// Load the settings.
		$this->init_settings();

		// Load admin form
		$this->init_form_fields();

		// Hooks
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'actions_before_saved_settings' ), 5 );
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'actions_after_saved_settings' ), 20 );
	}

	/**
	 * Init integration form fields
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters(
			'woocommerce_grow_settings_array',
			array(
				'ga_options_title'      => array(
					'title'       => __( 'GA Settings', 'woocommerce-grow' ),
					'description' => __( 'Settings to setup and authorize your GA account.', 'woocommerce-grow' ),
					'type'        => 'title',
					'desc_tip'    => true
				),
				// TODO: Highlight the Code field
				'ga_authentication'     => array(
					'title'       => __( 'Authentication', 'woocommerce-grow' ),
					'description' => __( 'Grant access to your Google account.', 'woocommerce-grow' ),
					'type'        => 'ga_auth_button',
					'class'       => 'button',
					'desc_tip'    => true
				),
				// TODO: Hide the token for security. May be show if it is set, or add remove token, or reset tokens when the Allow access is clicked.
				'authorization_code'    => array(
					'title'       => __( 'Authorization Code', 'woocommerce-grow' ),
					'description' => __( 'Enter in the field the Authorization code provided to you, when you allow access to your Google account.', 'woocommerce-grow' ),
					'type'        => 'password',
					'placeholder' => __( 'Paste Code Here', 'woocommerce-grow' )
				),
				'ga_api_uid'            => array(
					'title'       => __( 'Google Account UID', 'woocommerce-grow' ),
					'description' => __( 'Choose the account User ID.', 'woocommerce-grow' ),
					'type'        => 'wc_grow_uids',
					'class'       => 'wc-enhanced-select chosen_select'
				),
				'email_title'           => array(
					'title'       => __( 'Email Settings', 'woocommerce-grow' ),
					'description' => __( 'Settings to setup email reports.', 'woocommerce-grow' ),
					'type'        => 'title',
					'desc_tip'    => true
				),
				'email_enabled'         => array(
					'title'       => __( 'Enable Email Reports', 'woocommerce-grow' ),
					'description' => __( 'Enables weekly email reports.', 'woocommerce-grow' ),
					'type'        => 'checkbox',
					'default'     => 'yes',
					'desc_tip'    => true
				),
				'email_sender'          => array(
					'title'       => __( 'Email From', 'woocommerce-grow' ),
					'description' => __( 'Please enter the email sending the report.', 'woocommerce-grow' ),
					'type'        => 'text',
					'default'     => get_option( 'admin_email' ),
					'desc_tip'    => true
				),
				'email_recipients'      => array(
					'title'       => __( 'Email Recipients', 'woocommerce-grow' ),
					'description' => __( 'Please enter the report recipient emails. Please enter one email per line.', 'woocommerce-grow' ),
					'type'        => 'textarea',
					'default'     => get_option( 'admin_email' ),
					'desc_tip'    => true
				),
				'email_day_of_the_week' => array(
					'title'       => __( 'Email Report Every', 'woocommerce-grow' ),
					'description' => __( 'Please choose, which day of the week to email the report on.', 'woocommerce-grow' ),
					'type'        => 'select',
					'default'     => 'monday',
					'options'     => array(
						'monday'    => __( 'Monday', 'woocommerce-grow' ),
						'tuesday'   => __( 'Tuesday', 'woocommerce-grow' ),
						'wednesday' => __( 'Wednesday', 'woocommerce-grow' ),
						'thursday'  => __( 'Thursday', 'woocommerce-grow' ),
						'friday'    => __( 'Friday', 'woocommerce-grow' ),
						'saturday'  => __( 'Saturday', 'woocommerce-grow' ),
						'sunday'    => __( 'Sunday', 'woocommerce-grow' ),
					),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select chosen_select',
				),
				'email_report_type'     => array(
					'title'       => __( 'Report Data Type', 'woocommerce-grow' ),
					'description' => __( 'Please choose, what type of data should the report include.', 'woocommerce-grow' ),
					'type'        => 'select',
					'default'     => 'month_on_month',
					'options'     => array(
						'targets'        => __( 'Targets', 'woocommerce-grow' ),
						'month_on_month' => __( 'Month On Month', 'woocommerce-grow' ),
						'year_on_year'   => __( 'Year On Year', 'woocommerce-grow' ),
					),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select chosen_select',
				),
				'email_force_report'    => array(
					'title'       => __( 'Send Email', 'woocommerce-grow' ),
					'label'       => __( 'Send Sample Email' ),
					'description' => __( 'Press the button to send a sample email to the recipients.', 'woocommerce-grow' ) . '<br/>' . sprintf( __('%sClick here to preview the email template.%s', 'woocommerce-grow'), '<a href="'.wp_nonce_url( admin_url( '?preview_woocommerce_grow_mail=true' ), 'grow-preview-mail' ).'" target="_blank">', '</a>' ),
					'type'        => 'report_email_button',
					'class'       => 'button send-report-button',
					'desc_tip'    => false
				),
				'debug_title'           => array(
					'title'       => __( 'Debug Settings', 'woocommerce-grow' ),
					'description' => __( 'Settings to enable or disable debug mode.', 'woocommerce-grow' ),
					'type'        => 'title',
					'desc_tip'    => true
				),
				'debug_enabled'         => array(
					'title'       => __( 'Enable Debug Mode', 'woocommerce-grow' ),
					'description' => __( 'Enabled debug mode will log vital sections of your integration, which will help with troubleshooting.', 'woocommerce-grow' ),
					'type'        => 'checkbox',
					'default'     => 'no',
					'desc_tip'    => true
				),
			)
		);
	}

	/**
	 * Custom field for the Google Account UID
	 *
	 * @since 1.0
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return string
	 */
	public function generate_wc_grow_uids_html( $key, $data ) {
		$field    = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array()
		);

		$data               = wp_parse_args( $data, $defaults );
		$authorization_code = $this->get_option( 'authorization_code' );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<?php if ( '' != $authorization_code ) :
						$uids = $this->get_ga_account_ids();
						if ( WooCommerce_Grow_Helpers::get_field( 'error', $uids ) ) {
							$data['options']       = array( 'No Account UIDs Found' );
							$data['error_message'] = WooCommerce_Grow_Helpers::get_field( 'error', $uids );
						} else {
							$data['options'] = $uids;
						}

						// TODO: set a default account on activation
						?>
						<legend class="screen-reader-text">
							<span><?php echo wp_kses_post( $data['title'] ); ?></span>
						</legend>
						<select class="select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
							<option value=""><?php echo esc_html( __( 'Please select UID' ) ); ?></option>
							<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php if ( isset( $data['error_message'] ) ) { ?>
						<div class="error">
							<p><?php echo esc_html( $data['error_message'] ); ?></p></div>
					<?php } ?>
						<?php echo $this->get_description_html( $data ); ?>
					<?php else : ?>
						<p class="description">
							<?php _e( 'Please Save the Autorization Code. You will then be able to set the Google Account UID.', 'woocommerce-grow' ) ?>
						</p>
					<?php endif; ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Custom field for the Google Account Access Button
	 *
	 * @since 1.0
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return string
	 */
	public function generate_report_email_button_html( $key, $data ) {
		$field    = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo wp_kses_post( $data['title'] ); ?></span>
					</legend>
					<a href="<?php echo esc_url( WooCommerce_Grow_Helpers::get_ajax_url( 'grow_report_email_send', 'woocommerce-grow-send-report-email' ) ); ?>"
						<?php disabled( $data['disabled'], true ); ?>
					   id="<?php echo esc_attr( $field ); ?>"
					   class="<?php echo esc_attr( $data['class'] ); ?>"
					   style="<?php echo esc_attr( $data['css'] ); ?>"
						<?php echo $this->get_custom_attribute_html( $data ); ?>>
						<?php echo esc_attr( $data['label'] ); ?>
					</a>
					<br />
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php

		wp_enqueue_script( 'wc-grow-settings' );

		return ob_get_clean();
	}

	/**
	 * Custom field for the Google Account Access Button
	 *
	 * @since 1.0
	 *
	 * @param $key
	 * @param $data
	 *
	 * @return string
	 */
	public function generate_ga_auth_button_html( $key, $data ) {
		$field    = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );
		$ga   = WooCommerce_Grow_Google_Analytics::get_instance();

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php echo wp_kses_post( $data['title'] ); ?></span>
					</legend>
					<a href="<?php echo esc_url( $ga->get_authentication_url() ); ?>"
						<?php disabled( $data['disabled'], true ); ?>
					   id="<?php echo esc_attr( $field ); ?>"
					   class="<?php echo esc_attr( $data['class'] ); ?>"
					   data-redirect-url="<?php echo esc_url( $ga->get_authentication_url() ); ?>"
					   target="_blank"
					   style="<?php echo esc_attr( $data['css'] ); ?>"
						<?php echo $this->get_custom_attribute_html( $data ); ?>>
						<?php echo esc_attr( __( 'Allow Access to your Google account', 'woocommerce-grow' ) ) ?>
					</a>
					<br />
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php

		wp_enqueue_script( 'wc-grow-settings' );

		return ob_get_clean();
	}

	/**
	 * Perform actions up after saving setting
	 *
	 * @since 1.0
	 */
	public function actions_after_saved_settings() {
		// For any additional action needed to be taken on save settings
		$auth_code = WooCommerce_Grow_Helpers::get_setting( 'authorization_code', 'new' );
		$uid       = WooCommerce_Grow_Helpers::get_setting( 'ga_api_uid', 'new' );
		if ( '' != $auth_code && '' != $uid ) {
			$message = sprintf(
				__( 'Visit your %sGrow Dashboard%s page.', 'woocommerce-grow' ),
				'<a href="' . WooCommerce_Grow_Helpers::get_grow_page_url( 'dashboard' ) . '">',
				'</a>'
			);
			$message .= '<br/>' . __( '-OR-', 'woocommerce-grow' ) . '<br/>';
			$message .= sprintf(
				__( 'Visit your %sGrow Targets%s page to setup your targets.', 'woocommerce-grow' ),
				'<a href="' . WooCommerce_Grow_Helpers::get_grow_page_url( 'targets' ) . '">',
				'</a>'
			);

			$notices = new WooCommerce_Grow_Notices();

			$notices->add_error_notice( $message );

			// Remove stored ga sessions. In case a new account is saved.
			WooCommerce_Grow_Helpers::delete_transient( 'stored_sessions' );
			try{
				$ga = new WooCommerce_Grow_Google_Analytics();
				if ( ! $ga->has_stored_sessions() ) {
					$ga->set_sessions();
				}
			} catch (Exception $e) {
				// no errors, since this is only an attempt
			}
		}


	}

	/**
	 * Perform actions up before saving setting
	 *
	 * @since 1.0
	 */
	public function actions_before_saved_settings() {
		// Do action before we save the settings.
		// Here we can check if the authorization code changed and we can clear all access and refresh tokens
		// This way the plugin will obtain a new/fresh tokens

		$auth_code = WooCommerce_Grow_Helpers::get_setting( 'authorization_code', 'new' );

		// Check the current Auth Code and the one to be saved.
		// If they are different, reset the access and refresh tokens
		if ( $auth_code != WooCommerce_Grow_Helpers::get_field( $this->plugin_id . $this->id . '_authorization_code', $_POST, '' ) ) {
			WooCommerce_Grow_Helpers::update_option( 'access_token', '' );
			WooCommerce_Grow_Helpers::update_option( 'refresh_token', '' );
		}
	}

	/**
	 * Checks if the WordPress API is a valid method for selecting an account
	 *
	 * @return a list of accounts if available, false if none available
	 **/
	function get_ga_account_ids() {
		$accounts = array();

		$ga = WooCommerce_Grow_Google_Analytics::get_instance();

		try {
			$ga->is_app_authenticated();
		}
		catch ( Exception $e ) {
			return array( 'error' => $e->getMessage() );
		}

		try {
			// Get user available profiles
			$accounts = $ga->get_all_ga_profiles();

			natcasesort( $accounts );

			// TODO: set a default account on activation
			if ( count( $accounts ) > 0 ) {
				return $accounts;
			} else {
				throw new Exception( __( 'No Account UIDs Found.', 'woocommerce-grow' ) );
			}
		}
		catch ( Exception $e ) {
			return array( 'error' => $e->getMessage() );
		}
	}
}