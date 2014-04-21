<?php
	// loading classes
	require_once('../settings/user_defines.php');
	
	// Make output a real JavaScript file!
	header('Content-type: text/javascript'); // browser will now recognize the file as a valid JS file

	// prevent browser from caching
	header('pragma: no-cache');
	header('expires: 0'); // i.e. contents have already expired
?>


/** Find city from zip **/
function zipcityReq(ReqID,RevID){
	var val = $('#'+ReqID).val();
	$.get('<?php echo PATH_WEB;?>/common/query/zipcity/'+val, function(data) {
		$('#'+RevID).val(data);
	});	
}

/** Find customer e-mail **/
function emailReq(ReqID,ImgId){
	val = $('#'+ReqID).val();
	$.get('<?php echo PATH_WEB;?>/common/query/email/'+val, function(data){
		$("#"+ImgId).attr("src",data);
	});
}	
	
/** Find customer username **/
function loginReq(ReqID,ImgId){
	val = $('#'+ReqID).val();
	$.get('<?php echo PATH_WEB;?>/common/query/username/'+val, function(data){
		$("#"+ImgId).attr("src",data);
	});	
}

function removetext(id){
	$('#'+id).val('');
}

function stripAlphaChars(id){ 
	var strOut = $('#'+id).val(); 
    strOut = strOut.replace(/[^,0-9]/g, '');
	$('#'+id).val(strOut);
}

function numberWithCommas(n) {
    var parts=n.toString().split(".");
    return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".") + (parts[1] ? "," + parts[1] : "");
}

function thousandSep(id){
	var number = $('#'+id).val();
	$('#'+id).val(numberWithCommas(number));				
}

function showRecoverLogin(){
	$.get('<?php echo PATH_WEB;?>/common/recover_login', function(data) {
		showLightBox(data,400,140);		
	});	
}

function showAdminLogin(path){
	$.get('<?php echo PATH_WEB;?>/system/login/admin', function(data) {
		showLightBox(data,400,140);	
	});
}

function randomString(id){
	var text = '';
	var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

	for( var i=0; i < 32; i++ ){
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	$('#'+id).val(text);	
}

function editElement(path,element){
	var data = {};
	data['path'] = path;
	data['element'] = element;

	$.post('<?php echo PATH_WEB;?>/cms/elementcontrol/editPath/', data, function(data){ 
		showLightBox(data,920,620);						
	});	
}

$( document ).ready(function() {
			if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
				var msViewportStyle = document.createElement('style')
				msViewportStyle.appendChild(
					document.createTextNode(
						'@-ms-viewport{width:auto!important}'
					)
				)
				document.querySelector('head').appendChild(msViewportStyle)
			}
});