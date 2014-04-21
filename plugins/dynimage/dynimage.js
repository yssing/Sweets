var classes = 'img-responsive img-rounded pointer';

function dynImage() {
	//get all the input fields on the page
	images = document.getElementsByTagName('img');
	for(var i=0; i < images.length; i++) {
		images[i].setAttribute("id", 'image'+i);		
		images[i].setAttribute("class", classes);		
		imgsrc = images[i].getAttribute('src');
		
		// check if image is nested in a div with class, if so, the next step is skipped.
		if(!images[i].parentNode.getAttribute('class')){
			var div = $('<div class="col-md-12 col-xs-12"></div>').insertAfter(images[i]);		
			$( $(images[i]).remove() ).appendTo(div);	
		}
		imgWidth = document.getElementById('image'+i).naturalWidth;
		imgHeight = document.getElementById('image'+i).naturalHeight;	
		
		if(imgWidth == undefined && imgHeight == undefined){
			natural = getNatural(document.getElementById('image'+i));
			imgWidth = natural.width;
			imgHeight = natural.height;
			images[i].onclick = function() {"show('"+imgsrc+"',"+imgWidth+","+imgHeight+",this.id)"}; // for IE					
		}

		if(images[i].getAttribute('onclick') == null){
			images[i].setAttribute('onclick',"show('"+imgsrc+"',"+imgWidth+","+imgHeight+",this.id)"); // for FF
		}
	}
}

function show(imgSrc,imgWidth,imgHeight,id){
	if (typeof showLightBox == 'function') { 
		if (typeof toggleFullScreen == 'function'){		
			imgSrc = '<img src="'+imgSrc+'" class="'+classes+'" onclick="toggleFullScreen(\'lightbox_content\')" />';
		} else {
			imgSrc = '<img src="'+imgSrc+'" class="'+classes+'" />';
		}
		showLightBox(imgSrc,imgWidth,imgHeight);
	} else if ( typeof toggleFullScreen == 'function' ){		
		imgSrc = '<img src="'+imgSrc+'" class="'+classes+'" />';
		toggleFullScreen(id);
	} else {
		imgSrc = '<img src="'+imgSrc+'" class="'+classes+'" />';
	}
}

function getNatural(DOMelement) {
	var img = new Image();
	img.src = DOMelement.src;
	return {width: img.width, height: img.height};
}

$(document).ready(function(){
	dynImage();
});	