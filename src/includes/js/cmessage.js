jQuery(document).ready(function($) {
	// remove the cmessage indicator from all urls in the commentlist
	$('#comments a').each(function() {
		$(this).attr('href', remove_url_parameter($(this).attr('href'), 'cmessage'));
	});

	// prepare the message after a new comment
	var cmessage = $('<div id="cmessage" style="'+window.cmessage_styles+'; display:none"><strong>'+window.cmessage_text+'</strong></div>');
	if(window.cmessage_type == 'overlay') {
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

	// show message after comment
	cmessage
		.delay(500)
		.slideDown()
		.delay(window.cmessage_duration)
		.slideUp();

	// function to remove an url parameter from an url
	function remove_url_parameter(url, parameter) {
		var fragment = url.split('#');
		var urlparts= fragment[0].split('?');
		if(urlparts.length>=2) {
			var urlBase=urlparts.shift(); //get first part, and remove from array
			var queryString=urlparts.join("?"); //join it back up
			var prefix = encodeURIComponent(parameter)+'=';
			var pars = queryString.split(/[&;]/g);
			for(var i= pars.length; i-->0;) {               //reverse iteration as may be destructive
				if(pars[i].lastIndexOf(prefix, 0)!==-1) {   //idiom for string.startsWith
					pars.splice(i, 1);
				}
			}
			url = urlBase+'?'+pars.join('&');
			if(fragment[1]) {
				url += "#" + fragment[1];
			}
		}
		return url;
	}
});
