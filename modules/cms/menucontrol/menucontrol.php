<?php
class menucontrol{
	public static function indexAction(){
		route::error(403);
	}

	public static function listAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$body = views::displayEditListview(menu::listMenu());
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
		include_once('common/lists/iconlist.php');
		$glyphs = array();
		if(is_array($glyphlist)){
			foreach($glyphlist as $single){
				$glyphs[] = array($single,$single);									
			}
		}
		
		if($args[0]){
			list($id,$headline,$link,$glyph,$priority,$submenuid,$brand,$active,$navheader,$divider) = menu::readMenuItem($args[0]);
		}
		
		$linklist = array();
		$textarray = text::listText();
		if(is_array($textarray)){
			foreach($textarray as $line){
				$linklist[] = array('/cms/article/'.$line[0],$line[2]);									
			}
		}

		$body = form::beginForm('update','modules/cms/menu/update');
			$style = array("style" => "width:320px;border:0px;");
			$input = array("style" => "width:300px");		
			$body .= form::fieldset('field1','<h3>'.language::readType('HEADLINE').'</h3>',form::input($headline,'headline',TEXT,$input),$style);
			$body .= form::fieldset('field2','<h3>'.language::readType('PATH').'</h3>',form::select($linklist,$link,'link'),$style);
			$body .= form::fieldset('field3','<h3>'.language::readType('GLYPH').'</h3>',form::select($glyphs,$glyph,'glyphs'),$style);
			$body .= form::fieldset('field4','<h3>'.language::readType('PRIORITY').'</h3>',form::input($priority,'priority',TEXT,$input),$style);
			$body .= form::fieldset('field5','<h3>'.language::readType('SUBMENU').'</h3>',form::select(menu::listAllItems(),$submenuid,'submenuid'),$style);
			
			$body .= form::fieldset('field6','<h3>'.language::readType('HIGHLIGHTED').'</h3>',form::check($brand,'brand'),$style).'<br />';
			$body .= form::fieldset('field7','<h3>'.language::readType('ACTIVE').'</h3>',form::check($active,'active'),$style).'<br />';
			$body .= form::fieldset('field8','<h3>'.language::readType('SUBMENUHEAD').'</h3>',form::check($navheader,'navheader'),$style).'<br />';
			$body .= form::fieldset('field9','<h3>'.language::readType('SEPARATOR').'</h3>',form::check($divider,'divider'),$style).'<br />';
			$body .= form::input($args[0],'id',2);
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
		
		$brand = ($args['brand']) ? 1:0;
		$active = ($args['active']) ? 1:0;
		$navheader = ($args['navheader']) ? 1:0;
		$divider = ($args['divider']) ? 1:0;
		$submenuid = ($args['submenuid']) ? $args['submenuid']:0;
		$priority = ($args['priority']) ? $args['priority']:0;
		
		if(form::validate('update')){			
			if($args['id']){
				menu::updateMenuItem($args['id'],$args['headline'],$args['link'],$args['glyphs'],$priority,$submenuid,$brand,$active,$navheader,$divider);
			} else{
				menu::createMenuItem($args['headline'],$args['link'],$args['glyphs'],$priority,$submenuid,$brand,$active,$navheader,$divider);
			}
			route::redirect('cms/menu/list');
		}
	}	
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Headline" => "varchar(200)",
					"Path" => "varchar(200)",
					"Priority" => "int(10)",
					"FK_MenuID" => "int(10)",
					"Brand" => "tinyint(1)",
					"Active" => "tinyint(1)",
					"NavHeader" => "tinyint(1)",
					"Divider" => "tinyint(1)",
					"Glyph" => "tinyint(1)");
		$result = $databaseadmin->createTable('cms_menu',$what,"PK_MenuID");	
	}		

	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}

		menu::destroyMenuItem($args[0]);
		route::redirect('cms/menu/list');
	}
}
?>