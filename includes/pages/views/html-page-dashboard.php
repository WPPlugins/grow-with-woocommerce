<?php
/**
 * Dashboard page
 *
 * @since
 * @author VanboDevelops
 */
?>


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
	
			
<div class="page-content-wrapper wc-grow-dash">
	<div class="wc-grow-cards-wrapper wc-grow-dash-top">
		<div class="wc-grow-cards-current-view">
		<?php
			$card_class = WooCommerce_Grow_Helpers::get_card( $current_month, $current_year, $this->get_targets_type() );
			$card_class->set_targets_data();
			$card_class->set_card_data();
			$card_class->display_card();
			$report = new WooCommerce_Grow_Report_Revenue();
			$report->output_graph();
		?>
		</div>
		<div class="clear"></div>
			
	</div>
	<div class="wc-grow-side">

		<div class="wc-grow-panel wc-grow-panel-no-padding wc-grow-guides">
			<img src="<?php echo WooCommerce_Grow::get_plugin_url(); ?>/assets/img/side_guides.jpg"/>
		<ul>
			<li><strong>New in Version 1.2</strong></li>
			<li>&nbsp;</li>
			<p>We will soon be discontinuing this plugin as we move to our brand new and improved analytics version hosted on YoGrow.co</p>
			<p><a href="https://yogrow.co">Get your FREE TRIAL with YoGrow Now</a></p>
		</ul>
		

		</div>

	</div>
</div>


<div class="page-content-wrapper wc-grow-dash">
	<div class="wc-grow-cards-wrapper wc-grow-cards-wrapper-history ">
<div id="wc-grow-monthly-history-container">
			<h2 class="wc-grow-sub-title"><?php _e( 'Growth History', 'woocommerce-grow' ); ?></h2>
			<?php if ( 3 < count( $passed_months ) ) { ?>
				<button class="button wc-grow-view-all-history" id="wc-grow-view-all-history">
					<?php echo __( 'View All History', 'woocommerce-grow' ); ?>
				</button>
			<?php } ?>
			<?php if ( 0 == count( $passed_months ) ) { ?>
				<p><?php echo esc_html( __( 'There is no history logged, yet. You are on your first targeted month.', 'woocommerce-grow' ) ); ?></p>
			<?php } else { ?>
				<div class="clear"></div>
				<?php
				foreach( $passed_months_display as $passed ) {
					$card = WooCommerce_Grow_Helpers::get_card( $passed['month'], $passed['year'], $this->get_targets_type() );
					$card->set_targets_data();
					$card->set_card_data();
					$card->display_card();
				}
				 ?>
			<?php } ?>
		</div>

	</div>
</div>