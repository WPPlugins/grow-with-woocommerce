<?php
/**
 * Single Month Card generic template
 *
 * @since 1.0
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
		<div class="wc-grow-table wc-grow-revenue">
			<div style="" class="wc-grow-row">
				<div style="" class="wc-grow-cell-left">
					<p class="wc-grow-title"><?php echo $this->string_revenue_title(); ?></p>

					<p class="wc-grow-revenue-percentage"><?php echo $this->revenue_percentage ?>
						%</p>
				</div>
				<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
					<div class="wc-grow-target-bar clearfix wc-grow-background-color-purple" data-percent="<?php echo $this->revenue_percentage_bar ?>%">
						<div class="wc-grow-target-bar-title"></div>
						<div class="wc-grow-target-bar-bar"></div>
					</div>
					<p class="wc-grow-target-revenue-result">
						<span class="wc-grow-actual-result wc-grow-color-purple tips" data-tip="<?php echo esc_attr( $this->string_actual_metric ); ?>">
							<?php echo esc_html( $this->currency_symbol . $this->revenue ); ?>
						</span>
						/ <span class="wc-grow-revenue-target-amount tips" data-tip="<?php echo esc_attr( $this->string_target_metric ); ?>"><?php echo esc_html( $this->currency_symbol . $this->target_revenue ); ?></span>
					</p>
				</div>
			</div>
		</div>
		<div class="wc-grow-table wc-grow-orders">
			<div style="" class="wc-grow-row">
				<div style="" class="wc-grow-cell-left">
					<p class="wc-grow-title"><?php echo $this->string_orders_title(); ?></p>

					<p class="wc-grow-revenue-percentage"><?php echo $this->orders_percentage ?>
						%</p>
				</div>
				<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
					<div class="wc-grow-target-bar clearfix wc-grow-background-color-purple" data-percent="<?php echo $this->orders_percentage_bar ?>%">
						<div class="wc-grow-target-bar-title"></div>
						<div class="wc-grow-target-bar-bar"></div>
					</div>
					<p class="wc-grow-target-revenue-result">
						<span class="wc-grow-actual-result wc-grow-color-purple tips" data-tip="<?php echo esc_attr( $this->string_actual_metric ); ?>">
							<?php echo esc_html( $this->orders ); ?>
						</span>
						/ <span class="wc-grow-orders-target-amount tips" data-tip="<?php echo esc_attr( $this->string_target_metric ); ?>"><?php echo esc_html( $this->target_orders ); ?></span>
					</p>
				</div>
			</div>
		</div>
	</div>
	<div class="wc-grow-panel wc-grow-panel wc-grow-month-card-panel-bottom">
		<div class="wc-grow-table wc-grow-details">
			<div style="" class="wc-grow-row">
				<div class="wc-grow-cell-left" style="">
					<p class="wc-grow-title"><?php echo $this->string_sessions_title(); ?></p>
				</div>
				<div class="wc-grow-cell-middle" style="">
					<p class="wc-grow-percentage <?php echo esc_attr( $this->sessions_growth_color ); ?>">
						<?php echo esc_html( $this->sessions_percentage ); ?>%
					</p>
				</div>
				<div style="" class="wc-grow-cell-right">
					<p class="wc-grow-targets">
						<span class="wc-grow-color-purple tips" data-tip="<?php echo esc_attr( $this->string_actual_metric ); ?>"><?php echo esc_html( $this->sessions ); ?></span>
						/ <span class="wc-grow-sessions-target-amount tips" data-tip="<?php echo esc_attr( $this->string_target_metric ); ?>"><?php echo esc_html( $this->target_sessions ); ?></span>
					</p>
				</div>
			</div>
			<div style="" class="wc-grow-row">
				<div class="wc-grow-cell-left" style="">
					<p class="wc-grow-title"><?php echo $this->string_cr_title(); ?></p>

				</div>
				<div class="wc-grow-cell-middle" style="">
					<p class="wc-grow-percentage <?php echo esc_attr( $this->cr_growth_color ); ?>">
						<?php echo $this->cr_percentage ?>%
					</p>
				</div>
				<div style="" class="wc-grow-cell-right">
					<p class="wc-grow-targets">
						<span class="wc-grow-color-purple tips" data-tip="<?php echo esc_attr( $this->string_actual_metric ); ?>"><?php echo esc_html( $this->cr ); ?>%</span>
						/ <span class="wc-grow-cr-target-amount tips" data-tip="<?php echo esc_attr( $this->string_target_metric ); ?>"><?php echo esc_html( $this->target_cr ); ?>%</span>
					</p>
				</div>
			</div>
			<div style="" class="wc-grow-row">
				<div class="wc-grow-cell-left" style="">
					<p class="wc-grow-title"><?php echo $this->string_aov_title(); ?></p>

				</div>
				<div class="wc-grow-cell-middle" style="">
					<p class="wc-grow-percentage <?php echo esc_attr( $this->aov_growth_color ); ?>">
						<?php echo $this->aov_percentage ?>%
					</p>
				</div>
				<div style="" class="wc-grow-cell-right">
					<p class="wc-grow-targets">
						<span class="wc-grow-color-purple tips" data-tip="<?php echo esc_attr( $this->string_actual_metric ); ?>"><?php echo $this->currency_symbol . $this->aov ?></span>
						/ <span class="wc-grow-aov-target-amount tips" data-tip="<?php echo esc_attr( $this->string_target_metric ); ?>"><?php echo $this->currency_symbol . $this->target_aov; ?></span>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>