<?php

class languagecontrol {
	public static function indexAction(){	
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = views::displayEditListview(language::listLanguage());
		$body .= form::newButton();			
		
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
		
		if(isset($args[0]) && $args[0] > 0){
			list($id,$key,$string,$bodytext) = language::readLanguage($args[0]);
		} else {
			$id = '';
			$key = '';
			$string = '';
			$bodytext = '';
		}
		$body = form::beginForm('update',PATH_WEB.'/system/language/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('KEY').'</h3>',form::input($key,'key',0)).'<br />';
			$body .= form::fieldset('field1','<h3>'.language::readType('LANGUAGE').'</h3>',form::input($string,'string',0)).'<br />';
			$body .= form::fieldset('field1','<h3>'.language::readType('TEXT').'</h3>',form::input($bodytext,'body',0)).'<br />';
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');	
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();			
	}
		
	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		if($args['id'] > 0){
			language::updateLanguage($args['id'],$args['key'],$args['string'],$args['body']);		
		} else{
			if(language::createLanguage($args['key'],$args['string'],$args['body'])){
				$id = language::findlast();
			}
		}
		route::redirect('system/language/list');
	}

	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Type" => "varchar(45)", "String" => "Text");
		$result = $databaseadmin->createTable('generic_language',$what,"PK_LanguageID");
	}
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}		
		language::destroyLanguage($args[0]);
		route::redirect('system/language/list');
	}
}
?>