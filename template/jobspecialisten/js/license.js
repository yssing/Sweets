
function newLicense(id){
	$.get('/modules/jobspecialisten/license/newlicense', function(data) {
		$('#'+id).val(data);		
	});	
}