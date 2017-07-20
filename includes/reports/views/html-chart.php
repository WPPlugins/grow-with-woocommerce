<?php
/**
 * Displays the Graph JS script and the graph container HTML.
 * 
 * @since 1.0
 * @author VanboDevelops
 */
?>
<div class="chart-container">
	<div class="chart-placeholder main"></div>
</div>
<script type="text/javascript">
	window.wp = window.wp || {};
	wp.wcGrow = {};

	jQuery(function () {
		wp.wcGrow.order_data = jQuery.parseJSON('<?php echo $chart_data; ?>');
		wp.wcGrow.chartOptions = {
			legend: {
				show  : true,
				margin: [20, 30],
				position: 'nw'
			},
			grid  : {
				color      : '#aaa',
				borderColor: 'transparent',
				borderWidth: 0,
				hoverable  : true
			},
			xaxes : [{
				color      : '#aaa',
				position   : "bottom",
				tickColor  : 'transparent',
				mode       : "time",
				timeformat : "<?php if ( $this->chart_groupby == 'day' ) {echo '%d %b';} else {echo '%b';} ?>",
				monthNames : <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
				tickLength : 1,
				minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
				font       : {
					color: "#aaa"
				}
			}],
			yaxes : [
				{
					min         : 0,
					minTickSize : 1,
					tickDecimals: 0,
					color       : '#d4d9dc',
					font        : {color: "#aaa"}
				},
				{
					position          : "left",
					min               : 0,
					tickDecimals      : 2,
					alignTicksWithAxis: 1,
					color             : 'transparent',
					font              : {color: "#aaa"}
				}
			],
		};

		wp.wcGrow.chartSeries = [
			{
				label     : "<?php echo esc_js( __( 'Revenue', 'woocommerce-grow' ) ) ?>",
				data      : wp.wcGrow.order_data.revenue_amounts,
				yaxis     : 5,
				color     : '<?php echo $this->chart_colours['revenue_amount']; ?>',
				points    : {
					show     : true,
					radius   : 2,
					lineWidth: 3,
					fillColor: '#fff',
					fill     : true
				},
				lines     : {show: true, lineWidth: 2, fill: false},
				shadowSize: 0,
				<?php echo $this->get_currency_tooltip(); ?>
			},
			{
				label     : "<?php echo esc_js( __( 'Target Revenue', 'woocommerce-grow' ) ) ?>",
				data      : wp.wcGrow.order_data.target_revenue_amounts,
				yaxis     : 5,
				color     : '<?php echo $this->chart_colours['target_revenue_amount']; ?>',
				points    : {
					show     : true,
					radius   : 2,
					lineWidth: 3,
					fillColor: '#fff',
					fill     : true
				},
				lines     : {show: true, lineWidth: 2, fill: false},
				shadowSize: 0,
				<?php echo $this->get_currency_tooltip(); ?>
			},
		];

		wp.wcGrow.drawGraph = function (highlight) {
			wp.wcGrow.main_chart = jQuery.plot(
				jQuery('.chart-placeholder.main'),
				wp.wcGrow.chartSeries,
				wp.wcGrow.chartOptions
			);

			wp.wcGrow.main_chart.resize();
		}

		wp.wcGrow.drawGraph();
	});
</script>