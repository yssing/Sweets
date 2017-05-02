<?php

class gallerycontrol{

	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		
		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}
		$body = views::displayEditListview(ephotoGallery::listGalleries($searchVal));

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function deleteAction($args){
		if (!$args[0]){
			return false;
		}
		
		ephotoGallery::destroyGallery($args[0]);
		route::redirect('modules/ephoto/gallery/list');
	}
	
	public static function getSliderAction($args){
		if ($args[0]){
			list($galleryName,$imagelist) = ephotoGallery::readGallery($args[0]);
			$imagearray = explode(',',$imagelist);
				$sliders = '';
				foreach($imagearray as $single){
					list($id,$imagename,$imagedesc) = photo::readPhoto(photo::small($single));
					$sliders .= '<li><img src="/'.photo::full($single).'">';
					if($imagename){
						$sliders .= '<p class="caption">'.$imagename.'</p>';
					}
					if($imagedesc){
						$sliders .= '<p class="slider-text">'.$imagedesc.'</p>';
					}
					$sliders .= '</li>';
				}
			template::setValue('slider_items',$sliders);
			$slider = template::useBlock('image-slider');
		} else {
			$slider = '';
		}
		echo $slider;
	}
	
	public static function showGalleryAction($args){
		if ($args[0]){
			list($galleryName,$imagelist) = ephotoGallery::readGallery($args[0]);
			$imagearray = explode(',',$imagelist);
				$sliders = '';
				foreach($imagearray as $single){
					list($id,$imagename,$imagedesc) = photo::readPhoto(trim(photo::small($single)));
					$sliders .= '<li><img src="/'.photo::full($single).'">';
					if($imagename || $imagedesc){
						$sliders .= '<p class="caption">';						
						if($imagename){
							$sliders .= $imagename . "<br>";
						}
						if($imagedesc){
							$sliders .= $imagedesc;
						}						
						$sliders .= '</p>';
					}	
					$sliders .= '</li>';
				}
			template::setValue('slider_items',$sliders);
			$slider = template::useBlock('image-slider');
		} else {
			$slider = '';
		}
		
		template::initiate('gallery');
			template::noCache();
			template::header($galleryName);
			template::body($slider);
			template::title(text::readTextByKey('TITLE'));
			template::footer(text::readTextByKey('FOOTER'));
			template::replace('[COPY]',text::readTextByKey('COPY'));
			template::replace('[MENU]',menu::makeMenu());
		template::end();		
	}
	
	public static function findAction($args){
		if (!$args[0]){
			return false;
		}
		echo ephotoGallery::readSingleGallery($args[0]);
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if(isset($args[0])){
			$id = $args[0];
			list($name,$imagelist) = ephotoGallery::readGallery($id);
		} else {
			$id = $name = $imagelist = '';
		}

		$ephoto = array("onClick" => "showePhotoSel('imagelist','multiple')");

		$body = form::beginForm('update','modules/ephoto/gallery/update');
			$body .= form::fieldset('field1',language::readType('NAME'),form::input($name,'name',TEXT));
			$body .= form::fieldset('field2',language::readType('IMAGELIST'),form::textArea($imagelist,'imagelist'),$ephoto);
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');

		template::initiate('form');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}

	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		if (form::validate('update')){
			if ($args['id']){
				ephotoGallery::updateGallery($args['id'],$args['name'],$args['imagelist']);
			} else {
				ephotoGallery::createGallery($args['name'],$args['imagelist']);
			}
			route::redirect('modules/ephoto/gallery/');
		}
	}	

	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array(
					"name" => "varchar(50)",
					"imagelist" => "text");
		$result = $databaseadmin->createTable('ephoto_gallery',$what,"PK_GalleryID");

		// Importing the language settings for the entire module
		require_once('system/utils/import.class.php');
		import::importCSV('modules/ephoto/csv/language.csv','generic_language');
	}	
}
?>