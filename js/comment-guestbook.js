(function($) {
	$.blockUI({
		message: window.cmessage_text,
		centerY: 0,
		css: { top: '', bottom: '30px' }
	}); 
	setTimeout($.unblockUI, 2000);
	//var hash = window.location.hash;
})(jQuery);