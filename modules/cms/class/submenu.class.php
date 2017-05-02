<?php
/**
 * This class handles menu manipulation.
 *
 * Copyright (C) <2014> <Frederik Yssing>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   	CMS methods
 * @package		menu
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */
 
class submenu {
	/**
     * This method creates a new row in the menu table.
	 *
	 * @param string $headline The text to display.
	 * @param string $path The path.
	 * @param string $glyph do we use any of the defined icons?.
	 * @param int $priority
	 * @param int $submenuid
	 * @param int $brand
	 * @param int $active
	 * @param int $navheader
	 * @param int $divider
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function createMenuItem($headline, $menukey, $path, $glyph='-', $priority=0, $submenuid=0, $brand=0, $active=0, $navheader=0, $divider=0){
		//if ($headline && $path){
			$dbobject = new dbobject('cms_sub_menu');
			$dbobject->create('Headline',$headline);
			$dbobject->create('FK_MenuID',$menukey);
			$dbobject->create('Path',$path);
			$dbobject->create('Glyph',$glyph);
			$dbobject->create('Priority',$priority);
			$dbobject->create('FK_SubMenuID',$submenuid);
			$dbobject->create('Brand',$brand);
			$dbobject->create('Active',$active);
			$dbobject->create('NavHeader',$navheader);
			$dbobject->create('Divider',$divider);
			$dbobject->create('Language',language::get());	
			if ($dbobject->commit()){
				return $dbobject->readLastEntry();
			} 
		//}
		return false;
	}
	
	/**
     * This method finds all the data relating to a menu item.
	 *
	 * @param int $menudId the id to look up
	 *
	 * @return array on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function readMenuItem($menuId = 0,$subMenuId = 0){
		$dbobject = new dbobject('cms_sub_menu');
		$dbobject->join("cms_menu", "PK_MenuID", "FK_MenuID");
		
		$dbobject->read("PK_SubMenuID");
		$dbobject->read("Headline");
		$dbobject->read("Path");
		$dbobject->read("Glyph");
		$dbobject->read("Priority");
		$dbobject->read("FK_SubMenuID");
		$dbobject->read("Brand");
		$dbobject->read("Active");
		$dbobject->read("NavHeader");
		$dbobject->read("Divider");
		$dbobject->read("FK_MenuID");
		$dbobject->where("PK_SubMenuID",$menuId);
		if ($subMenuId){
			$dbobject->where("FK_SubMenuID",$subMenuId);
		}
		$dbobject->where("Language",language::get());
		$dbobject->orderby("Path");
		return $dbobject->fetchSingle();			
	}	

	/**
     * This method finds all the data relating to a menu item.
	 *
	 * @param int $subMenuId if we need to look for a specific submenu
	 *
	 * @return array on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	private static function buildMenu($subMenuId = 0, $menukey = 'MENU'){
		$dbobject = new dbobject('cms_sub_menu');
		$dbobject->join("cms_menu", "PK_MenuID", "FK_MenuID");
		
		$dbobject->read("PK_SubMenuID");
		$dbobject->read("Headline");
		$dbobject->read("Path");
		$dbobject->read("Glyph");
		$dbobject->read("Priority");
		$dbobject->read("FK_SubMenuID");
		$dbobject->read("Brand");
		$dbobject->read("Active");
		$dbobject->read("NavHeader");
		$dbobject->read("Divider");
		//if ($subMenuId){
			$dbobject->where("FK_SubMenuID",$subMenuId);
		//}
		$dbobject->where("cms_menu.MenuKey",$menukey);
		$dbobject->where("Language",language::get());
		$dbobject->orderby("Brand","DESC");
		$dbobject->orderby("FK_SubMenuID");
		$dbobject->orderby("Priority");		
		return $dbobject->fetch();
	}
	
	/**
     * This method finds all menu items.
	 *
	 * @return array on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listAllItems(){
		$dbobject = new dbobject('cms_sub_menu');
		$dbobject->read("PK_SubMenuID");
		$dbobject->read("Headline");
		$dbobject->where("Language",language::get());
		return $dbobject->fetch();
	}
	
	/**
     * This method finds all the data relating to a menu item.
	 *
	 * @param String $searchval If any wildcard search is done.	 
	 *
	 * @return array on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listMenu($searchval = ''){
		$dbobject = new dbobject('cms_sub_menu');
		$dbobject->join("cms_menu", "PK_MenuID", "FK_MenuID");
		
		$dbobject->read("PK_SubMenuID");
		$dbobject->read("FK_SubMenuID");
		$dbobject->read("MenuKey");
		$dbobject->read("Headline");
		
		$dbobject->read("Glyph");
		$dbobject->read("Priority");
		$dbobject->read("Brand");
		$dbobject->read("Active");
		$dbobject->read("NavHeader");
		$dbobject->read("Divider");		
		if ($searchval){
			$dbobject->wildcard("Headline",$searchval);
		}		
		$dbobject->where("Language",language::get());
		$dbobject->orderby("Brand","DESC");
		$dbobject->orderby("FK_SubMenuID");
		$dbobject->orderby("Priority");
		return $dbobject->fetch();		
	}

	/**
     * This method creates submenus.
	 * This method makes use of bootstrap styling
	 *
	 * @param int $submenu the id of the submenu items
	 *
	 * @return array on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function makeSubMenu($submenu, $menukey){
		$menuArray = self::buildMenu($submenu, $menukey);		
		$menu = '';
		if (!$menuArray){
			return false;
		}
		
		foreach($menuArray as $singleLine){
			
			$menu .= self::makeSubMenu($singleLine[0], $menukey);
			if ($singleLine[9] == 1){
				$menu .= '<li class="divider"></li>';
			} else {
				if ($singleLine[7] == 1){
					$active = 'active';
				} else {
					$active = '';
				}
				$SEOHeader = text::SEOHeader($singleLine[1]);
				if ($singleLine[8] == 1){
					$menu .= '<li class="dropdown-header">'.$singleLine[1].'</li>';
				} else {
					if ($singleLine[3]){
						$menu .= '<li class="'.$active.'"><a href="'.$singleLine[2].$SEOHeader.'"><span class="glyphicon glyph'.$singleLine[3].' menu_icon"></span>'.$singleLine[1].'</a></li>';
					} else {
						$menu .= '<li class="'.$active.'"><a href="'.$singleLine[2].$SEOHeader.'">'.$singleLine[1].'</a></li>';
					}
				}
			}
		}
		if ($menu){
			$menu = '<ul class="dropdown-menu">'.$menu.'</ul>';
		}
		return $menu;
	}	
	
	/**
     * This method creates the menu, it builds the menu using <ul> and bootstrap.
	 * it will also include language flags.
	 * This method makes use of bootstrap styling
	 * It uses the $menukey variable to control both the template and the MasterMenu!
	 *
	 * @param int $menu Specify the master menu here.
	 *
	 * @return string the menu.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function makeMenu($menukey = 'MENU'){
		$menuArray = self::buildMenu(0, $menukey);
		$brand = '';
		$menuList = '<ul class="nav navbar-nav">';		
		
		if (is_array($menuArray)){
			foreach($menuArray as $singleLine){		
				
				if ($singleLine[6] == 1){
					$brand = '<a class="navbar-brand" href="'.$singleLine[2].'">'.$singleLine[1].'</a>';
				} else {
					if ($singleLine[7] == 1){
						$active = 'active';
					} else {
						$active = '';
					}
					$subMenuList = self::makeSubMenu($singleLine[0], $menukey);
					$SEOHeader = text::SEOHeader($singleLine[1]);
					if ($subMenuList){
						$menuList .= '<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$singleLine[1].' <b class="caret"></b></a>'.$subMenuList.'</li>';
					} else {
						if (strlen($singleLine[3]) > 2){
							$menuList .= '<li class="'.$active.'"><a href="'.$singleLine[2].$SEOHeader.'"><span class="glyphicon '.$singleLine[3].' menu_icon"></span>'.$singleLine[1].'</a></li>';
						} else {
							$menuList .= '<li class="'.$active.'"><a href="'.$singleLine[2].$SEOHeader.'">'.$singleLine[1].'</a></li>';
						}
					}
				}
			}
		}
		if(language::countLanguages() > 1){
			$menuList .= '<li class="dropdown">'.language::listFlags().'</li>';
		}
		$menuList .= '</ul>';

		template::setValue('brand',$brand);
		template::setValue('menulist',$menuList);		

		return template::useBlock(strtolower($menukey));
	}

	/**
     * This method updates a menu with all the current parameters.
	 *
	 * @param int $menuid the sub menu id, not the master menu!!
	 * @param string $headline The text to display.
	 * @param int $menukey The master menu id.
	 * @param string $path The path.
	 * @param string $glyph do we use any of the defined icons?.
	 * @param int $priority
	 * @param int $submenuid
	 * @param int $brand
	 * @param int $active
	 * @param int $navheader
	 * @param int $divider
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function updateMenuItem($menuId, $headline, $menukey, $path, $glyph='-', $priority=0, $submenuid=0, $brand=0, $active=0, $navheader=0, $divider=0){		
		$dbobject = new dbobject('cms_sub_menu');
		$dbobject->update('Headline',$headline);
		$dbobject->update('Path',$path);
		$dbobject->update('FK_MenuID',$menukey);
		$dbobject->update('Glyph',$glyph);
		$dbobject->update('Priority',$priority);
		$dbobject->update('FK_SubMenuID',$submenuid);
		$dbobject->update('Brand',$brand);
		$dbobject->update('Active',$active);
		$dbobject->update('NavHeader',$navheader);
		$dbobject->update('Divider',$divider);
		$dbobject->update('Language',language::get());
		$dbobject->where("PK_SubMenuID",$menuId);
		return $dbobject->commit();		
	}
	
	/**
     * This method deletes a submenu entry in the database.
	 *
	 * @param int $SubMenuID The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyMenuItem($SubMenuID){
		$dbobject = new dbobject('cms_sub_menu');
		$dbobject->destroy();
		$dbobject->where("PK_SubMenuID",$SubMenuID);
		return $dbobject->commit();		
	}
}
?>