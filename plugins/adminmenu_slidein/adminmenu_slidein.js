function showAdminMenu() {
	$('#edit_buttons').css('width','190px');
	$('#edit_buttons').css('display','inline-block');
	$('#editorial').css('background-color','#cdcdcd');
}

function hideAdminMenu() {
	$('#edit_buttons').css('display','none');
	$('#editorial').css('background-color','#f5f5f5');
};

function initAdmin(editmode,articleid){
	editmode = (editmode) ? editmode : '';

	var array = document.URL.split('/?')
	var data = {};
	data['editmode'] = editmode;
	data['docurl'] = array[0];
	data['articleid'] = articleid;

	if (data){
		$.post('/plugins/adminmenu_slidein/', data, function(data){
			$("body").prepend(data);
		});
	}
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