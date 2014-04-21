function showAdminMenu() {
	$('#edit_buttons').css('width','190px');
	$('#edit_buttons').css('visibility','visible');
	$('#editorial').css('background-color','#cdcdcd');
}

function hideAdminMenu() {
	$('#edit_buttons').css('width','0px');
	$('#edit_buttons').css('visibility','hidden');
	$('#editorial').css('background-color','#f5f5f5');
};

function initAdmin(editmode,url){
	editmode = (editmode) ? editmode : '';
	url = '/plugins/admin/'+url+editmode;
	$.get(url, function(data) {
		$("body").prepend(data);
	});
}

function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}

function getArticleId(url){
	result = url.split('/');
	return (result[result.length-1]) ? result[result.length-1] : 0;
}

$(document).ready(function(){
	initAdmin(getURLParameter('editmode'),getArticleId(document.URL));
	hideAdminMenu();
});	