(function ($) {
	var bpGalleryList = new Array();
	var bpGalleryRange = 4;
	var bpGalleryIntervId;
	var bpGalleryCounter = 0;
	var classes = 'img-responsive img-rounded pointer';
	
	$.fn.initGallery = function(galleryName, range) {
		bpGalleryRange = (range) ? range : 4;
		var url = '/plugins/gallery/list/'+galleryName;
		var i = 0;

		$.getJSON(url, function(data) {
			$.each( data, function( key, val ) {
				imgEvent = 'show(\'/uploads/images/' + val[0] + '\',' + val[1] + ',' + val[2] + ',this.id)';
				bpGalleryList[i] = '<img style="display:inline" class="'+classes+'" id="bpImage'+i+'" onclick="'+imgEvent+'" src="/uploads/small/' + val[0] + '" />';				
				if(i == bpGalleryRange){
					imageChanger();
				}				
				i++;
			});
		});
		bpGalleryIntervId = setInterval(imageChanger, 10000);
	};

	var imageChanger = function (){
		var list = '';
		if((bpGalleryRange*bpGalleryCounter) > bpGalleryList.length){
			bpGalleryCounter = 0;
		}

		for (i = (bpGalleryCounter*bpGalleryRange); i < (bpGalleryCounter*bpGalleryRange)+bpGalleryRange; i++){
			if(i >= bpGalleryList.length){
				break;
			} else {
				list += bpGalleryList[i];
			}	
		}
		$('#bpGalleryContent').html(list);
		bpGalleryCounter++;
		return true;
	};	
})(jQuery);