/** Find city from zip **/
function zipcityReq(ReqID,RevID){
	var val = $('#'+ReqID).val();
	$.get('/common/query/zipcity/'+val, function(data) {
		$('#'+RevID).val(data);
	});	
}

/** Find customer e-mail **/
function emailReq(ReqID,ImgId){
	val = $('#'+ReqID).val();
	$.get('/common/query/email/'+val, function(data){
		$("#"+ImgId).attr("src",data);
	});
}	
	
/** Find customer username **/
function loginReq(ReqID,ImgId){
	val = $('#'+ReqID).val();
	$.get('/common/query/username/'+val, function(data){
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
	$.get('/common/recover_login', function(data) {
		showLightBox(data,400,140);		
	});	
}

function showAdminLogin(path){
	$.get('/system/login/admin', function(data) {
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

	$.post('/cms/elementcontrol/editPath/', data, function(data){ 
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