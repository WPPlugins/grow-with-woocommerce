<?php
/**
 * Outputs the graph wrapper html
 * 
 * @since 1.0
 * @author VanboDevelops
 */

?>
<div class="graph-container">
	<div class="wc-grow-panel wc-grow-month-card-panel-top wc-grow-month-card-panel-target">
		<div id="legend-container" class="wc-grow-float-left wc-grow-color-red"></div>
		<div id="filters-container" class="wc-grow-float-right">
			<span class="dashicons dashicons-admin-generic wc-grow-settings-toggle"></span>
			
			<div class="wc-grow-dash-settings">
				<div class="wc-grow-range">
					<select id="wc-grow-graph-range" name="wc-grow-revenue-graph-range" class="wc-enhanced-select">
						<option value="monthly"><?php _e( 'Current Month', 'woocommerce-grow' ); ?></option>
						<option value="quarterly" <?php selected( 1, 1 ); ?>><?php _e( '3 Months', 'woocommerce-grow' ); ?></option>
						<option value="yearly"><?php _e( 'Full Year', 'woocommerce-grow' ); ?></option>
					</select>
				</div>
				<div class="wc-grow-range">
					<select id="wc-grow-targets-type" name="wc-grow-targets-type" class="wc-enhanced-select">
						<?php foreach( $target_type_options as $slug => $name ) { ?>
							<option value="<?php esc_attr_e( $slug ); ?>" <?php selected( $targets_type, $slug ); ?>>
								<?php esc_html_e( $name )?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="main wc-grow-panel wc-grow-background-color-white">
		<?php $this->get_main_chart(); ?>
	</div>
</div>