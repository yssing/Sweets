jQuery.fn.dynImage = function() {	
	$("img[src!='']").each(function(){
		$(this).addClass("img-responsive");
    });
}

$(window).load(function(){
	$(document).dynImage();
});