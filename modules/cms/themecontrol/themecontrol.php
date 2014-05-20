<?php
class themecontrol{
	public static function indexAction(){
		route::error(403);	
	}
	
	public static function listAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		$body = '<h3>'.language::readType('SELECTED').': '.theme::readTheme().'</h3><br />';
		$body .= views::displayListview(theme::listerTheme(),'');	
		$body .= '<br /><br />';		
		$body .= form::beginForm('upload','modules/cms/theme/upload');		
			$input = array("style" => "width:350px;");
			$body .= form::file('','file',$input).'<br />';
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
				if ($_FILES["file"]["error"] == 0){
					move_uploaded_file($_FILES["file"]["tmp_name"],'template/'.$filename);
					$zip = new ZipArchive;
					if ($zip->open('template/'.$filename) === TRUE) {
						$zip->extractTo('template/');
						$zip->close();
					}
					unlink('template/'.$filename);
				}
			}			
		}
		route::redirect('cms/theme/list');		
	}
	
	public static function activateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	

		theme::enableTheme($args[0]);
		route::redirect('cms/theme/list');
	}	

	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}

		if(form::rrmdir('template/'.$args[0])){
			theme::enableTheme('default');
		}
		route::redirect('cms/theme/list');
	}	
}
?>