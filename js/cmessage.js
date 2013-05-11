(function($) {
	// show the message after a comment was made
	var message_style = 'background-color:rgb(255, 255, 224); border-color:rgb(230, 219, 85); color:rgb(51, 51, 51); padding:6px 20px; '
	                  + 'text-align:center; border-radius:5px; border-width:1px; border-style:solid';
	var cmessage = $('<div id="cmessage" style="'+message_style+'; display:none"><strong>'+window.cmessage_text+'</strong></div>');
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
		.delay(3000)
		.slideUp();
})(jQuery);
