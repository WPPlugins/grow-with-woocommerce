<?php
/**
 * Targets page, monthly card html
 *
 * @since
 * @author VanboDevelops
 */
?>
<div class="wc-grow-month-card">
	<!--	Month Name-->
	<div class="wc-grow-panel wc-grow-month-card-panel-top wc-grow-background-color-blue">
		<div class="wc-grow-float-right">
			<span class="wc-grow-month-name"><?php echo esc_html( $this->month_in_text . ', ' . $this->year ); ?></span>
			<span class="dashicons dashicons-calendar-alt"></span>
		</div>
	</div>

	<!--	Revenue panel-->
	<div class="wc-grow-panel wc-grow-month-card-panel-bottom">
		<div class="wc-grow-table wc-grow-revenue">
			<div style="" class="wc-grow-row">
				<div style="" class="wc-grow-cell-left">
					<p class="wc-grow-title"><?php echo $this->string_revenue_title(); ?></p>

					<p class="wc-grow-revenue-percentage"><?php echo esc_attr( $this->revenue_target_percentage ); ?>%</p>
					<input
						class="wc-grow-input-revenue wc-grow-input"
						type="hidden"
						name="revenue_target[<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
						value="<?php echo esc_attr( $this->revenue_target ); ?>" />
				</div>
				<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
					<div class="wc-grow-target-bar clearfix wc-grow-background-color-purple" data-percent="<?php echo esc_attr( $this->revenue_target_percentage ); ?>%">
						<div class="wc-grow-target-bar-title"></div>
						<div class="wc-grow-target-bar-bar"></div>
					</div>
					<p class="wc-grow-target-revenue-result">
						<span class="wc-grow-color-purple"><?php echo esc_html( $this->currency_symbol ); ?></span>
						<span class="wc-grow-actual-result wc-grow-color-purple tips" data-tip="<?php echo esc_attr( $this->string_target_metric() ); ?>">
							<?php echo esc_html( $this->revenue_target ); ?>
						</span>
						/ <?php echo $this->currency_symbol; ?><span class="wc-grow-revenue-comparison-amount tips" data-tip="<?php echo esc_attr( $this->string_comparison_metric() ); ?>"><?php echo esc_html( $this->initial_revenue ); ?></span>
					</p>
				</div>
			</div>
		</div>
		<!--	Orders panel-->
		<div class="wc-grow-table wc-grow-orders">
			<div style="" class="wc-grow-row">
				<div style="" class="wc-grow-cell-left">
					<p class="wc-grow-title"><?php echo $this->string_orders_title(); ?></p>

					<p class="wc-grow-orders-percentage"><?php echo esc_attr( $this->orders_target_percentage ); ?>%</p>
					<input
						class="wc-grow-input-orders wc-grow-input"
						type="hidden"
						name="orders_target[<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
						value="<?php echo esc_attr( $this->orders_target ); ?>" />
				</div>
				<div style="" class="wc-grow-cell-right wc-grow-cell-span-2">
					<div class="wc-grow-target-bar clearfix wc-grow-background-color-purple" data-percent="<?php echo esc_attr( $this->orders_target_percentage ); ?>%">
						<div class="wc-grow-target-bar-title"></div>
						<div class="wc-grow-target-bar-bar"></div>
					</div>
					<p class="wc-grow-target-orders-result">
						<span class="wc-grow-actual-result wc-grow-color-purple tips" data-tip="<?php echo esc_attr( $this->string_target_metric() ); ?>">
							<?php echo esc_html( $this->orders_target ); ?>
						</span>
						/ <span class="wc-grow-orders-comparison-amount tips" data-tip="<?php echo esc_attr( $this->string_comparison_metric() ); ?>"><?php echo esc_html( $this->initial_orders ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<!--	Calculations panel-->
	<div class="wc-grow-panel wc-grow-month-card-panel-bottom">
		<div class="wc-grow-table wc-grow-details">
			<div style="" class="wc-grow-row monthly-sessions">
				<div class="wc-grow-cell-left" style="">
					<p class="wc-grow-title">
						<?php echo $this->string_sessions_title(); ?>
						<span class="wc-grow-percentage-modified" style="display: <?php echo esc_attr( $this->sessions_display_modified ) ?>;">*</span>
					</p>

				</div>
				<div class="wc-grow-cell-middle" style="">
					<p class="wc-grow-percentage <?php echo esc_attr( $this->sessions_rate_color ); ?>" data-percentage="<?php echo esc_attr( $this->sessions_growth_target_rate ); ?>">
						<span class="wc-grow-percentage-value"><?php echo esc_attr( $this->sessions_growth_target_rate ); ?></span>%
						<input
							class="wc-grow-input-sessions-rate wc-grow-input"
							type="hidden"
							name="sessions_target[growth_rate][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
							value="<?php echo esc_attr( $this->sessions_growth_target_rate ); ?>" />
						<input type="hidden"
						       class="wc-grow-input-modified wc-grow-input"
						       name="sessions_target[growth_rate][modified][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
						       value="<?php echo esc_attr( $this->sessions_modified ); ?>" />
						<span class="wc-grow-slider"></span>
					</p>
				</div>
				<div style="" class="wc-grow-cell-right">
					<p class="wc-grow-targets">
						<span class="wc-grow-targets-value"><?php echo esc_html( $this->sessions_target ); ?></span>
						<input
							class="wc-grow-input-sessions-value wc-grow-input"
							type="hidden"
							name="sessions_target[target][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
							value="<?php echo esc_attr( $this->sessions_target ); ?>" />
					</p>
				</div>
			</div>
			<div style="" class="wc-grow-row monthly-cr">
				<div class="wc-grow-cell-left" style="">
					<p class="wc-grow-title">
						<?php echo $this->string_cr_title(); ?>
						<span class="wc-grow-percentage-modified" style="display: <?php echo esc_attr( $this->cr_display_modified ) ?>;">*</span>
					</p>

				</div>
				<div class="wc-grow-cell-middle">
					<p class="wc-grow-percentage <?php echo esc_attr( $this->cr_rate_color ); ?>" data-percentage="<?php echo esc_attr( $this->cr_growth_target_rate ); ?>">
						<span class="wc-grow-percentage-value"><?php echo esc_attr( $this->cr_growth_target_rate ); ?></span>%
						<input
							class="wc-grow-input-cr-rate wc-grow-input"
							type="hidden"
							name="cr_target[growth_rate][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
							value="<?php echo esc_attr( $this->cr_growth_target_rate ); ?>" />
						<input type="hidden"
						       class="wc-grow-input-modified wc-grow-input"
						       name="cr_target[growth_rate][modified][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
						       value="<?php echo esc_attr( $this->cr_modified ); ?>" />
						<span class="wc-grow-slider"></span>
					</p>
				</div>
				<div style="" class="wc-grow-cell-right">
					<p class="wc-grow-targets">
						<span class="wc-grow-targets-value"><?php echo esc_html( $this->cr_target ); ?></span>%
						<input
							class="wc-grow-input-cr-value wc-grow-input"
							type="hidden"
							name="cr_target[target][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
							value="<?php echo esc_attr( $this->cr_target ); ?>" />
					</p>
				</div>
			</div>
			<div style="" class="wc-grow-row monthly-aov">
				<div class="wc-grow-cell-left" style="">
					<p class="wc-grow-title">
						<?php echo $this->string_aov_title(); ?>
						<span class="wc-grow-percentage-modified" style="display: <?php echo esc_attr( $this->aov_display_modified ) ?>;">*</span>
					</p>
				</div>
				<div class="wc-grow-cell-middle">
					<p class="wc-grow-percentage <?php echo esc_attr( $this->aov_rate_color ); ?>" data-percentage="<?php echo esc_attr( $this->aov_growth_target_rate ); ?>">
						<span class="wc-grow-percentage-value"><?php echo esc_attr( $this->aov_growth_target_rate ); ?></span>%
						<input
							class="wc-grow-input-aov-rate wc-grow-input"
							type="hidden"
							name="aov_target[growth_rate][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
							value="<?php echo esc_attr( $this->aov_growth_target_rate ); ?>" />
						<input type="hidden"
						       class="wc-grow-input-modified wc-grow-input"
						       name="aov_target[growth_rate][modified][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
						       value="<?php echo esc_attr( $this->aov_modified ); ?>" />
						<span class="wc-grow-slider"></span>
					</p>
				</div>
				<div style="" class="wc-grow-cell-right">
					<p class="wc-grow-targets">
						<?php echo $this->currency_symbol; ?>
						<span class="wc-grow-targets-value"><?php echo esc_html( $this->aov_target ); ?></span>
						<input
							class="wc-grow-input-aov-value wc-grow-input"
							type="hidden"
							name="aov_target[target][<?php echo esc_attr( $this->year ) ?>][<?php echo esc_attr( $this->month ) ?>]"
							value="<?php echo esc_attr( $this->aov_target ); ?>" />
					</p>
				</div>
			</div>
		</div>
	</div>
</div>