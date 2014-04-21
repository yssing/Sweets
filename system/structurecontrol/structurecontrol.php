<?php

class structurecontrol{
	public static function listAction($path){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		if(sizeof($path)){
			$pathlong = urldecode($path);
		} else {
			$pathlong = $_SERVER['DOCUMENT_ROOT'];		
		}

		$body = form::beginField('field1',language::readType('FILEFOLDERACTIONS'));
			$body .= form::beginForm('filelist',PATH_WEB.'/system/structure/delete');
				$body .= views::displayListview(files::folderLister($pathlong));
				$body .= '<br />';
				$body .= form::input($pathlong,'path',HIDDEN);
				$body .= form::submit(language::readType('DELFILES'),'submit',1);
			$body .= form::endForm('filelist',0);
			
			$body .= form::newButton('createfile/?path='.$pathlong);
			$body .= '<br /><br />';
			$body .= form::beginForm('folder',PATH_WEB.'/system/structure/createfolder');		
				$inp = array("onClick" => "removetext(this.id)");
				$body .= form::input(language::readType('FOLDERNAME'),'newfolder','text',$inp);
				$body .= form::input($pathlong,'path',HIDDEN);
				$body .= form::submit(language::readType('CREATEFOLDER'),'submit',1);
			$body .= form::endForm('folder',0);	
			
			$body .= '<br />';
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

	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		if(is_dir($args['path'])){
			self::listAction(urlencode($args['path']));
			return false;
		}

		$filename = explode('/',$args['path']);
		$filename = $filename[(sizeof($filename)-1)];
		$filetype = form::getFileExtension($args['path']);
		$value = file_get_contents($args['path']);
		$body = form::beginForm('Edit',PATH_WEB.'/system/structure/update');
		$body .= form::fieldset('field1','<h3>'.language::readType('NAME').'</h3>',form::input($filename,'filename',0,array("style" => "width:920px;"))).'<br />';
		
		if($filetype == 'jpg' || $filetype == 'gif' || $filetype == 'jpeg' || $filetype == 'png'){
			$path = str_replace('www/','',$args['path']);
			$body = '<img src="'.PATH_WEB.'/'.$path.'" style="max-width:990px;" />';
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
		$body .= form::textarea(trim($value),'value',array("style" => "width:940px;height:600px;")).'<br />';
		$body .= form::input($args['path'],'path',HIDDEN);	
		$body .= form::endForm('Create');					

		template::initiate('form');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();		
	}
	
	public static function createfolderAction($args){	
		if(!user::validateAdmin()){
			route::error(403);
		}	
		if(form::validate('folder')){
			form::checkfolder($args['path'].'/'.$args['newfolder']);		
		}
		route::redirect('system/structure/edit/?path='.urlencode($args['path'].'/'.$args['newfolder']));
	}
	
	public static function createfilereceiverAction($args){	
		if(!user::validateAdmin()){
			route::error(403);
		}	
		if(!$args['filename']){
			return false;
		}
		file_put_contents($args['path'].'/'.$args['filename'],$args['value']);
		route::redirect('system/structure/edit/?path='.urlencode($args['path'].'/'.$args['filename']));	
	}
	
	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}		
		file_put_contents($args['path'],$args['value']);
		route::redirect('system/structure/edit/?path='.urlencode($args['path']));
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
		if(!user::validateAdmin()){
			route::error(403);
		}		
		if(form::validate('filelist')){
			foreach ($args as $key => $value) {	
				if($value == 'on'){
					$file = $args['path'].'/'.form::decode($key);
					form::rrmdir($file);
				}
			}			
		}
		route::redirect('system/structure/edit/?path='.urlencode($args['path']));
	}
}
?>