(function ($) {

	$('.wc-grow-percentage').on('click', function () {
		var countdown;
		var slider = $(this).find('.wc-grow-slider');
		var sliderValue = $(this).data('percentage');

		slider.slider({
			min  : -100,
			max  : 100,
			value: sliderValue,
			slide: function (event, ui) {
				var parent = $(this).parent();
				var baseTable = $(this).closest('.wc-grow-table');
				var baseMonth = $(this).closest('.wc-grow-month-card');
				parent.find(".wc-grow-percentage-value").html(ui.value);
				parent.find("input.wc-grow-input").val(ui.value);
				parent.data('percentage', ui.value);

				// TODO: Optimize all updates to a base attribute, which is continuously watched for change.
				calculateMonthlySessions(baseTable.find('.monthly-sessions'));
				calculateMonthlyCr(baseTable.find('.monthly-cr'));
				calculateMonthlyAov(baseTable.find('.monthly-aov'));
				calculateMonthlyRevenue(baseMonth);
				calculateMonthlyOrders(baseMonth);

				$(this).closest('.wc-grow-row').find('.wc-grow-percentage-modified').show();
				parent.find('.wc-grow-input-modified').val('1');

				adjustRateColor(parent, ui.value);
			},
			stop : function (event, ui) {
				$('.wc-grow-slider-wrapper').addClass('modified');
				maybe_show_growth_slider();
			}
		});

		$(this).hover(
			function () {
				clearTimeout(countdown);
			}, function () {
				countdown = setSliderDelay(slider);
			}
		);
	});

	function setSliderDelay(el) {
		var countdown;
		countdown = setTimeout(function () {
			destroy_growth_slider(el);
		}, 5000);

		return countdown;
	}

	function calculateMonthlySessions(baseRow) {
		var baseSessions = $('.wc-grow-initial-sessions-number').val();
		var objSessions,
			objSessionsValue,
			sessionsValue,
			objSessionsTarget;
		objSessions = baseRow.find('.wc-grow-input-sessions-rate');
		objSessionsTarget = baseRow.find('.wc-grow-targets .wc-grow-targets-value');
		objSessionsValue = baseRow.find('.wc-grow-input-sessions-value');
		sessionsValue = Math.ceil(calculateGrowth(baseSessions, objSessions.val()));

		objSessionsValue.val(sessionsValue);
		objSessionsTarget.html(sessionsValue);
	}

	function calculateMonthlyCr(baseRow) {
		var baseCr = $('.wc-grow-initial-cr-number').val();
		var objCr,
			objCrValue,
			crValue,
			objCrTarget;
		objCr = baseRow.find('.wc-grow-input-cr-rate');
		objCrTarget = baseRow.find('.wc-grow-targets .wc-grow-targets-value');
		objCrValue = baseRow.find('.wc-grow-input-cr-value');
		crValue = formatDecimalToOnePoints(parseFloat(calculateGrowth(baseCr, objCr.val())));

		objCrValue.val(crValue);
		objCrTarget.html(crValue);
	}

	function calculateMonthlyAov(baseRow) {
		var baseAov = $('.wc-grow-initial-aov-number').val();
		var objAov,
			objAovValue,
			aovValue,
			objAovTarget;
		objAov = baseRow.find('.wc-grow-input-aov-rate');
		objAovTarget = baseRow.find('.wc-grow-targets .wc-grow-targets-value');
		objAovValue = baseRow.find('.wc-grow-input-aov-value');
		aovValue = formatDecimalToNoPoints(parseFloat(calculateGrowth(baseAov, objAov.val())));

		objAovValue.val(aovValue);
		objAovTarget.html(aovValue);
	}

	function calculateMonthlyRevenue(baseMonth) {
		var block = baseMonth.find('.wc-grow-revenue');
		var revenue = formatDecimalToNoPoints(
			parseFloat(
				baseMonth.find('.wc-grow-input-sessions-value').val() *
				( baseMonth.find('.wc-grow-input-cr-value').val() / 100 ) *
				baseMonth.find('.wc-grow-input-aov-value').val()
			)
		);

		var baseRevenue = $('.wc-grow-initial-revenue-number').val();
		var revenuePercent = formatDecimalToNoPoints(calculateGrowthFromBase(revenue, baseRevenue));

		var objRevPercent = block.find('.wc-grow-revenue-percentage');
		var objRevValue = block.find('.wc-grow-input-revenue');
		var objRevActualResult = block.find('.wc-grow-actual-result');
		var objRevValueBar = block.find('.wc-grow-target-bar-title span');
		var objGrowTarget = block.find('.wc-grow-target-bar-bar');

		objRevPercent.html(revenuePercent + '%');
		objRevValue.val(revenue);
		objRevValueBar.html(revenuePercent + '%');
		objRevActualResult.html(revenue);
		objGrowTarget.css({'width': ( revenuePercent > 100 ? 100 : revenuePercent ) + '%'});
	};

	function calculateMonthlyOrders(baseMonth) {
		var block = baseMonth.find('.wc-grow-orders');
		var orders = Math.ceil(
			parseFloat(
				baseMonth.find('.wc-grow-input-sessions-value').val() *
				( baseMonth.find('.wc-grow-input-cr-value').val() / 100 )
			)
		);

		var baseOrders = $('.wc-grow-initial-orders-number').val();
		var ordersPercent = formatDecimalToNoPoints(calculateGrowthFromBase(orders, baseOrders));
		var objOrdersPercent = block.find('.wc-grow-orders-percentage');
		var objOrdersValue = block.find('.wc-grow-input-orders');
		var objOrdersActualResult = block.find('.wc-grow-actual-result');
		var objOrdersValueBar = block.find('.wc-grow-target-bar-title span');
		var objGrowTarget = block.find('.wc-grow-target-bar-bar');

		objOrdersPercent.html(ordersPercent + '%');
		objOrdersValue.val(orders);
		objOrdersValueBar.html(ordersPercent + '%');
		objOrdersActualResult.html(orders);
		objGrowTarget.css({'width': ( ordersPercent > 100 ? 100 : ordersPercent ) + '%'});
	};

	function calculateRevenue(sessions, cr, aov) {
		return formatDecimalToNoPoints(Number(sessions) * ( Number(cr) / 100 ) * Number(aov));
	}

	function calculateOrders(sessions, cr) {
		return Math.ceil(Number(sessions) * ( Number(cr) / 100 ));
	}

	function convertToPercentageFloat(percentage) {
		return parseFloat(percentage / 100);
	}

	function calculateGrowth(base, percentage) {
		return Number(base) + Number(( convertToPercentageFloat(percentage) * base ));
	}

	function formatDecimalToTwoPoints(decimal) {
		return Math.round(decimal * 100) / 100;
	}
	function formatDecimalToOnePoints(decimal) {
		return Math.round(decimal * 10) / 10;
	}

	function formatDecimalToNoPoints(decimal) {
		return Math.round(decimal);
	}

	function calculateGrowthFromBase(target, base) {
		return Number(( target / base ) * 100) - Number(100);
	}

	function adjustRateColor(rate, value) {
		if (value > 0) {
			if (rate.hasClass('wc-grow-color-red')) {
				rate.removeClass('wc-grow-color-red').addClass('wc-grow-color-green');
			}
		} else {
			if (rate.hasClass('wc-grow-color-green')) {
				rate.removeClass('wc-grow-color-green').addClass('wc-grow-color-red');
			}
		}
	}

	/**
	 * Growth Slider section
	 */
		// Show slider on init, if we have to.
	maybe_show_growth_slider();

	/**
	 * Create the Growth slider, if we don't have any modifications
	 */
	function maybe_show_growth_slider() {
		if ($('.wc-grow-slider-wrapper').hasClass('modified')) {
			destroy_growth_slider($('.wc-growth-rate-slider'));
		} else {
			init_growth_slider($('.wc-growth-rate-slider'));
		}
	}

	// On Reset Click event
	$('.wc-grow-reset').on('click', function () {
		// Trigger the Slider display action
		$('.wc-grow-slider-wrapper').removeClass('modified');
		// Reset all monthly metric % to 0
		calculateAllMonths(0);
		setAllMonthsVisualMetrics(0, $('.wc-growth-rate-slider #percentage'), $('.wc-grow-percentage-value'));
		setAllMonthsInputFields(0);

		// Show slider
		maybe_show_growth_slider();

		return false;
	});

	// Update comparison month input event
	$('.wc-grow-initial-sessions-number, .wc-grow-initial-cr-number, .wc-grow-initial-aov-number').on('keyup', function () {
		// Trigger the Slider display action
		var rate = $('.wc-grow-growth-rate').val();

		// Reset all monthly metric % to 0
		setBaseRevenueAndOrders();
		calculateAllMonths(rate);
		setAllMonthsVisualMetrics(rate, $('.wc-growth-rate-slider #percentage'), $('.wc-grow-percentage-value'));
		setAllMonthsInputFields(rate);

		// Show slider
		maybe_show_growth_slider();

		return false;
	});

	function setBaseRevenueAndOrders() {
		var sessions = $('.wc-grow-initial-sessions-number').val();
		var cr = $('.wc-grow-initial-cr-number').val();
		var aov = $('.wc-grow-initial-aov-number').val();
		var revenue = calculateRevenue(sessions, cr, aov);
		var orders = calculateOrders(sessions, cr);

		$('.wc-grow-initial-revenue-number').val(revenue);
		$('#wc-grow-initial-revenue-visual').html(revenue);
		$('.wc-grow-revenue-comparison-amount').html(revenue);

		$('.wc-grow-initial-orders-number').val(orders);
		$('#wc-grow-initial-orders-visual').html(orders);
		$('.wc-grow-orders-comparison-amount').html(orders);
	}

	function init_growth_slider(element) {
		var parent = element.parent();
		var input = parent.find('.wc-grow-growth-rate');
		var sliderValue = input.val();
		var metrics_color_corrected = false;

		element.slider({
			min  : 0,
			max  : 100,
			value: sliderValue,
			slide: function (event, ui) {
				setAllMonthsVisualMetrics(ui.value, parent.find('#percentage'), $('.wc-grow-percentage-value'));

				//// TODO: Optimize all updates to a base attribute, which is continuously watched for change.
				calculateAllMonths(ui.value);
				if (false == metrics_color_corrected) {
					adjustRateColor($('.wc-grow-percentage'), ui.value);
					metrics_color_corrected = true;
				}
			},
			stop : function (event, ui) {
				setAllMonthsInputFields(ui.value);
				hide_all_modified_icons();
			}
		})
	}

	/**
	 * Sets the metric months Visual fields
	 *
	 * @param rate
	 * @param visualInput
	 * @param spanVisualPercent
	 */
	function setAllMonthsVisualMetrics(rate, visualInput, spanVisualPercent) {
		visualInput.html(rate + '%');
		spanVisualPercent.html(rate);
	}

	/**
	 * Sets the metric months input fields
	 * @param rate
	 */
	function setAllMonthsInputFields(rate) {
		var percentage = $('.wc-grow-percentage'); // Data percentage
		var sessionsRate = $('.wc-grow-input-sessions-rate'); // Sessions input %
		var crRate = $('.wc-grow-input-cr-rate'); // CR input %
		var aovRate = $('.wc-grow-input-aov-rate'); // AOV input %
		var modified = $('.wc-grow-input-modified'); // Modified *
		var input = $('.wc-grow-growth-rate');

		input.val(rate);
		percentage.data('percentage', rate);
		sessionsRate.val(rate);
		crRate.val(rate);
		aovRate.val(rate);
		modified.val('0');
	}

	/**
	 * Change all target months, all metrics when sliding the growth slider
	 *
	 * @param rate
	 */
	function calculateAllMonths(rate) {
		var initialSessions = $('.wc-grow-initial-sessions-number').val();
		var initialCr = $('.wc-grow-initial-cr-number').val();
		var initialAov = $('.wc-grow-initial-aov-number').val();
		var initialRevenue = $('.wc-grow-initial-revenue-number').val();
		var initialOrders = $('.wc-grow-initial-orders-number').val();

		var crPercent = formatDecimalToOnePoints(parseFloat(calculateGrowth(initialCr, rate)));
		var sessions = Math.ceil(calculateGrowth(initialSessions, rate));
		var aov = formatDecimalToNoPoints(parseFloat(calculateGrowth(initialAov, rate)));
		var revenue = formatDecimalToNoPoints(
			parseFloat(
				sessions * ( crPercent / 100 ) * aov
			)
		);
		var revenuePercent = formatDecimalToNoPoints(calculateGrowthFromBase(revenue, initialRevenue));
		var orders = Math.ceil(
			parseFloat(
				sessions * ( crPercent / 100 )
			)
		);
		var ordersPercent = formatDecimalToNoPoints(calculateGrowthFromBase(orders, initialOrders));

		$('.monthly-sessions .wc-grow-input-sessions-value').val(sessions);
		$('.monthly-sessions .wc-grow-targets-value').html(sessions);
		$('.monthly-cr .wc-grow-input-cr-value').val(crPercent);
		$('.monthly-cr .wc-grow-targets-value').html(crPercent);
		$('.monthly-aov .wc-grow-input-aov-value').val(aov);
		$('.monthly-aov .wc-grow-targets-value').html(aov);

		$('.wc-grow-revenue .wc-grow-input-revenue').val(revenue);
		$('.wc-grow-revenue .wc-grow-actual-result').html(revenue);
		$('.wc-grow-revenue .wc-grow-revenue-percentage').html(revenuePercent + '%');
		$('.wc-grow-revenue .wc-grow-target-bar-bar').css({'width': ( revenuePercent > 100 ? 100 : revenuePercent ) + '%'})

		$('.wc-grow-orders .wc-grow-input-orders').val(orders);
		$('.wc-grow-orders .wc-grow-actual-result').html(orders);
		$('.wc-grow-orders .wc-grow-orders-percentage').html(ordersPercent + '%');
		$('.wc-grow-orders .wc-grow-target-bar-bar').css({'width': ( ordersPercent > 100 ? 100 : ordersPercent ) + '%'})

	}

	function destroy_growth_slider(element) {
		if (element.hasClass('ui-slider')) {
			element.slider('destroy');
		}
	}

	function animateBar(barWrapper, bar) {
		bar.css({
			width: barWrapper.attr('data-percent')
		}, 3000);
	}

	function hide_all_modified_icons() {
		$('.wc-grow-percentage-modified').hide();
	}



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
