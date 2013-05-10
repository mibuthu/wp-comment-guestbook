(function($) {
	$.blockUI({ 
		centerY: 0, 
		css: { top: '', bottom: '30px' } 
	}); 
	setTimeout($.unblockUI, 2000);
})(jQuery);