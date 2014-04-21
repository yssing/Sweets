// Calendar plugin. 

function displayDate(opener,datevar,parent){
	datefield = $('#'+opener).val();
	if($('#'+opener+'Div').length == 0){
		$('#'+opener).after('<div class="dateDiv" id="'+opener+'Div"></div>');
		$('#'+opener+'Div').hide();
	} else {
		if(parent == 1){
			$('#'+opener+'Div').toggle();
		}
	}
	$.get('../plugins/calendar/calendar.php?opener='+opener+"&datevar="+datevar+"&datefield="+datefield, function(data) {
		if(parent == 1){
			$('#'+opener+'Div').show("fast",function() {
				// Animation complete.
				$('#'+opener+'Div').html(data);
			});
			
			$('#'+opener+'Div').html(data);
		} else {
			$('#'+opener+'Div').html(data);
		}
	});	
}

function changeBackground(cellid){
	$('#'+cellid).css("background","#ffffff");
}

function returnBackground(cellid){
	$('#'+cellid).css("background","");
}		

function returnTime(opener,date){
	$('#inputDate'+opener).val(date);
}

function formTime(id){
	time = $('#'+id).val();
	switch(time.length){
		case 2:
		$('#'+id).val(time+=':');
		break;
		case 5:
		$('#'+id).val(time+=':');
		break;			
	}
	if(time.length > 8){
		$('#'+id).val(time.substring(0,8));
	}
}

function formDate(id){
	time = $('#'+id).val();
	switch(time.length){
		case 4:
		$('#'+id).val(time+='-');
		break;
		case 7:
		$('#'+id).val(time+='-');
		break;			
	}
	if(time.length > 10){
		$('#'+id).val(time.substring(0,10));
	}
}	

function parseTime(opener,date,time){
	if(date == 'yyyy-mm-dd'){
		date = '0000-00-00';
	}
	if(time == 'tt:mm:ss'){
		time = '00:00:00';
	}
	parsedValue = date+' ' +time;
	$('#'+opener).val(parsedValue);
	hideDate(opener);
}

function hideDate(opener){
	open = 1;
	$('#'+opener+'Div').hide();
	$('#'+opener).removeAttr('disabled');
}