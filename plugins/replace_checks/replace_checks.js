var imgFalse = '/plugins/replace_checks/box.png';
var imgTrue = '/plugins/replace_checks/tick.png';

/** replaces checkboxes with images **/
function replaceChecks() {
	//get all the input fields on the page
	inputs = document.getElementsByTagName('input');
	//cycle trough the input fields
	for(var i=0; i < inputs.length; i++) {
		//check if the input is a checkbox
		if(inputs[i].getAttribute('type') == 'checkbox') {		  
			//create a new image
			var img = document.createElement('img');
			//check if the checkbox is checked
			if(inputs[i].checked) {
				img.src = imgTrue;
			} else {
				img.src = imgFalse;
			}
			//set image ID and onclick action
			img.id = 'checkImage'+i;
			//set image
			img.onclick = new Function('checkChange('+i+')');
			//place image in front of the checkbox
			inputs[i].parentNode.insertBefore(img, inputs[i]);

			//hide the checkbox
			inputs[i].style.display='none';
		}
	}
}

//change the checkbox status and the replacement image
function checkChange(i) {
	if(inputs[i].checked) {
		inputs[i].checked = '';
		document.getElementById('checkImage'+i).src=imgFalse;
	} else {
		inputs[i].checked = 'checked';
		document.getElementById('checkImage'+i).src=imgTrue;
	}
}

$(document).ready(function(){
	replaceChecks();
});	