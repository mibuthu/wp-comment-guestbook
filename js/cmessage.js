(function($) {
	// show the message after a comment was made
	var message_style = 'background-color:rgb(255, 255, 224); border-color:rgb(230, 219, 85); color:rgb(51, 51, 51); padding:3px; margin:10px;'
	                  + 'text-align:center; border-radius:3px 3px 3px 3px; border-width:1px; border-style:solid';
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
		$('<div id="cmessage" style="'+message_style+'; display:none"><strong>'+window.cmessage_text+'</strong></div>')
			.appendTo(hash)
			.delay(500)
			.slideDown()
			.delay(3000)
			.slideUp();
	}
})(jQuery);
