function checkPW(){
	re = /^(?=.*[a-zA-Z])(?=.*[0-9])/;
	if ($('#password1').val().length >= 6) {
		if (re.test(document.getElementById("password1").value)){
			$("#check_pw").html('<img src="/template/akkordberegner/icon/accept.png">');
		} else {
			$("#check_pw").html('<img src="/template/akkordberegner/icon/stop.png">');
		}
	} else {
		$("#check_pw").html('<img src="/template/akkordberegner/icon/stop.png">');
	}
}

function comparePW(){
	if ($('#password1').val() == $('#password2').val()){
		$("#recheck_pw").html('<img src="/template/akkordberegner/icon/accept.png">');
	} else {
		$("#recheck_pw").html('<img src="/template/akkordberegner/icon/stop.png">');
	}
}

/**
 * Since all the mandatory fields are already validated, we simply look for the
 * icons showing failed validation
 */
function submitform(){
	var stop_submit = false;
	images = document.getElementsByTagName('img');
	for(var i=0; i < images.length; i++) {
		imgsrc = images[i].getAttribute('src');
		if (imgsrc == "/template/akkordberegner/icon/stop.png"){
			stop_submit = true;
		}
	}

	if ($("#accept").is(':checked')){
		if (!stop_submit){
			document.step1.submit();
		}
	} else {
		$( "#accept" ).parent().effect( 'highlight' );
	}
}