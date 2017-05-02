<?php

class structurecontrol{
	public static function listAction($path){
		if (!user::validateAdmin()){
			route::error(403);
		}	
		if (sizeof($path)){
			$pathlong = urldecode($path);
		} else {
			$pathlong = $_SERVER['DOCUMENT_ROOT'];		
		}

		//$list = array();
		//$list[] =  array("Zip","Zip");
		//$list[] =  array("Delete","Delete");		
		
		$body = form::beginForm('filelist',PATH_WEB.'/system/structure/actions');
			$body .= views::displayListview(files::folderAdminLister($pathlong),'',1,PAGING,'filelistview');
			$body .= form::input($pathlong,'path',HIDDEN);
			//$body .= form::submit(language::readType('DELFILES'),'submit',1);
			//$body .= form::select($list,0,'fileaction');	
		$body .= form::endForm('filelist',0);
		
		$body .= form::beginField('field1',language::readType('CREATE_NEW_FILE'));		
			$body .= form::beginForm('form','createfile/?path='.$pathlong);
			$body .= form::submit(language::readType('CREATE'),'submit',1);
			$body .= form::endForm('form',0);			
		$body .= form::endField();
		
		$body .= form::beginField('field1',language::readType('CREATE_NEW_FOLDER'));			
			$body .= form::beginForm('folder',PATH_WEB.'/system/structure/createfolder');		
				$inp = array("placeholder" => language::readType('FOLDERNAME'));
				$body .= form::inputControl('','newfolder','Name',$inp);
				$body .= form::input($pathlong,'path',HIDDEN);
				$body .= form::submit(language::readType('CREATEFOLDER'),'submit',1);
			$body .= form::endForm('folder',0);			
		$body .= form::endField();
		
		$body .= form::beginField('field1',language::readType('UPLOAD_FILE'));				
			$body .= form::beginForm('upload',PATH_WEB.'/system/structure/upload');
				$body .= form::file('','file');
				$body .= form::input($pathlong,'path',HIDDEN);
			$body .= form::endForm('upload');
		$body .= form::endField();
		
		template::initiate('form');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();	
	}

	public static function renameAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
	}
	
	private static function Zip($source, $destination, $include_dir = false){
		if (!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}

		if (file_exists($destination)) {
			unlink ($destination);
		}

		$zip = new ZipArchive();
		if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}
		$source = str_replace('\\', '/', realpath($source));

		if (is_dir($source) === true){

			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

			if ($include_dir) {
				$arr = explode("/",$source);
				$maindir = $arr[count($arr)- 1];

				$source = "";
				for ($i=0; $i < count($arr) - 1; $i++) { 
					$source .= '/' . $arr[$i];
				}

				$source = substr($source, 1);

				$zip->addEmptyDir($maindir);
			}

			foreach ($files as $file){
				$file = str_replace('\\', '/', $file);

				// Ignore "." and ".." folders
				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
					continue;

				//$file = realpath($file);

				if (is_dir($file) === true){
					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				} else if (is_file($file) === true) {
					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
				}
			}
		} else if (is_file($source) === true){
			$zip->addFromString(basename($source), file_get_contents($source));
		}
		return $zip->close();
	}	
	
	/**
	 * This action zip packs a file from the path given and creates a downloadable file call
	 */
	public static function downloadAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		form::checkfolder('uploads/admin');
		$filename = 'uploads/admin/'.$args['file'].'.zip';

		self::Zip($args['path'].'/'.$args['file'], $filename, true);
		
		header("Content-type: application/zip");
		header("Content-Disposition: attachment; filename=".$filename."");
		header("Content-length: " . filesize($filename));
		header("Pragma: no-cache"); 
		header("Expires: 0");
		readfile($filename);
		
		form::rrmdir($filename);
	}	

	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if (is_dir($args['path'])){
			self::listAction(urlencode($args['path']));
			return false;
		}

		$filename = explode('/',$args['path']);
		$filename = $filename[(sizeof($filename)-1)];
		$filetype = form::getFileExtension($args['path']);
		$value = file_get_contents($args['path']);
		$body = form::beginForm('Edit','/system/structure/update');
		$body .= form::fieldset('field1','<h3>'.language::readType('NAME').'</h3>',form::input($filename,'filename',0,array("style" => "width:920px;"))).'<br />';
		
		if ($filetype == 'jpg' || $filetype == 'gif' || $filetype == 'jpeg' || $filetype == 'png'){
			$path = str_replace('C:/www','',$args['path']);
			$body .= '<img src="'.$path.'" style="max-width:990px;" />';
		} else {
			$body .= form::textarea(trim($value),'value',array("style" => "width:940px;height:600px;")).'<br />';
			$body .= form::input($args['path'],'path',HIDDEN);	
			$body .= form::endForm('Edit');					
		}

		template::initiate('form');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();			
	}
	
	public static function createfileAction($args){
		$body = form::beginForm('Create',PATH_WEB.'/system/structure/createfilereceiver');
		$body .= form::label('lbl1',language::readType('NAME').form::input('','filename',0,array("style" => "width:890px;"))).'<br />';
		$body .= form::textarea('','value',array("style" => "width:940px;height:600px;")).'<br />';
		$body .= form::input($args['path'],'path',HIDDEN);	
		$body .= form::endForm('Create');					

		template::initiate('form');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();		
	}
	
	public static function createfolderAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}	
		if (form::validate('folder')){
			form::checkfolder($args['path'].'/'.$args['newfolder']);		
		}
		route::redirect('system/structure/edit/?path='.urlencode($args['path'].'/'.$args['newfolder']));
	}
	
	public static function createfilereceiverAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}	
		if (!$args['filename']){
			return false;
		}
		file_put_contents($args['path'].'/'.$args['filename'],$args['value']);
		route::redirect('system/structure/edit/?path='.urlencode($args['path'].'/'.$args['filename']));	
	}
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		file_put_contents($args['path'],$args['value']);
		route::redirect('system/structure/edit/?path='.urlencode($args['path']));
	}
	
	public static function uploadAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}			
		if (form::validate('upload')){
			$filename = $_FILES["file"]["name"];
			if (form::getFileExtension($_FILES["file"]["name"])){
				if ($_FILES["file"]["error"] > 0){
					if ($_FILES["file"]["error"] == 1){
						form::errMsg("Filen er muligvis for stor!");
					} else {
						form::errMsg("Return Code: ".$_FILES["file"]["error"]);
					}
				} else {
					move_uploaded_file($_FILES["file"]["tmp_name"],$args['path'].'/'.$filename);
				}
			}			
		}
		route::redirect('system/structure/edit/?path='.urlencode($args['path']));
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		if ($args['path'] && $args['file']){
			form::rrmdir($args['path'].'/'.$args['file']);		
		}
		route::redirect('system/structure/edit/?path='.urlencode($args['path']));
	}	
	
	public static function batchDeleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		if (form::validate('filelist')){
			foreach ($args as $key => $value) {	
				if ($value == 'on'){
					$file = $args['path'].'/'.form::decode($key);
					form::rrmdir($file);
				}
			}			
		}
		route::redirect('system/structure/edit/?path='.urlencode($args['path']));
	}
}
?>