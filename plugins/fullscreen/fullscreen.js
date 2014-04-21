function toggleFullScreen(element) {
	FullScreenElement = document.getElementById(element);
	if (!document.mozFullScreen && !document.webkitFullScreen) {
		if (FullScreenElement.mozRequestFullScreen) {
			FullScreenElement.mozRequestFullScreen();
		} else {
			FullScreenElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
		}
		$('#'+FullScreenElement).addClass("fullscreen");
	} else {
		if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else {
			document.webkitCancelFullScreen();
		}
		$('#'+FullScreenElement).removeClass("fullscreen");
	}
}

$(document).ready(function(){

});	