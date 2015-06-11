jQuery(function($) {
	$('.content-grid-nav-tab').click(function(event) { // admin nav tabs
		event.preventDefault();
		$(this).addClass('nav-tab-active').siblings().removeClass('nav-tab-active');
		$('.content-grid-group').hide();
		
		var clicked_index = $(this).index();
		clicked_index = clicked_index + 1;
		$( '.content-grid-group:nth-child('+clicked_index+')' ).show();
	});

	$('.content-grid-nav-tab:first-child').click(); // activate first nav tab

	$('.cg-admin-wrap .cg-row-control').change(function() {
		var cols_count;
		cols_count = $(this).val();
		if(cols_count == 0) {
			$(this).closest('.cg-admin-row').find('.js-cg-admin-cols-wrap').addClass('cg-hide');
		} else {
			$(this).closest('.cg-admin-row').find('.js-cg-admin-cols-wrap').removeClass('cg-hide');
			if(cols_count == 1) {
				$(this).closest('.cg-admin-row').find('.js-cg-admin-cols-wrap').find('.js-cg-admin-col:first-child')
					.removeClass('cg-hide cg-col-6').addClass('cg-col-12');
				$(this).closest('.cg-admin-row').find('.js-cg-admin-cols-wrap').find('.js-cg-admin-col:last-child')
					.addClass('cg-hide');
			} else { // 2 cols
				$(this).closest('.cg-admin-row').find('.js-cg-admin-cols-wrap').find('.js-cg-admin-col:first-child')
					.removeClass('cg-hide cg-col-12').addClass('cg-col-6');
				$(this).closest('.cg-admin-row').find('.js-cg-admin-cols-wrap').find('.js-cg-admin-col:last-child')
					.removeClass('cg-hide cg-col-12').addClass('cg-col-6');
			}
		}
	});
});