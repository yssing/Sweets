(function ($) {
	var bpGalleryList = new Array();
	var bpGalleryRange = 4;
	var bpGalleryIntervId;
	var bpGalleryCounter = 0;
	var classes = 'img-responsive img-rounded pointer';
	var list = '';

	/**
	 * Initializes the gallery changer.
	 *
	 * @param string galleryName The name of the gallery to use
	 * @param int range How many images to show per gallery view
	 * @param string appendto what element to append the gallery to, needs to be an id!
	 */
	$.fn.initGallery = function(galleryName, range, appendto) {
		bpGalleryRange = (range) ? range : 4;
		var url = '/plugins/gallery/list/'+galleryName;
		var i = 0;

		$.getJSON(url, function(data) {
			$.each( data, function( key, val ) {
				width = parseInt(val[1])+12;
				height= parseInt(val[2])+10;
				imgEvent = 'showLightBox(\'<img src=/' + val[0] + '>\',' + width + ',' + height + ',this.id)';
				bpGalleryList[i] = '<img style="display:inline" class="'+classes+'" id="bpImage'+i+'" onclick="'+imgEvent+'" src="/' + val[0] + '" />';
				if (i == bpGalleryRange){
					imageChanger();
				}
				i++;
			});
		});
		$("#"+appendto).append('<div class="container bpGalleryContent hidden-xs" id="bpGalleryContent"></div>');
		bpGalleryIntervId = setInterval(imageChanger, 5000);
	};

	var imageChanger = function (){
		var list = '';
		if ((bpGalleryRange*bpGalleryCounter) > bpGalleryList.length){
			bpGalleryCounter = 0;
		}

		for (i = (bpGalleryCounter*bpGalleryRange); i < (bpGalleryCounter*bpGalleryRange)+bpGalleryRange; i++){
			if (i >= bpGalleryList.length){
				break;
			} else {
				list += bpGalleryList[i];
			}
		}
		if(list != ''){			
			$('#bpGalleryContent').html(list);
		}
		
		bpGalleryCounter++;
		return true;
	};
})(jQuery);