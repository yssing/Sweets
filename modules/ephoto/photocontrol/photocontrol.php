<?php	
require_once(PATH_SYS.'utils/imageresize.class.php');

class photocontrol {
	
	public static $selectorId = 0;
	
	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
	}
	
	public static function photoFinderAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}				
	}
	
	public static function listDatesAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}

		template::setValue('ephoto_folder_list',photo::listPhotoFolders('uploads/photo'));
		template::setValue('template',template::getTheme());
		$body = template::useBlock('dates');		

		template::initiate('photo_dates');
			template::body($body);
		template::end();
	}
	
	public static function listThumbnailsAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		$date = explode('/',$args['path']);		
		echo '<h4>'.$date[2].' - '.$date[3].'</h4>';
		
		if ($args['id']){
			echo views::displayEditListview(photo::listThumbsSelec($args['path'].'/small',$args['id'],$args['type'],5),'',0);			
		} else {
			echo views::displayEditListview(photo::listThumbs($args['path'].'/small'),'',0);			
		}		
	}
	
	public static function findThumbnailsAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		echo '<h4>'.$args['path'].'</h4>';
		echo views::displayEditListview(photo::findThumbs($args['path'],$args['id']),'',0);
	}
	
	public static function descriptionAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		echo self::renderDescription(photo::small($args['path']));
	}
	
	private static function renderDescription($url){
		list($id,$name,$description) = photo::readPhoto($url);
		if ($name){			
			$body = form::label('ephoto_lbl',$name);
		} else {
			$body = form::label('ephoto_lbl',$url);
		}
		$body .= form::input($url,'ephoto_path',HIDDEN);
		$body .= form::input($name,'ephoto_name',TEXT);
		$body .= form::textarea($description,'ephoto_text');
		$body .= '<p></p>';
		$body .= form::newButton('javascript:updateDescription()', 'Update');
		return $body;		
	}

	public static function listAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}

		$form = form::beginForm('upload','/modules/ephoto/photo/upload');	
			$form .= form::file('','file[]');
		$form .= form::endForm('upload');
		
		template::setValue('ephoto_list_dates', photo::listDates());
		template::setValue('ephoto_upload_form', $form);
		template::setValue('ephoto_description', '');
		template::setValue('ephoto_selectorId', '');
		template::setValue('ephoto_show_thumbnails', views::displayEditListview(photo::listThumbs('uploads/photo/'.date("Y").'/'.date("m").'/small',5),'listview',0));
		$body = template::useBlock('ephoto');
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}
	
	/**
	 * Used with a lighbox, when images needs to be selected.
	 */
	public static function selListAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if (isset($args[0])){
			self::$selectorId = $args[0];

			if(isset($args[1])){
				$selecType = $args[1];
			} else {
				$selecType = 'single';
			}

			template::setValue('ephoto_list_dates', photo::listDates());
			template::setValue('ephoto_upload_form', '');
			template::setValue('ephoto_description', '');
			template::setValue('ephoto_selectorId', $args[0]);
			template::setValue('ephoto_selectorType', $selecType);
			template::setValue('ephoto_show_thumbnails', views::displayEditListview(photo::listThumbsSelec('uploads/photo/'.date("Y").'/'.date("m").'/small',$args[0],$selecType,5),'listview',0));
			$body = template::useBlock('ephoto');
			
			echo $body;
		} else {
			echo 'Error!';
		}
	}
	
	/**
	 * finds information on an image.
	 * It uses the medium image, rather than the full image, since they can be big.
	 */
	public static function getImageInfoAction($args){
		$url = str_replace('/small/','/medium/',$args['path']);

		list($Width,$Height) = getimagesize($url);

		$data[0] = $url;
		$data[1] = $Width;
		$data[2] = $Height;
		
		echo json_encode($data);
	}
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Name" => "varchar(100)",
			"Description" => "text",
			"URL" => "varchar(200)");
		$result = $databaseadmin->createTable('ephoto',$what,"PK_PhotoID");
	}	

	public static function updateAction($args){	
		if (photo::updatePhotoInfo($args['path'], $args['name'], $args['description'])){
			echo self::renderDescription($args['path']);
		}
	}

	/**
	 * This takes care of uploading a range of images.
	 */
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
							form::checkfolder("uploads");
							form::checkfolder("uploads/","photo");
							form::checkfolder("uploads/photo/",date("Y"));
							form::checkfolder("uploads/photo/".date("Y")."/",date("m"));
							form::checkfolder("uploads/photo/".date("Y")."/".date("m")."/","small");
							form::checkfolder("uploads/photo/".date("Y")."/".date("m")."/","medium");
							form::checkfolder("uploads/photo/".date("Y")."/".date("m")."/","full");
							$path = "uploads/photo/".date("Y")."/".date("m")."";

							move_uploaded_file($_FILES["file"]["tmp_name"][$i],$path."/full/".$filename);
							try {
								$image = new imageresize($path."/full/".$filename);
								$image->setNewSize(120,90);
								$image->resizeImage('H');
								$image->saveImage($path."/small/".$filename);

								$image = new imageresize($path."/full/".$filename);
								$image->setNewSize(800,600);
								$image->resizeImage('H');
								$image->saveImage($path."/medium/".$filename);
								form::errMsg("Success!");
								photo::createPhoto($filename,'',$path."/small/".$filename);
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
		route::redirect('/modules/ephoto/photo/list');
	}
	
	/**
	 * Deletes an image.
	 * It removes both the small, medium and the full image
	 * It also removes the database entry.
	 * Once the image have been removed, it will then re-create
	 * the image view.
	 */
	public static function deleteAction($args){
		$path = $args['path'];
		$image = $args['image'];

		unlink($path."/".$image);

		unlink(photo::medium($path)."/".$image);
		unlink(photo::full($path)."/".$image);			
		photo::destroyPhoto(photo::small($path).'/'.$image);

		$date = explode('/',$args['path']);
		
		echo '<h4>'.$date[2].' - '.$date[3].'</h4>';
		echo views::displayEditListview(photo::listThumbs($args['path']),'',0);
	}
}
?>