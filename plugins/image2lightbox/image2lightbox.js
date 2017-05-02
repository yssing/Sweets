jQuery.fn.image2Lightbox = function() {	
	//get all the input fields on the page
	images = document.getElementsByTagName('img');
	for(var i=0; i < images.length; i++) {
		images[i].setAttribute("id", 'image'+i);
		images[i].className = images[i].className + " pointer";
		imgsrc = images[i].getAttribute('src');
		
		imgWidth = document.getElementById('image'+i).naturalWidth;
		imgHeight = document.getElementById('image'+i).naturalHeight;	
		
		if (imgWidth == undefined && imgHeight == undefined){
			natural = getNatural(document.getElementById('image'+i));
			imgWidth = natural.width;
			imgHeight = natural.height;
			images[i].onclick = function() {"showInLightbox('"+imgsrc+"',"+imgWidth+","+imgHeight+",this.id)"}; // for IE					
		}

		if (images[i].getAttribute('onclick') == null){
			images[i].setAttribute('onclick',"showInLightbox('"+imgsrc+"',"+imgWidth+","+imgHeight+",this.id)"); // for FF
		}
	}
}

var showInLightbox = function (imgSrc,imgWidth,imgHeight,id){
	if (typeof showLightBox == 'function') { 
		if (typeof toggleFullScreen == 'function'){		
			imgSrc = '<img src="'+imgSrc+'" class="center" onclick="toggleFullScreen(\'lightbox_content\')" />';
		} else {
			imgSrc = '<img src="'+imgSrc+'" class="center" />';
		}
		showLightBox(imgSrc,imgWidth+10,imgHeight+10);
	}
}

var getNatural = function(DOMelement) {
	var img = new Image();
	img.src = DOMelement.src;
	return {width: img.width, height: img.height};
}

$(document).ready(function(){
	$(document).image2Lightbox();
});	