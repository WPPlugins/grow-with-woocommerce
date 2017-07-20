<?php
/**
 * PhpStorm
 *
 * @since
 * @author VanboDevelops
 */

?>
<div class="wc-grow-panel wc-grow-background-color-blue">
	<div class="wc-grow-float-left">
		<span class="wc-grow-month-name"><?php echo __( 'Comparison Month', 'woocommerce-grow' ) ?></span>
	</div>
	<div class="wc-grow-float-right">
		<span class="dashicons dashicons-calendar-alt"></span>
	</div>
</div>
<div class="wc-grow-panel wc-grow-background-color-white initial-metrics-sidebar">
	<h3><?php echo __( '1. Set Key Metrics', 'woocommerce-grow' ) ?></h3>
	<div class="wc-grow-table wc-grow-details">
		<div style="" class="wc-grow-row">
			<div class="wc-grow-cell-left" style="">
				<p class="wc-grow-title"><?php echo __( 'Sessions', 'woocommerce-grow' ) ?></p>
			</div>
			<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
				<p class="wc-grow-targets wc-grow-targets-initial-content">
					<input
						type="text"
						class="wc-grow-initial-sessions-number wc-grow-clean-input-field"
						name="initial_sessions_number"
						value="<?php echo esc_attr( $initial_sessions ); ?>" />
				</p>
			</div>
		</div>
		<div style="" class="wc-grow-row">
			<div class="wc-grow-cell-left" style="">
				<p class="wc-grow-title"><?php echo __( 'CR', 'woocommerce-grow' ) ?></p>
			</div>
			<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
				<p class="wc-grow-targets wc-grow-targets-initial-content">
					<span class="outside-input-symbol">%</span>
					<input
						type="text"
						class="wc-grow-initial-cr-number wc-grow-clean-input-field"
						name="initial_cr_number"
						value="<?php echo esc_attr( $initial_cr ); ?>" />
				</p>
			</div>
		</div>
		<div style="" class="wc-grow-row">
			<div class="wc-grow-cell-left" style="">
				<p class="wc-grow-title"><?php echo __( 'AOV', 'woocommerce-grow' ) ?></p>
			</div>
			<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
				<p class="wc-grow-targets wc-grow-targets-initial-content">
					<span class="outside-input-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
					<input
						type="text"
						class="wc-grow-initial-aov-number wc-grow-clean-input-field"
						name="initial_aov_number"
						value="<?php echo esc_attr( $initial_aov ); ?>" />
				</p>
			</div>
		</div>
	</div>
</div>
<div class="wc-grow-panel wc-grow-background-color-white initial-metrics-sidebar">
	<h3><?php echo __( '2. Calculated Results', 'woocommerce-grow' ) ?></h3>
	<div class="wc-grow-table wc-grow-details">
		<div style="" class="wc-grow-row">
			<div class="wc-grow-cell-left" style="">
				<p class="wc-grow-title"><?php echo __( 'Revenue', 'woocommerce-grow' ) ?></p>
			</div>
			<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
				<p class="wc-grow-targets wc-grow-targets-initial-content">
					<span class=""><?php echo esc_html( $currency_symbol ); ?></span>
					<input
						type="hidden"
						class="wc-grow-initial-revenue-number wc-grow-clean-input-field"
						name="initial_revenue_number"
						value="<?php echo esc_attr( $initial_revenue ); ?>" />
					<span id="wc-grow-initial-revenue-visual" class="wc-grow-initial-revenue-visual"><?php echo esc_html( $initial_revenue ); ?></span>
					<a class="wc-grow-tooltip" href="#">
						<span
							class="dashicons dashicons-info tips"
							data-tip="<?php _e( 'Revenue is calculated by multiplying the
							following metrics together: Sessions x CR x AOV', 'woocommerce-grow' ); ?>">

						</span>
					</a>
				</p>
			</div>
		</div>
		<div style="" class="wc-grow-row">
			<div class="wc-grow-cell-left" style="">
				<p class="wc-grow-title"><?php echo __( 'Orders', 'woocommerce-grow' ) ?></p>
			</div>
			<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
				<p class="wc-grow-targets wc-grow-targets-initial-content">
					<input
						type="hidden"
						class="wc-grow-initial-orders-number wc-grow-clean-input-field"
						name="initial_orders_number"
						value="<?php echo esc_attr( $initial_orders ); ?>" />
					<span id="wc-grow-initial-orders-visual" class="wc-grow-initial-orders-visual"><?php echo esc_html( $initial_orders ); ?></span>
					<a class="wc-grow-tooltip" href="#">
						<span
							class="dashicons dashicons-info tips"
							data-tip="<?php _e( 'Orders are calculated by multiplying the
							 following metrics together: Sessions x CR', 'woocommerce-grow' ); ?>">
						</span>
					</a>
				</p>
			</div>
		</div>
	</div>
</div>