/** Find city from zip **/
function zipcityReq(ReqID,RevID){
	var val = $('#'+ReqID).val();
	$.get('/common/query/zip_city/'+val, function(data) {
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

/** update days selector with one that realtes to selected month **/
function updateDays(RequestID,TargetID){
	val = $('#'+RequestID).val();
	$.get('/common/query/days_in_month/'+val+'/'+TargetID, function(data){
		$('#'+TargetID).replaceWith(data);
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
		showLightBox(data,420,160);
	});	
}

function randomString(id, number){
	if (typeof number === 'undefined') { 
		number = 32; 
	}
	var text = '';
	var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

	for( var i=0; i < number; i++ ){
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	$('#'+id).val(text);	
	alert(text);
}

function editText(id){
	var data = {};
	data['id'] = id;

	$.post('/modules/cms/text/edit_text/', data, function(data){ 
		showLightBox(data,920,620);
	});
}

function updateText(id){
	var data = {};
	data['id'] = id;	
	data['bodytext'] = tinyMCE.get('bodytext').getContent();
	data['formsaltupdate'] = $('#formsaltupdate').val();

	$.post('/modules/cms/text/update_text/', data, function(data){ 
		showLightBox(data,920,620);
	});
}

function editElement(id,element){
	var data = {};
	data['id'] = id;
	data['element'] = element;

	$.post('/modules/cms/element/edit_element/', data, function(data){ 
		showLightBox(data,920,620);
	});	
}

/**
 * Confirms a delete.
 * It uses ajax to get the correct language variable for the confirmation
 * The language can be retrieved with the action: '/common/query/get_text/xxxxxxx' <- key
 */
function confirmDel(path){
	$.get('/common/query/get_text/confirmdel', function(data){
		if (confirm(data)){
			window.location.href = path;
		}
	});
}

/**
 * Reads terms and conditions and display them in a lightbox
 */
function terms(){
	$.get('/modules/cms/text/get_text_by_key/terms', function(data){ 
		showLightBox(data,480,640);
	});	
} 

/**
 * reads the text using google Text2Speech engine
 */
function speechtext(textid){
	$.get('/modules/cms/speech/read_text/'+textid, function(data){ 
		showLightBox(data,340,80);
	});	
}

/**
 * Opens an ePhoto window for image selection
 * Have to be logged in as admin!
 */
function showePhotoSel(selectorId,selection){
	$.get('/modules/ephoto/photo/sel_list/'+selectorId+'/'+selection, function(data){ 
		showLightBox(data,960,520);
	});
}

function mailtext(textid){
	if (textid == undefined){
		textid = $('#textid').val();
	}

	var postdata = {};
	postdata['receiver_text'] = $('#receiver_text').val();
	postdata['receiver_mail'] = $('#receiver_mail').val();
	postdata['sender_mail'] = $('#sender_mail').val();
	postdata['sender_name'] = $('#sender_name').val();
	postdata['captcha'] = $('#captcha').val();
	postdata['textid'] = textid;
	data = '';
	$.post('/modules/cms/text/send_mail', postdata, function(data){
		showLightBox(data,480,420);
	});
}

function printDiv(divID) {
	//Get the HTML of div
	var divElements = document.getElementById(divID).innerHTML;
	//Get the HTML of whole page
	var oldPage = document.body.innerHTML;

	var postdata = {};
	postdata['divElements'] = divElements;
	data = '';
	$.post('/common/query/print', postdata, function(data){
		document.body.innerHTML = data;
	});	
	
	//Print Page
	window.print();

	//Restore original HTML
	document.body.innerHTML = oldPage;
}

$(document).ready(function() {
	if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
		var msViewportStyle = document.createElement('style')
		msViewportStyle.appendChild(
			document.createTextNode(
				'@-ms-viewport{width:auto!important}'
			)
		)
		document.querySelector('head').appendChild(msViewportStyle)
	}
	
	$( "div.system_error" )
	.mouseover(function() {
		$(this).css("min-height",140);
	})
	.mouseout(function() {
		$(this).css("height",80);
	});	
});