<?php
/**
 * Display the empty "Targets" type card.
 *
 * @since 1.1
 * @author VanboDevelops
 */

?>
<div class="wc-grow-month-card">
	<div class="wc-grow-panel wc-grow-month-card-panel-top wc-grow-month-card-panel-target wc-grow-background-color-blue">
		<div class="wc-grow-float-left <?php echo esc_attr( $this->track_color ); ?>">
			<span class="dashicons <?php echo esc_attr( $this->track_icon ); ?>"></span>
			<span class="wc-grow-followed-target"><?php echo esc_html( $this->track_message ); ?></span>
		</div>
		<div class="wc-grow-float-right">
			<span class="wc-grow-month-name"><?php echo esc_html( $this->month_name ); ?></span>
			<span class="dashicons dashicons-calendar-alt"></span>
		</div>
	</div>
	<div class="wc-grow-panel wc-grow-panel wc-grow-month-card-panel-bottom">
		<img id="growl-targets-error" src="<?php echo esc_attr( WooCommerce_Grow::get_plugin_url() . '/assets/img/growl_med.png' ); ?>"/>
		<?php echo sprintf(
			__(
				'Sorry we are unable to display your Targets data as you have not set any targets, yet.
				Please %sclick here%s to setup your eCommerce Grow Targets for the year.', 'woocommerce-grow'
			),
			'<a href="' . admin_url( 'admin.php?page=woocommerce-grow&tab=targets' ) . '">',
			'</a>'
		) ?>
	</div>
</div>
