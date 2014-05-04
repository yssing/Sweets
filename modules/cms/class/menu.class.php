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
 
class menu {
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
	public static function createMenuItem($headline, $path, $glyph='-', $priority=0, $submenuid=0, $brand=0, $active=0, $navheader=0, $divider=0){
		$database = new database();
		if($headline && $path){
			$data = array("Headline" => "'".$headline."'",
				"Path" => "'".$path."'",
				"Glyph" => "'".$glyph."'",
				"Priority" => $priority,
				"FK_MenuID" => $submenuid,
				"Brand" => $brand,
				"Active" => $active,
				"NavHeader" => $navheader,
				"Divider" => $divider,
				"Language" => "'".$_SESSION['CountryCode']."'");
			if(!$database->create('cms_menu',$data)){
				return false;
			}
		}
		return false;
	}
	
	/**
     * This method finds the newest entry in the menu table.
	 *
	 * @return int/bool id on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function findlast(){
		$database = new database();
		list($id) = $database->readLastEntry('cms_menu');
		return $id;
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
	public static function readMenuItem($menuId){
		$database = new database();
		return $database->readSingle("cms_menu","PK_MenuID,Headline,Path,Glyph,Priority,FK_MenuID,Brand,Active,NavHeader,Divider","PK_MenuID = ".$menuId." AND Language = '".$_SESSION['CountryCode']."'");
	}	

	/**
     * This method finds all the data relating to a menu item.
	 *
	 * @param int $menudId the id to look up
	 * @param int $subMenuId if we need to look for a specific submenu
	 *
	 * @return array on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	private static function buildMenu($menuId = 0,$subMenuId = 0){
		$database = new database();
		return $database->readSingle("cms_menu","PK_MenuID,Headline,Path,Glyph,Priority,FK_MenuID,Brand,Active,NavHeader,Divider","PK_MenuID = ".$menuId." AND FK_MenuID = ".$subMenuId."  AND Language = '".$_SESSION['CountryCode']."'");
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
		$database = new database();
		return $database->read("cms_menu","PK_MenuID,Headline","Language = '".$_SESSION['CountryCode']."'");		
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
	public static function listMenu($submenu = 0){
		$database = new database();
		$menu = array();
		$menuArray = $database->read("cms_menu","PK_MenuID, FK_MenuID","Language = '".$_SESSION['CountryCode']."'","Brand DESC, FK_MenuID, Priority");
		$max = sizeof($menuArray);
		for($i = 0; $i < $max;$i++){
			list($menuId,$subMenuId) = $menuArray[$i];
			$menu[] = self::buildMenu($menuId,$subMenuId);
		}
		return $menu;
	}

	/**
     * This method creates submenus.
	 *
	 * @param int $submenu the id of the submenu items
	 *
	 * @return array on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 * @TODO: make it use template blocks
     */		
	public static function makeSubMenu($submenu){
		$database = new database();
		$menuArray = $database->read("cms_menu","PK_MenuID,Headline,Path,Glyph,Priority,FK_MenuID,Brand,Active,NavHeader,Divider","FK_MenuID = ".$submenu." AND Language = '".$_SESSION['CountryCode']."'","Brand DESC, FK_MenuID, Priority");
		$menu = '';

		foreach($menuArray as $singleLine){
			$menu .= self::makeSubMenu($singleLine[0]);
			if($singleLine[9] == 1){
				$menu .= '<li class="divider"></li>';
			} else {
				if($singleLine[7] == 1){
					$active = 'active';
				} else {
					$active = '';
				}
				if($singleLine[8] == 1){
					$menu .= '<li class="dropdown-header">'.$singleLine[1].'</li>';
				} else {
					if($singleLine[3] != ''){
						$menu .= '<li class="'.$active.'"><a href="'.$singleLine[2].'"><span class="glyphicon glyph'.$singleLine[3].' menu_icon"></span>'.$singleLine[1].'</a></li>';
					} else {
						$menu .= '<li class="'.$active.'"><a href="'.$singleLine[2].'">'.$singleLine[1].'</a></li>';
					}
				}
			}
		}
		if($menu){
			$menu = '<ul class="dropdown-menu">'.$menu.'</ul>';
		}
		return $menu;
	}	
	
	/**
     * This method creates the menu, it builds the menu using <ul> and bootstrap.
	 * it will also include language flags.
	 *
	 * @param int $menudId the id to look up
	 *
	 * @return string the menu.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 * @TODO: make it use template blocks
     */			
	public static function makeMenu(){
		$database = new database();
		$menuArray = $database->read("cms_menu","PK_MenuID,Headline,Path,Glyph,Priority,FK_MenuID,Brand,Active,NavHeader,Divider","FK_MenuID = 0 AND Language = '".$_SESSION['CountryCode']."'","Brand DESC, FK_MenuID, Priority");
		$brand = '';
		$menuList = '';
		foreach($menuArray as $singleLine){
			
			if($singleLine[6] == 1){
				$brand = '<a class="navbar-brand" href="'.$singleLine[2].'">'.$singleLine[1].'</a>';
			} else {
				if($singleLine[7] == 1){
					$active = 'active';
				} else {
					$active = '';
				}
				$subMenuList = self::makeSubMenu($singleLine[0]);
				if($subMenuList){
					$menuList .= '<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$singleLine[1].' <b class="caret"></b></a>'.$subMenuList.'</li>';
				} else {
					if(strlen($singleLine[3]) > 2){
						$menuList .= '<li class="'.$active.'"><a href="'.$singleLine[2].'"><span class="glyphicon '.$singleLine[3].' menu_icon"></span>'.$singleLine[1].'</a></li>';
					} else {
						$menuList .= '<li class="'.$active.'"><a href="'.$singleLine[2].'">'.$singleLine[1].'</a></li>';
					}
				}
			}
		}
		$menuList .= '<li class="dropdown">'.userlanguage::listFlags().'</li>';
		
		$menu = '
		<div class="navbar navbar-inverse" role="navigation">
			<div class="container">     
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
                </button>
                '.$brand.'
				</div>
				<!--nav class="navbar-collapse collapse menushader bs-navbar-collapse" role="navigation"-->
				<nav class="navbar-collapse collapse menushader bs-navbar-collapse" role="navigation">
					<ul class="nav navbar-nav">'.$menuList.'</ul>
				</nav>
			</div>
        </div>';
		
		return $menu;
	}

	/**
     * This method updates a menu with all the current parameters.
	 *
	 * @param int $menud
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
	public static function updateMenuItem($menuId, $headline, $path, $glyph='-', $priority=0, $submenuid=0, $brand=0, $active=0, $navheader=0, $divider=0){
		$database = new database();
		$data = array("Headline" => "'".$headline."'","Path" => "'".$path."'","Glyph" => "'".$glyph."'","Priority" => $priority, "FK_MenuID" => $submenuid, "Brand" => $brand, "Active" => $active, "NavHeader" => $navheader, "Divider" => $divider, "Language" => "'".$_SESSION['CountryCode']."'");
		return $database->update("cms_menu",$data,"PK_MenuID = ".$menuId);
	}
	
	/**
     * This method deletes a menu entry in the database.
	 *
	 * @param int $menuId The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyMenuItem($menuId){
		$database = new database();
		if(!$database->destroy("cms_menu","PK_MenuID = ".$menuId)){
			return false;
		}
		return true;
	}
}
?>