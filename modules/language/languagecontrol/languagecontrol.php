<?php

class languagecontrol {
	public static function indexAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}
		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}
		$body = views::displayEditListview(language::listLanguage($searchVal));

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
		if (isset($args[0])){
			list($id,$key,$string,$bodytext) = language::readLanguage($args[0]);
		} else {
			$id = '';
			$key = '';
			$string = '';
			$bodytext = '';
		}
		$body = form::beginForm('update','modules/language/language/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('KEY').'</h3>',form::input($key,'key',TEXT)).'<br />';
			$body .= form::fieldset('field2','<h3>'.language::readType('LANGUAGE').'</h3>',form::input($string,'language',TEXT)).'<br />';
			$body .= form::fieldset('field3','<h3>'.language::readType('TEXT').'</h3>',form::textarea($bodytext,'value')).'<br />';			
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');	
		
		template::initiate('form');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();			
	}
		
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}	
		if ($args['id'] > 0){
			language::updateLanguage($args['id'],$args['key'],$args['language'],$args['value']);		
		} else{
			language::createLanguage($args['key'],$args['language'],$args['value']);
		}
		route::redirect('modules/language/language/list');
	}

	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Type" => "varchar(45)", 
					"String" => "Text");
		$result = $databaseadmin->createTable('generic_language',$what,"PK_LanguageID");
		$databaseadmin->importSQLfile('modules/language/sql/');
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		language::destroyLanguage($args[0]);
		route::redirect('modules/language/language/list');
	}
}
?>