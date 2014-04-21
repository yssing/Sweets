<?php
class filecontrol{
	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = views::displayEditListview(files::fileLister('uploads/files',6)).'<br />';		
		$body .= form::beginForm('upload',PATH_WEB.'/system/file/upload');	
		$body .= form::file('','file').'<br />';
		$body .= form::endForm('upload');
		
		template::initiate('admin');
			template::noCache();
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
			if(form::getFileExtension($_FILES["file"]["name"])){
				if ($_FILES["file"]["error"] > 0){
					if($_FILES["file"]["error"] == 1){
						form::errMsg(language::readType('FILETOBIG'));
					} else {
						form::errMsg("Return Code: ".$_FILES["file"]["error"]);
					}
				} else {
					$path = "uploads";
					form::checkfolder("","uploads");
					form::checkfolder("uploads/","files");
					move_uploaded_file($_FILES["file"]["tmp_name"],$path."/files/".$filename);
				}
			}			
		}
		route::redirect('system/file/list');		
	}	
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}		
		form::rrmdir('uploads/files/'.$args[0]);
		route::redirect('system/file/list');	
	}
}

?>