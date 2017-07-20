<?php
/**
 * Outputs the Targets page HTML
 *
 * @since  1.0
 * @author Raison | Ivan Andreev
 */

?>
<div class="page-content-wrapper">
	<div class="wc-grow-help-overlay-wrapper" style="display: none;">
		<div class="wc-grow-help-overlay"></div>
		<div class="wc-grow-help-growl">
			<img src="<?php echo WooCommerce_Grow::get_plugin_url() . '/assets/img/growl_400.png'; ?>" />

			<div class="grow-helt-text">
				<h2><?php echo __( 'Big Update: Now launched as YoGrow' ) ?></h2>
				<p>First a BIG thanks for all the support and feedback from the WordPress and Woo community. You're awesome!</p>
				<p>Grow has grown up into <a href="https://yogrow.co">YoGrow</a>! We've rebuilt from the ground up to help you increase your sales. We know WooCommerce stores love it and know you will too.</p>
				<p><a href="https://yogrow.co">Click here to get your free trial</a></p>
				<p class="aside">We're keeping this plugin going for the next few months. Click on the chap on your left to resume use.</p>
			</div>
		</div>
	</div>
	
	<form method="post" enctype="multipart/form-data">
		<div class="wc-grow-cards-wrapper">
			<?php
			// TODO: Add months to plugin activation hook
			$months          = WooCommerce_Grow_Helpers::get_option( 'current_target_months' );
			$monthly_targets = WooCommerce_Grow_Helpers::get_option( 'monthly_targets', array() );

			if ( '' == $months ) {
				$months = WooCommerce_Grow_Helpers::get_twelve_months_ahead();
				WooCommerce_Grow_Helpers::update_option( 'current_target_months', $months );
			}

			$growth_rate      = WooCommerce_Grow_Helpers::get_option( 'growth_rate' );
			$initial_revenue  = WooCommerce_Grow_Helpers::get_option( 'initial_revenue_number' );
			$initial_orders   = WooCommerce_Grow_Helpers::get_option( 'initial_orders_number' );
			$initial_sessions = WooCommerce_Grow_Helpers::get_option( 'initial_sessions_number' );
			$initial_cr       = WooCommerce_Grow_Helpers::get_option( 'initial_cr_number' );
			$initial_aov      = WooCommerce_Grow_Helpers::get_option( 'initial_aov_number' );
			$modded            = false;
			foreach ( $months as $target_month ) {
				$month = WooCommerce_Grow_Helpers::get_field( 'month', $target_month );
				$year  = WooCommerce_Grow_Helpers::get_field( 'year', $target_month );

				$target_card = new WooCommerce_Grow_Card_Targets( $month, $year );
				$target_card->set_initials_data( $initial_revenue, $initial_orders, $initial_sessions, $initial_cr, $initial_aov );
				$target_card->set_growth_rate( $growth_rate );
				$target_card->set_targets_data();
				$target_card->display_card();
				if ( false === $modded ) {
					$modded = $target_card->is_card_modified();
				}
			}
			?>
			<div class="clear"></div>
		</div>
		<?php
			$currency_symbol  = get_woocommerce_currency_symbol();
			include( 'html-page-targets-side-panel.php' );
		?>
	</form>

</div>