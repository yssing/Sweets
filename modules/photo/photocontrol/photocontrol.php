<?php
include_once('contributions/resize.class.php');	
class photocontrol {
	public static function indexAction($args){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
	}
	
	public static function listAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		
		$body = views::displayListview(files::folderLister('photo/',false),'').'<br />';
		$body .= form::beginForm('photo',PATH_WEB.'/photo/photo/upload');	
			$body .= form::file('','file');
		$body .= form::endForm('photo');
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	
	}
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Name" => "varchar(100)",
			"Description" => "text",
			"URL" => "varchar(200)");
		$result = $databaseadmin->createTable('photo',$what,"PK_PhotoID");
	}		

	public static function uploadAction($args){
		if(form::validate('photo')){		
			$filename = $_FILES["file"]["name"];				
			$filetype = form::getFileExtension($_FILES["file"]["name"]);
			if($filetype){
				if (($filetype == "gif")
				|| ($filetype == "png")
				|| ($filetype == "jpeg")
				|| ($filetype == "jpg")){
					if ($_FILES["file"]["error"] > 0){
						if($_FILES["file"]["error"] == 1){
							form::errMsg("Billedet er muligvis for stort, prøv at gøre det mindre");
						} else {
							form::errMsg("Return Code: ".$_FILES["file"]["error"]);
						}
					} else {
						form::checkfolder("","photo");
						form::checkfolder("photo/",date("Y"));					
						form::checkfolder("photo/".date("Y")."/",date("m"));						
						form::checkfolder("photo/".date("Y")."/".date("m")."/","small");
						form::checkfolder("photo/".date("Y")."/".date("m")."/","medium");
						form::checkfolder("photo/".date("Y")."/".date("m")."/","full");		
						$path = "photo/".date("Y")."/".date("m")."/";

						move_uploaded_file($_FILES["file"]["tmp_name"],$path."/full/".$filename);
						try {
							$obj = new Resize($path."/full/".$filename);
							$obj->setNewImage($path."/small/".$filename);
							$obj->setProportional(1);
							$obj->setDegrees(0);
							$obj->setNewSize(120,100);
							$obj->make();

							$obj = new Resize($path."/full/".$filename);
							$obj->setNewImage($path."/medium/".$filename);
							$obj->setProportionalFlag('H');
							$obj->setProportional(1);
							$obj->setDegrees(0);
							$obj->setNewSize(800,600);
							$obj->make();
							form::errMsg("Success!");
							photo::createPhoto($filename,'',$path."/full/".$filename);
						}
						catch (Exception $e) {
							self::DBug($e);
						}
					}
				} else {
					form::errMsg("Forkert filtype, kun png, gif og jpg kan bruges.");
				}	
			}
			route::redirect('photo/photo/list');
		}
	}
	
	public static function deleteAction($args){
	
	}
}
?>