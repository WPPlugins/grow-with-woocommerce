<?php
/**
 * Growth Rate Slider Panel
 *
 * @since
 * @author VanboDevelops
 */
?>

<div class="wc-grow-panel wc-grow-background-color-white initial-metrics-sidebar">
	<h3>
		<?php echo __( '3. Growth Calculator', 'woocommerce-grow' ) ?>
		<a class="wc-grow-tooltip" href="#" style="float: right;">
			<span
				class="dashicons dashicons-info tips"
				data-tip="<?php _e(
					'Use the growth slider to change the %
				growth targets for ALL METRICS and ALL MONTHS. You can change the % growth
				 for each metric in each month too.', 'woocommerce-grow'
				); ?>">

			</span>
		</a>
	</h3>

	<div class="wc-grow-slider-wrapper <?php echo true == $modded ? 'modified' : ''; ?>">
		<p class="wc-grow-reset-message">
			<?php echo __( 'You have custom growth targets set.', 'woocommerce-grow' ) ?><br /><br />
			<a href="#" class="wc-grow-reset"><?php echo __( 'Click Here to reset all.', 'woocommerce-grow' ) ?></a>
		</p>
		<input
			type="hidden"
			name="growth_rate"
			class="wc-grow-growth-rate"
			value="<?php echo esc_attr( $growth_rate ) ?>" />
		<span class="wc-growth-rate-slider">
			<span id="percentage"><?php echo esc_attr( $growth_rate ) ?>%</span>
		</span>
	</div>
</div>