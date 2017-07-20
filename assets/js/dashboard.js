(function ($) {

	$('#wc-grow-view-all-history').on('click', function () {

		var container = $('#wc-grow-monthly-history-container');

		container.block({
			message   : null,
			overlayCSS: {
				background: '#fff',
				opacity   : 0.6
			}
		});

		var data = {
			action  : 'get_remaining_history_months',
			_wpnonce: wcGrow.security,
		};

		$.ajax({
			url    : wcGrow.ajaxUrl,
			data   : data,
			type   : 'GET',
			success: function (response) {
				response = JSON.parse(response);
				if (isVarSet(response.error)) {
					if ($('.wc-grow-history-error').length > 0) {
						$('.wc-grow-history-error').remove();
					}

					var appendAfter = $('#wc-grow-view-all-history');
					appendAfter.after('<div class="error wc-grow-history-error"><p>' + response.error + '</p></div>');
					setTimeout(function () {
						$('.wc-grow-history-error').remove();
					}, 3000);
				} else {
					var appendTo = container.find('.wc-grow-month-card').last();
					appendTo.after(response.months);
					$('#wc-grow-view-all-history').hide();

					container.find('.wc-grow-target-bar').each(function () {
						animateBar($(this), $(this).find('.wc-grow-target-bar-bar'));
					})
				}

				container.unblock();
			}
		});
		return false;
	});

	$('#wc-grow-targets-type').on('change', function () {
		var container = $('.wc-grow-cards-wrapper');
		var targetsType = $(this).val();

		container.block({
			message   : null,
			overlayCSS: {
				background: '#fff',
				opacity   : 0.6
			}
		});

		var data = {
			action     : 'change_targets_type',
			_wpnonce   : wcGrow.security,
			targetsType: targetsType,
		};

		$.ajax({
			url    : wcGrow.ajaxUrl,
			data   : data,
			type   : 'GET',
			success: function (response) {
				response = JSON.parse(response);
				if (isVarSet(response.error)) {
					alert( 'Could not set the Targets Type: Error: ' + response.error );
					container.unblock();
				} else if (isVarSet(response.result)) {
					location.reload();
				}
			}
		});
	});

	$('#wc-grow-graph-range').on('change', function () {

		var container = $('.graph-container');
		var range = $(this).val();

		container.block({
			message   : null,
			overlayCSS: {
				background: '#fff',
				opacity   : 0.6
			}
		});

		var data = {
			action           : 'get_dashboard_graph_data',
			_wpnonce         : wcGrow.security,
			revenueGraphRange: range,
		};

		$.ajax({
			url    : wcGrow.ajaxUrl,
			data   : data,
			type   : 'GET',
			success: function (response) {
				response = JSON.parse(response);
				if (isVarSet(response.error)) {
					// TODO: Decide on the appropriate action
				} else if (isVarSet(response.revenue_amounts) && isVarSet(response.target_revenue_amounts)) {
					wp.wcGrow.order_data.target_revenue_amounts = response.target_revenue_amounts;
					wp.wcGrow.order_data.revenue_amounts = response.revenue_amounts;

					wp.wcGrow.chartSeries[0].data = wp.wcGrow.order_data.revenue_amounts;
					wp.wcGrow.chartSeries[1].data = wp.wcGrow.order_data.target_revenue_amounts;

					wp.wcGrow.main_chart.setData(wp.wcGrow.chartSeries);
					wp.wcGrow.main_chart.resize();
					wp.wcGrow.main_chart.setupGrid();
					wp.wcGrow.main_chart.draw();
				}

				container.unblock();
			}
		});
		return false;
	});

	function isVarSet(variable) {
		if (typeof(variable) != "undefined" && variable !== null) {
			return true;
		}

		return false;
	}

	function animateBar(barWrapper, bar) {
		bar.animate({
			width: barWrapper.attr('data-percent')
		}, 3000);
	}



$( ".wc-grow-settings-toggle" ).click(function() {     
   $('.wc-grow-dash-settings').toggle();
   $('.wc-grow-settings-toggle').toggleClass("wc-grow-settings-toggle-color")
   
});


	/** Overlay Section **/
	if( '1' == wcGrow.firstLoad ) {
		add_overlay($('.wc-grow-help-overlay-wrapper'));
	}

/*
	$('.wc-grow-need-help').on('click', function () {
		add_overlay($('.wc-grow-help-overlay-wrapper'));
		return false;
	});

	$('.wc-grow-help-overlay-wrapper').on('click', function () {
		remove_overlay($('.wc-grow-help-overlay-wrapper'));
		return false;
	});
*/

	$('.wc-grow-help-growl img').on('click', function () {
		remove_overlay($('.wc-grow-help-overlay-wrapper'));
		return false;
	});

	function add_overlay(element) {
		element.show();
	}

	function remove_overlay(element) {
		element.hide();
	}

})(jQuery);
