
function saveResume(){
	var data = {};
	data['resume'] = $("#resume").val();
	
	$("#js_profile_resume").html('<div class="nextform"><img src="/template/jobspecialisten/images/download.gif" border="0"></div>');
	
	if (data){
		$.post('/modules/jobspecialisten/competence/add_resume/', data, function(data){
			$("#js_profile_resume").html(data);
		});
	}
}

function updateResume(id){
	var data = {};
	data['resume'] = $("#resume"+id).val();
	data['id'] = id;
	
	$("#js_profile_resume").html('<div class="nextform"><img src="/template/jobspecialisten/images/download.gif" border="0"></div>');
	
	if (data){
		$.post('/modules/jobspecialisten/competence/update_resume/', data, function(data){
			$("#js_profile_resume").html(data);
		});
	}
}

function saveCourse(){
	var data = {};
	data['course'] = $("#course").val();
	data['courseyear'] = $("#courseyear").val();
	
	$("#js_profile_courses").html('<div class="nextform"><img src="/template/jobspecialisten/images/download.gif" border="0"></div>');
	
	if (data){
		$.get('/modules/jobspecialisten/competence/add_course/', data, function(data){
			$("#js_profile_courses").html(data);
		});
	}
}

function updateCourse(id){
	var data = {};
	data['course'] = $("#course"+id).val();
	data['courseyear'] = $("#courseyear"+id).val();	
	data['id'] = id;
	
	$("#js_profile_courses").html('<div class="nextform"><img src="/template/jobspecialisten/images/download.gif" border="0"></div>');
	
 	if (data){
		$.get('/modules/jobspecialisten/competence/update_course/', data, function(data){
			$("#js_profile_courses").html(data);
		});
	}
}

function saveExperience(){
	var data = {};
	data['place'] = $("#experienceplace").val();
	data['title'] = $("#experiencetitle").val();
	data['fromdate'] = $("#experiencefromdate").val();
	data['todate'] = $("#experiencetodate").val();
	data['description'] = $("#experiencedescription").val();
	
	$("#js_profile_experience").html('<div class="nextform"><img src="/template/jobspecialisten/images/download.gif" border="0"></div>');
	
	if (data){
		$.get('/modules/jobspecialisten/competence/add_experience/', data, function(data){
			$("#js_profile_experience").html(data);
		});
	}
}

function updateExperience(id){
	var data = {};
	data['place'] = $("#experienceplace"+id).val();
	data['title'] = $("#experiencetitle"+id).val();
	data['fromdate'] = $("#experiencefromdate"+id).val();
	data['todate'] = $("#experiencetodate"+id).val();
	data['description'] = $("#experiencedescription"+id).val();
	data['id'] = id;	
	
	$("#js_profile_experience").html('<div class="nextform"><img src="/template/jobspecialisten/images/download.gif" border="0"></div>');
	
	if (data){
		$.get('/modules/jobspecialisten/competence/update_experience/', data, function(data){
			$("#js_profile_experience").html(data);
		});
	}
}