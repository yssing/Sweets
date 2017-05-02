// Temporary variables to hold mouse x-y pos.s
var MyMouseXoffset = 0;
var MyMouseYoffset = 0;
var globalmove = 0;
var globCountDown = 5;
var popupOffset = 0;

function closeRed(){
	$('#lightbox_close').attr("class", 'lightbox_close_red');
}

function closeGrey(){
	$('#lightbox_close').attr("class", 'lightbox_close_grey');
}

function lightboxScroll()  {  
	$('#lightbox').css("top",window.pageYOffset+popupOffset+"px");
	$('#lightboxbackground').css("top",window.pageYOffset+"px");
	return true; 
} 

function DelayClose(countdown){
	$('#closeWindow').html("Vinduet lukker automatisk om " + globCountDown + " sekunder.");
	setTimeout("DelayClose()", 1000);
	if (!globCountDown){
		hideLightBox();
	}
	globCountDown--;
}

function centerLightbox(){
	lightboxwidth = $('#lightbox_content').width();
	pos_left = (($(document).width() - lightboxwidth)/2);
	$('#lightbox').css("left",pos_left+"px"); 
}

function showLightBox(content,width,height){
	globalmove = 0;
	pos_left = (($(document).width() - width)/2);
	if (width == 0){
		$('#lightbox').css("position","relative");
	} else {
		$('#lightbox').css("left",pos_left+"px"); 
		$('#lightbox').css("width",width+4+"px"); 
		$('#lightbox_content').css("width",width+"px"); 
	}	
	if (height){
		$('#lightbox_content').css("overflow","auto");

		if ((height + parseInt($('#lightbox').css('margin-top')) + 20) > window.innerHeight){
			height = (window.innerHeight - parseInt($('#lightbox').css('margin-top')) - 50);
		}		
		$('#lightbox_content').css("height",height+2+"px");			
	}
	
	$('#lightbox').css("top", $(window).scrollTop()+"px");	
	$('#lightbox').css("visibility","visible");
	$('#lightbox').show(1);
	
	$('#lightboxbackground').css("visibility","visible");
	$('#lightboxbackground').show(1);
	
	if (content){
		$('#lightbox_content').html(content);
		lightboxHeadline();
	}
}

function lightboxHeadline(){
	//this functions takes the first line of text and puts it in the headline of the lightbox.
	var first_line;
	$("#lightbox_content")
		.contents()
		.each(function() { 
			return !(first_line = this, $.trim(this.innerHTML||this.data));
		});

	var str = $.trim($(first_line).text()).split('\n');	
	$('#lightbox_headline').html('<h5>'+str[0]+'</h5>');
}

function hideLightBox(){
	globCountDown = 5;
	globalmove = 0;
	$('#lightbox').css('opacity',1);
	$('#lightbox_content').html('');
	$('#lightbox').hide(1);
	$('#lightboxbackground').hide(1);
	//if updateThis() is a function in the parentscript, then we use it
	//if not, then we do nothing.
	if ( typeof updateThis == 'function' ) { 
		updateThis(); 
	}	
}

function moveWindow(){
	if (globalmove == 1){
		globalmove = 0;
		MyMouseXoffset = 0
		MyMouseYoffset = 0;
		$('#lightbox').css('opacity',1);
	} else {
		globalmove = 1;
		$('#lightbox').css('opacity',0.8);
	}
}

function storeMyOffset(tempX,tempY){
	if (!MyMouseXoffset){
		MyMouseXoffset = tempX - $('#lightbox').offset().left;
	}
	if (!MyMouseYoffset){
		MyMouseYoffset = tempY - $('#lightbox').offset().top;
	}	
}

function getMouseXY(mouseX,mouseY) {
	var scrollval = -70;
	tempX = mouseX;
	tempY = mouseY;
	// catch possible negative values
	if (tempX < 0){tempX = 0}
	if (tempY < 0){tempY = 0}  

	if (globalmove){
		storeMyOffset(tempX,tempY)
		$('#lightbox').css("position","absolute");
		$('#lightbox').css("left",tempX-MyMouseXoffset+"px");
		$('#lightbox').css("top",tempY-MyMouseYoffset+scrollval+"px");
	}
	return true
}

function initLightbox(){
	var returnString = '';
	returnString += '<div id="lightboxbackground" class="lightboxbackground" onClick="hideLightBox()"></div>';
	returnString += '<div id="lightbox" class="lightbox"><table cellpadding="0" cellspacing="0">';
	returnString += '<tr><td class="lightbox_header" onMouseDown="moveWindow()" colspan="3"><div class="lightbox_headline" id="lightbox_headline"></div><span id="lightbox_close" class="lightbox_close_grey" onClick="hideLightBox()" onMouseOut="closeGrey()" onMouseOver="closeRed()">&nbsp;</span></td>';
	returnString += '</tr><tr><td class="lightbox_border"></td><td>';		
	returnString += '<div id="lightbox_content" class="lightbox_content"><img src="../plugins/lightbox/download.gif" /></div>';	
	returnString += '</td><td class="lightbox_border"></td></tr><tr><td class="lightbox_footer" colspan="3"></td></table></div>';
	return returnString;
}

$(window).resize(function() {
	centerLightbox();
});

$(document).ready(function(){
	try {
		$("body").prepend(initLightbox());
	} catch(e) {
		alert(e.message);
	}
	
	$(document).on ('keydown', function (e) {	
		if (e.which == 27){
			hideLightBox(); 
		}
	});
	
	$(window).mousemove(function( event ) {		
		getMouseXY(event.pageX,event.pageY);
	});	
	
    $(window).scroll(function(){
		lightboxScroll();
    })	
});	