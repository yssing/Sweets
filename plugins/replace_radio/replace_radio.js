var radioFalse = '/plugins/replace_radio/box.png';
var radioTrue = '/plugins/replace_radio/tick.png';
jQuery.fn.replace_radio = function() {	
	$("input:radio").each(function(){
		$(this).css('display','none');
		if ($(this).prop( "checked" ) == true){
			$(this).wrap(' <label for="'+$(this).attr("id")+'"></label> ');
			$(this).before(' <img src="'+radioTrue+'" >');
		} else {
			$(this).wrap(' <label for="'+$(this).attr("id")+'"></label> ');
			$(this).before('<img src="'+radioFalse+'" >');
		}
		$(this).click(function() {
			replace_single_radio($(this).attr("name"));
		});
    });
	
	var replace_single_radio = function(radio_name) {	
		$('input[name='+radio_name+']' ).each(function(){
			$(this).parent().children("img").remove();
			$(this).unwrap();
			if ($(this).prop( "checked" ) == true){
				$(this).wrap(' <label for="'+$(this).attr("id")+'"></label> ');
				$(this).before(' <img src="'+radioTrue+'" >');
			} else {
				$(this).wrap(' <label for="'+$(this).attr("id")+'"></label> ');
				$(this).before(' <img src="'+radioFalse+'" >');
			}		
		});
	}
}

$(window).load(function(){
	$(document).replace_radio();
});