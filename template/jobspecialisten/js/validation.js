	function emailFind(){
		val = $('#mail1').val();
		if (val.length >= 8) {
			$.get('/common/query/email/'+val, function(data){
				$("#check_mail").html('<img src='+data+'>');
			});
		} else {
			$("#check_mail").html('<img src="/template/jobspecialisten/icon/stop.png">');
		}
	}	

	function verifyLicens(id){
		val = $('#'+id).val();
		$.get('/modules/jobspecialisten/license/verify_licens/'+val, function(data){
			$("#licens_icon").html('<img src='+data+'>');
		});
	}

	function compareEmail(){
		if ($('#mail1').val() == $('#mail2').val()){
			$("#recheck_mail").html('<img src="/template/jobspecialisten/icon/accept.png">');
		} else {
			$("#recheck_mail").html('<img src="/template/jobspecialisten/icon/stop.png">');
		}
	}

	function checkPW(){
		re = /^(?=.*[a-zA-Z])(?=.*[0-9])/;
		if ($('#pw1').val().length >= 6) {
			if (re.test(document.getElementById("pw1").value)){
				$("#check_pw").html('<img src="/template/jobspecialisten/icon/accept.png">');
			} else {
				$("#check_pw").html('<img src="/template/jobspecialisten/icon/stop.png">');
			}
		} else {
			$("#check_pw").html('<img src="/template/jobspecialisten/icon/stop.png">');
		}
	}

	function comparePW(){
		if ($('#pw1').val() == $('#pw2').val()){
			$("#recheck_pw").html('<img src="/template/jobspecialisten/icon/accept.png">');
		} else {
			$("#recheck_pw").html('<img src="/template/jobspecialisten/icon/stop.png">');
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
			if (imgsrc == "/template/jobspecialisten/icon/stop.png"){
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