$.fn.adminmenu = function (data) {
	var useName = '';
	$(".adminmenu > li").each(function() {
		if ($(this).hasClass("headline")){
			useName = $(this).text();
			$(this).on('click', this, function() {
				toggleView($(this).text());
			});
		} else {
			$(this).addClass(useName);
			$(this).css('display','none');
		}
	});
	
	function toggleView(element){
		$("."+element).slideToggle();
	}
}

$(document).ready(function(){
	$(document).adminmenu();
});	