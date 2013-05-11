(function($) {
	// show the message after a comment was made
	if ( window.cmessage_type == 'overlay' ) {
		$.blockUI({
			message: '<strong>'+window.cmessage_text+'</strong>',
			centerY: 0,
			css: { top: '', bottom: '30px' }
		}); 
		setTimeout($.unblockUI, 2000);
	}
	else { // inline
		var hash = window.location.hash;
		$(hash).append('<div class="cmessage" style="color:rgb(120,220,20)"><strong>'+window.cmessage_text+'</strong></div>');
	}
})(jQuery);
