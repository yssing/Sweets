function showScrollToTop(){
	$('#scroll_to_top').css("visibility","visible"); 	
	//$('#scroll_to_top').slideOut("slow");
	$('#scroll_to_top').stop().animate({'margin-right':'0px'},100);
}

function hideScrollToTop(){
	//$('#scroll_to_top').slideUp("slow");
	$('#scroll_to_top').stop().animate({'margin-right':'-100px'},100);
}

function scrollToTop(){
	//window.scrollTo(0,0);
	$("html, body").animate({ scrollTop: 0 }, 250);
	hideScrollToTop();
}

function initScrollToTop(){
	var returnString = '';
	returnString += '<div class="scroll_to_top hidden-xs hidden-sm" id="scroll_to_top" onclick="scrollToTop()">';
	//returnString += '<h2>Scroll til top<h2>';
	returnString += '</div>';
	return returnString;
}

$(document).ready(function(){
	$("body").append(initScrollToTop());
	hideScrollToTop();
	var target = $(".scrolltarget").offset().top;
	var interval = setInterval(function() {
		if ($(window).scrollTop() >= target) {
			showScrollToTop();
		} else {
			hideScrollToTop();			
		}
	}, 250);
});	