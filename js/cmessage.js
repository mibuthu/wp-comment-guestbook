(function($) {
	// show the message after a comment was made
	var cmessage = $('<div id="cmessage" style="'+window.cmessage_styles+'; display:none"><strong>'+window.cmessage_text+'</strong></div>');
	if ( window.cmessage_type == 'overlay' ) {
		cmessage.appendTo('body')
			.css('position','fixed')
			.css('z-index','2000')
			.css('bottom','20%')
			.css('left', '50%')
			.css('margin','0 0 0 -'+cmessage.width()/2+'px');
	}
	else { // inline
		var hash = window.location.hash;
		cmessage.appendTo(hash)
			.css('margin', '6px');
	}
	cmessage
		.delay(500)
		.slideDown()
		.delay(window.cmessage_duration)
		.slideUp();
})(jQuery);
