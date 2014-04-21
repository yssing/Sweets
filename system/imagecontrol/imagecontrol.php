<?php
include_once('contributions/resize.class.php');	

class imagecontrol{
	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = views::displayEditListview(files::fileLister('uploads/small',6)).'<br />';		
		$body .= form::beginForm('upload',PATH_WEB.'/system/image/upload');	
		$body .= form::file('','file').'<br />';
		$body .= form::endForm('upload');
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();		
	}	
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error('403');
		}
		$file = urldecode($args['path']);
		$filetype = form::getFileExtension($file);
		if($filetype == 'jpg' || $filetype == 'gif' || $filetype == 'jpeg' || $filetype == 'png'){
			$path = str_replace('www/','',$file);
			$path = str_replace('small','images',$path);
			$body = '<img src="'.PATH_WEB.'/'.$path.'" />';
		} else {
			route::error('404');
		}
			
		template::initiate('form');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}	
	
	public static function uploadAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	

		if(form::validate('upload')){
			$filename = $_FILES["file"]["name"];				
			$filetype = form::getFileExtension($_FILES["file"]["name"]);
			if($filetype){
				if (($filetype == "gif")
				|| ($filetype == "png")
				|| ($filetype == "jpeg")
				|| ($filetype == "jpg")){
					if ($_FILES["file"]["error"] > 0){
						if($_FILES["file"]["error"] == 1){
							form::errMsg(language::readType('IMAGETOBIG'));
						} else {
							form::errMsg("Return Code: ".$_FILES["file"]["error"]);
						}
					} else {
						$path = "uploads";
						form::checkfolder("","uploads");
						form::checkfolder("uploads/","small");
						form::checkfolder("uploads/","medium");
						form::checkfolder("uploads/","images");

						move_uploaded_file($_FILES["file"]["tmp_name"],$path."/images/".$filename);
						try {
							$obj = new Resize($path."/images/".$filename);
							$obj->setNewImage($path."/small/".$filename);
							$obj->setProportional(1);
							$obj->setDegrees(0);
							$obj->setNewSize(120,100);
							$obj->make();

							$obj = new Resize($path."/images/".$filename);
							$obj->setNewImage($path."/medium/".$filename);
							$obj->setProportionalFlag('H');
							$obj->setProportional(1);
							$obj->setDegrees(0);
							$obj->setNewSize(800,600);
							$obj->make();
						}
						catch (Exception $e) {
							die($e);
						}
					}
					route::redirect('system/image/list');
				} else {
					form::errMsg(language::readType('WRONGIMAGETYPE'));
				}	
			}
		}			
	}		
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}		
		form::rrmdir('uploads/images/'.$args[0]);
		form::rrmdir('uploads/medium/'.$args[0]);
		form::rrmdir('uploads/small/'.$args[0]);		
		route::redirect('system/image/list');	
	}	
}
?>