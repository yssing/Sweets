jQuery.fn.cropImage = function(img_crop_id) {	
	/**
	 * append the overlay
	 */
	$("#"+img_crop_id).parent().append('<div id="img-overlay" class="img-overlay"> </div>');
	
	/**
	 * remove it, if image is correct size or lower resolution than maximum size.
	 */
	if ($("#"+img_crop_id).width() <= $("#img-overlay").width() && $("#"+img_crop_id).height() <= $("#img-overlay").height()){
		$('#img-overlay').remove();
		return false;
	}
	
	var tempy = Math.floor($("#"+img_crop_id).offset().top);
	var tempx = Math.floor($("#"+img_crop_id).offset().left);
	var offsetX = 15;
	var offsetY = 15;
	var storedY = 0;
	var storedX = 0;	

    $(document).mousemove(function(e){
		moveBlock(e);
    });	
		
	$('#img-overlay').click(function(){
		confirmCrop();
	});	

	$( "#"+img_crop_id ).click(function() {
		confirmCrop();
	});	
	
	var moveBlock = function(e){
        var x = e.pageX - tempx + offsetX;
        var y = e.pageY - tempy + offsetY;

		if (offsetY < y ){		
			if (y < ($("#"+img_crop_id).height() - $("#img-overlay").height() + offsetY)) {			
				$('#img-overlay').css("top",(y));
				storedY = y;
			} else {
				storedY = $("#"+img_crop_id).height() - $("#img-overlay").height() + offsetY;
				$('#img-overlay').css("top",storedY);
			}
		} else {
			$('#img-overlay').css("top",offsetY);
			storedY = offsetY;
		}
		
		if (offsetX < x){ 
			if (x < ($("#"+img_crop_id).width() - $("#img-overlay").width() + offsetX)) {
				$('#img-overlay').css("left",(x));
				storedX = x;
			} else {
				storedX = $("#"+img_crop_id).width() - $("#img-overlay").width() + offsetX;
				$('#img-overlay').css("left",storedX);
			}
		} else {
			$('#img-overlay').css("left",offsetX);
			storedX = offsetX;
		}
	}
	
	var confirmCrop = function(){
		$.get('/common/query/get_text/confirmcrop', function(data){
			if (confirm(data)){
				var newwidth = $("#img-overlay").width();
				var newheight = $("#img-overlay").height();
				
				$('#img-overlay').remove();
				$("#"+img_crop_id).parent().append('<div id="img-overlay-crop" class="img-overlay-crop"> </div>');
				
				$("#img-overlay-crop").css("width",(newwidth-2));
				$("#img-overlay-crop").css("height",(newheight-2));
				
				$("#img-overlay-crop").css("top",storedY);
				$("#img-overlay-crop").css("left",storedX);
				window.location.href = '/modules/jobspecialisten/image/crop_image/?x='+newwidth+'&y='+newheight+'&xoffset='+storedX+'&yoffset='+storedY;
			}
		});
	}
}

$(window).load(function(){
	$(document).cropImage('user_img_id');
});