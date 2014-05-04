<?php

class articlecontrol {

	public static function indexAction($args){	
		if($args){
			list($id,$key,$headline,$bodytext) = text::readText($args[0]);
			// if the article is not found, we throw a 404
			if(!$id){
				route::error(404);
			}		
		} else {
			route::error(404);
		}		
		template::initiate('main');
			template::header($headline);
			template::body($bodytext);
			template::title(element::readElementPath(route::$url,'[TITLE]',TITLE));
			template::footer(element::readElementPath(route::$url,'[FOOTER]',USERFOOTER));			
			template::replace('[COPY]',element::readElementPath(route::$url,'[COPY]',COPYFOOTER));
			template::replace('[MENU]',menu::makeMenu());				
		template::end();
	}

	public static function listAction($args){	
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = '<div id="edit"></div>';		
		$body .= views::displayEditListview(text::listText());
		$body .= form::newButton();		
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();	
	}
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		text::destroyText($args[0]);
		route::redirect('cms/article/list');
	}
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$database = new database();
		$what = array("Headline" => "varchar(100)",
			"BodyText" => "text",
			"TextKey" => "varchar(45)",
			"DateOnline" => "datetime",
			"DateOffline" => "datetime");
		$result = $database->createTable('cms_text',$what,"PK_TextID");
	}		
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error('403');
		}
		if(isset($args[0]) && $args[0]){
			list($id,$key,$headline,$bodytext,$language) = text::readText($args[0]);
		} else {
			$id = 0;
			$key = '';
			$headline = '';
			$bodytext = '';
			$language = $_SESSION['CountryCode'];
		}
		$fieldset = array("style" => "width:890px;");
		$body = form::beginForm('update',PATH_WEB.'/cms/article/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('KEY').'</h3>',form::input($key,'key',TEXT)).'<br />';
			$body .= form::fieldset('field2','<h3>'.language::readType('LANGUAGE').'</h3>',form::input($language,'language',TEXT)).'<br />';
			$body .= form::fieldset('field3','<h3>'.language::readType('HEADLINE').'</h3>',form::input($headline,'headline',TEXT,$fieldset)).'<br />';
			$body .= form::fieldset('field4','<h3>'.language::readType('TEXT').'</h3>',form::textarea($bodytext,'bodytext',array("style" => "width:900px;height:620px;"))).'<br />';
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');		
				
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}
	
	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		if(form::validate('update')){
			if($args['id']){
				list($id,$key,$headline,$bodytext,$language) = text::readText($args['id']);
				textrevision::createRevision($headline,$bodytext,$key,$language,$id);				
				text::updateText($args['id'],$args['key'],$args['headline'],$args['bodytext'],$args['language']);
			} else {			
				text::createText($args['headline'],$args['bodytext'],$args['key'],$args['language']);
			}
			route::redirect('cms/article/list');
		}
	}	
}
?>