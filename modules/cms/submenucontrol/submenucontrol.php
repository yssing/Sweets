<?php
class submenucontrol{
	public static function indexAction(){
		route::error(403);
	}

	public static function listAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}	
		$body = views::displayEditListview(submenu::listMenu($searchVal));

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}

	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		include_once('common/lists/iconlist.php');
		$glyphs = array();
		if (is_array($glyphlist)){
			foreach($glyphlist as $single){
				$glyphs[] = array($single,$single);
			}
		}
		
		if (isset($args[0])){
			list($id,$headline,$link,$glyph,$priority,$submenuid,$brand,$active,$navheader,$divider,$menukey) = submenu::readMenuItem($args[0]);
		} else {
			$id = $headline = $link = $glyph = $priority = $submenuid = $brand = $active = $navheader = $divider = $menukey = '';			
		}
		
		$linklist = array();
		$textarray = text::listText();
		if (is_array($textarray)){
			foreach($textarray as $line){
				$linklist[] = array('/modules/cms/text/'.$line[0],$line[2]);
			}
		}

		$body = form::beginForm('update','/modules/cms/submenu/update');
			$style = array("style" => "width:320px;border:0px;");
			$input = array("style" => "width:300px");
			$body .= form::fieldset('field3','<h3>'.language::readType('MENU_KEY').'</h3>',form::select(menu::listMenu(),$menukey,'menukey'),$style);
			$body .= form::fieldset('field1','<h3>'.language::readType('HEADLINE').'</h3>',form::input($headline,'headline',TEXT,$input),$style);
			$body .= form::fieldset('field2','<h3>'.language::readType('PATH').'</h3>',form::select($linklist,$link,'link'),$style);
			$body .= form::fieldset('field2','<h3>'.language::readType('PATHALT').'</h3>',form::input('','alt-path',TEXT,$input),$style);
			$body .= form::fieldset('field3','<h3>'.language::readType('GLYPH').'</h3>',form::select($glyphs,$glyph,'glyphs'),$style);
			$body .= form::fieldset('field4','<h3>'.language::readType('PRIORITY').'</h3>',form::input($priority,'priority',TEXT,$input),$style);
			$body .= form::fieldset('field5','<h3>'.language::readType('SUBMENU').'</h3>',form::select(submenu::listAllItems(),$submenuid,'submenuid'),$style);
			
			$body .= form::fieldset('field6','<h3>'.language::readType('HIGHLIGHTED').'</h3>',form::check($brand,'brand'),$style).'<br />';
			$body .= form::fieldset('field7','<h3>'.language::readType('ACTIVE').'</h3>',form::check($active,'active'),$style).'<br />';
			$body .= form::fieldset('field8','<h3>'.language::readType('SUBMENUHEAD').'</h3>',form::check($navheader,'navheader'),$style).'<br />';
			$body .= form::fieldset('field9','<h3>'.language::readType('SEPARATOR').'</h3>',form::check($divider,'divider'),$style).'<br />';
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
		$brand 		= isset($args['brand']) ? 1:0;
		$active 	= isset($args['active']) ? 1:0;
		$navheader 	= isset($args['navheader']) ? 1:0;
		$divider 	= isset($args['divider']) ? 1:0;
		$submenuid 	= ($args['submenuid']) ? $args['submenuid']:0;
		$priority 	= ($args['priority']) ? $args['priority']:0;
		$link 		= ($args['alt-path']) ? $args['alt-path'] : $args['link'];
		if (form::validate('update')){
			if ($args['id']){
				submenu::updateMenuItem($args['id'],$args['headline'],$args['menukey'],$link,$args['glyphs'],$priority,$submenuid,$brand,$active,$navheader,$divider);
			} else{
				submenu::createMenuItem($args['headline'],$args['menukey'],$link,$args['glyphs'],$priority,$submenuid,$brand,$active,$navheader,$divider);
			}
			route::redirect('/modules/cms/submenu/list');
		}
	}	

	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Headline" => "varchar(200)",
					"Path" => "varchar(200)",
					"Priority" => "int(10)",
					"FK_SubMenuID" => "int(10)",
					"FK_MenuID" => "int(10)",
					"Brand" => "tinyint(1)",
					"Active" => "tinyint(1)",
					"NavHeader" => "tinyint(1)",
					"Divider" => "tinyint(1)",
					"Glyph" => "tinyint(1)");
		$result = $databaseadmin->createTable('cms_sub_menu',$what,"PK_SubMenuID");
	}

	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		submenu::destroyMenuItem($args[0]);
		route::redirect('cms/submenu/list');
	}
}
?>