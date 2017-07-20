(function ($) {
	// Start the animated bars
	$('.wc-grow-target-bar').each(function () {
		animateBar($(this), $(this).find('.wc-grow-target-bar-bar'));
	});

	function animateBar(barWrapper, bar) {
		bar.animate({
			width: barWrapper.attr('data-percent')
		}, 3000);
	}
})(jQuery);

