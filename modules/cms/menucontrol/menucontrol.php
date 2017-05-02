<?php

class menucontrol{
	
	public static function indexAction(){
		route::error(403);
	}

	public static function listAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$body = views::displayEditListview(menu::listMenu());

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();	
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		if (isset($args[0])){
			list($id,$menuname,$menukey) = menu::readMenu($args[0]);
		} else {
			$id = $menuname = $menukey = '';			
		}

		$body = form::beginForm('update','modules/cms/menu/update');

			$body .= form::fieldset('field1',language::readType('NAME'),form::input($menuname,'menuname',TEXT));
			$body .= form::fieldset('field2',language::readType('KEY'),form::input($menukey,'menukey',TEXT));

			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');

		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));	
			template::body($body);
		template::end();
	}	

	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		if (form::validate('update')){
			if ($args['id']){
				menu::updateMenu($args['id'],$args['menuname'],$args['menukey']);
			} else{
				menu::createMenu($args['menuname'],$args['menukey']);
			}
			route::redirect('cms/menu/list');
		}
	}	
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		
		$databaseadmin = new databaseadmin();
		$what = array("MenuName" => "varchar(200)", 
					  "MenuKey" => "varchar(200)");		
		$result = $databaseadmin->createTable('cms_menu',$what,"PK_MenuID");
		if ($result){
			menu::createMenu('The main menu, must be present!','MENU');
		}
	}	

	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		menu::destroyMenu($args[0]);
		route::redirect('cms/menu/list');
	}	
}
?>