<?php
class settingscontrol {
	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = views::displayEditListview(key::listKeys());
		$body .= form::newButton();
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();		
	}
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error('403');
		}
		list($id,$key,$value) = key::readKey($args[0]);
		
		$body = form::beginForm('settings',PATH_WEB.'/system/settings/update');	
			$body .= form::fieldset('field1','<h3>'.language::readType('NAME').'</h3>',form::input($key,'key',0,array("style" => "width:920px;"))).'<br />';		
			$body .= form::fieldset('field2','<h3>'.language::readType('VALUE').'</h3>',form::textarea($value,'value')).'<br />';
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('settings');	
				
		template::initiate('form');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}	
		
	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}		
		if(key::doesExist($args['key'])){
			key::updateKey($args['id'],$args['key'],$args['value']);
			route::redirect('system/settings/edit/'.$args['id']);
			return true;
		} else {
			if(key::createKey($args['key'],$args['value'])){
				route::redirect('system/settings/edit/'.key::findlast());
				return true;
			}
		}		
		route::redirect('system/settings/list');		
	} 
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$database = new database();
		$what = array("KeySetting" => "varchar(45)", "ValueSetting" => "text");
		$result = $database->createTable('generic_key',$what,"PK_KeyID");
	}	
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		key::destroyKey($args[0]);
		route::redirect('system/settings/list');		
	}
}
?>