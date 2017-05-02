<?php
require_once(PATH_SYS.'utils/imageresize.class.php');

class imagecontrol{
	public static function indexAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}

		$body = views::displayEditListview(files::fileLister('uploads/images/small',6),'listview',0).'<br />';		
		$body .= form::beginForm('upload',PATH_WEB.'/system/image/upload');	
			$body .= form::file('','file[]');
		$body .= form::endForm('upload');
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();		
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		$file = urldecode($args['path']);
		$filetype = form::getFileExtension($file);
		if ($filetype == 'jpg' || $filetype == 'gif' || $filetype == 'jpeg' || $filetype == 'png'){
			$path = str_replace('www/','',$file);
			$path = str_replace('small','full',$path);
			$body = '<img src="/'.$path.'" />';
		} else {
			route::error(404);
		}
			
		template::initiate('form');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}	
	
	public static function uploadAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}	

		if (form::validate('upload')){
			for($i = 0; $i < count($_FILES["file"]["name"]); $i++){					
				$filename = str_replace(' ','_',$_FILES["file"]["name"][$i]);
				$filetype = form::getFileExtension($_FILES["file"]["name"][$i]);
				if ($filetype){
					if (($filetype == "gif")
					|| ($filetype == "png")
					|| ($filetype == "jpeg")
					|| ($filetype == "jpg")){
						if ($_FILES["file"]["error"][$i] > 0){
							if ($_FILES["file"]["error"][$i] == 1){
								baseclass::errMsg(language::readType('IMAGE_TO_BIG'));
							} else {
								form::errMsg("Return Code: ".$_FILES["file"]["error"][$i]);
							}
						} else {
							$path = "uploads/images";
							baseclass::checkfolder("uploads");
							baseclass::checkfolder("uploads/","images");
							baseclass::checkfolder("uploads/images/","small");
							baseclass::checkfolder("uploads/images/","medium");
							baseclass::checkfolder("uploads/images/","full");

							try {
								move_uploaded_file($_FILES["file"]["tmp_name"][$i],$path."/full/".$filename);
								$image = new imageresize($path."/full/".$filename);
								$image->setNewSize(120,90);
								$image->resizeImage('H');
								$image->saveImage($path."/small/".$filename);

								$image = new imageresize($path."/full/".$filename);
								$image->setNewSize(800,600);
								$image->resizeImage('H');
								$image->saveImage($path."/medium/".$filename);
							}
							catch (Exception $e) {
								baseclass::DBug($e);
							}
						}
					} else {
						baseclass::errMsg(language::readType('WRONG_IMAGE_TYPE'));
					}	
				}
			}
		}
		route::redirect('system/image/list');			
	}	
		
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		baseclass::rrmdir('uploads/images/full/'.$args[0]);
		baseclass::rrmdir('uploads/images/medium/'.$args[0]);
		baseclass::rrmdir('uploads/images/small/'.$args[0]);		
		route::redirect('system/image/list');	
	}	
}
?>