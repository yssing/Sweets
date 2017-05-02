var setCookie = function (){
	console.log('setCookie');
	document.cookie = "acceptcookie = 1; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
	$("#cookienotification").slideUp();
}

var getCookie = function (cname){
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i < ca.length; i++) {
		var c = ca[i].trim();
		if (c.indexOf(name) == 0){ 
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

var checkCookie = function (){
	var acceptcookie = getCookie("acceptcookie");
	if (acceptcookie == "") {
		getCookieText();
	}
}

var getCookieText = function (){
	$.get('/plugins/cookienotification/getText', function(data){
		$('body').prepend('<div class="cookie_notification" id="cookienotification" onclick="setCookie()"><div class="container"><div>'
		+data+'<button class="col-sm-6 cookie_n_button" onclick="setCookie()">OK</button></div></div></div>');
	});
}	

$(document).ready(function(){
	checkCookie();
});