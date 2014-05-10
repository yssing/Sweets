<?php
class modulecontrol{
	public static $modules = array();
	
	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		//self::listModules($_SERVER['DOCUMENT_ROOT'].'/');
		self::listModules('modules/');
		$body = views::displayEditListview(self::$modules);
		$body .= '<br />';
		$body .= form::beginForm('upload','/system/module/upload');		
			$body .= form::file('','file');
			$body .= form::submit('Upload','submit',1,array("style" => "margin-left:5px;"));
		$body .= form::endForm('upload',false);	
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$body = '<h3>Install '.$args[0].'</h3>';
		$body .= 'Will only install tables if they have not been installed before!';
		
		if(is_dir(PATH_MOD.$args[0])) {
			$folders = scandir(PATH_MOD.$args[0]);
			foreach($folders as $folder){								
				if($folder != '.' && $folder != '..'){
					if(strpos($folder, 'control')){
						include_once(PATH_MOD.$args[0].'/'.$folder.'/'.$folder.'.php');
						if(is_callable(array($folder,'installAction'))){
							$folder::installAction();
						}
					}
				}
			}
		}
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('INSTALL'));
			template::body($body);	
		template::end();		
	}
	
	public static function deleteAction($args){
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('INSTALL'));
			template::body('Has no action!');	
		template::end();		
	}
	
	public static function uploadAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}
		if(form::validate('upload')){
			$filename = $_FILES["file"]["name"];
			if(form::getFileExtension($_FILES["file"]["name"])){
				if ($_FILES["file"]["error"] == 0){
					move_uploaded_file($_FILES["file"]["tmp_name"],$filename);
					$zip = new ZipArchive;
					if ($zip->open($filename) === true) {
						$zip->extractTo(PATH_MOD);
						$zip->close();
						list($modulename) = explode('.',$filename); 
						if(is_dir(PATH_MOD.$modulename)) {
							$folders = scandir(PATH_MOD.$modulename);
							foreach($folders as $folder){								
								if($folder != '.' && $folder != '..'){
									if(strpos($folder, 'control')){
										include_once(PATH_MOD.$modulename.'/'.$folder.'/'.$folder.'.php');
										if(is_callable(array($folder,'installAction'))){
											$folder::installAction();
										}
									}
								}
							}
						}
					}
					unlink($filename);
				}
			}			
		}
		route::redirect('system/module/list');
	}	
	
	public static function listModules($dir){
		$header = '';
		$folders = scandir($dir);
		foreach($folders as $folder){
			if(	$folder != '.' && 
				$folder != '..' && 
				$folder != 'contributions' &&
				$folder != 'uploads' &&
				$folder != 'template' &&
				$folder != 'settings' &&
				$folder != 'scripts' &&
				$folder != 'plugins' &&
				$folder != 'cache' &&
				$folder != 'audio' &&
				$folder != 'common' ){
				if(is_dir($dir.'/'.$folder)) {		
					if(strpos($folder, 'control')){
						$tmpdir = str_replace($_SERVER['DOCUMENT_ROOT'],'',$dir);
						if($tmpdir != $header){
							$headline = explode('/',$tmpdir);
							self::$modules[] = array($headline[(sizeof($headline)-1)]);
						}
						$header = $tmpdir;						
					}
					self::listModules($dir.'/'.$folder);
				}
			}
		}
	}
}
?>