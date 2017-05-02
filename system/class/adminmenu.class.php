<?php
/**
 * This class handles the admin menu.
 * It reads through folders, and list folders, that contains "control" as a part of the name.
 * It will link to that folders list action, if a list action does not exists, then the router
 * will revert to using the index action instead.
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
 * @category   	Generic system methods
 * @package    	admin menu
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 */
class adminmenu {

	public static $adminmenu = '';
	
	/**
	 * This method creates the menu for the administrator.
	 *
	 * This is done by looping through all directories in the root folder.
	 * the folders that has "control" as part of the name will be listed, with a link
	 * to the folder with a list action.
	 *
	 * If a menu.json file is found in the root of a module folder, then this is used
	 * to create the menu for that module instead of scanning through the folders to create
	 * links to the controllers
	 *
	 * @param string $dir The directory to look in.
	 *
	 * @return string the admin menu.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0	
	 */
	public static function createMenu($dir){
		if (!baseclass::$adminid){
			return '';
		}
		$header = '';
		$folders = scandir($dir);
		foreach($folders as $folder){
			if ($folder != '.' && 
				$folder != '..' && 
				$folder != 'contributions' &&
				$folder != 'uploads' &&
				$folder != 'template' &&
				$folder != 'settings' &&
				$folder != 'scripts' &&
				$folder != 'plugins' &&
				$folder != 'cache' &&
				$folder != 'audio' &&
				$folder != 'common' ){

				if (is_dir($dir.'/'.$folder)) {
					if (is_file($dir.'/'.$folder.'/menu.json')){
						$json = json_decode(file_get_contents($dir.'/'.$folder.'/menu.json',false), true);
						if (is_array($json)){
							foreach($json as $key => $value){
								self::$adminmenu .= '<li class="headline">'.$key.'</li>';
								
								foreach($value as $key => $value){
									self::$adminmenu .= '<li><a href="/modules/'.$folder.'/'.$value.'/list">'.$key.'</a></li>';									
								}								
							}
						}						
					} else {
						if (strpos($folder, 'control')){
							$tmpdir = str_replace($_SERVER['DOCUMENT_ROOT'],'',$dir);
							if ($tmpdir != $header){
								$headline = explode('/',$tmpdir);
								self::$adminmenu .= '<li class="headline">'.$headline[(sizeof($headline)-1)].'</li>';
							}
							$header = $tmpdir;
							$foldername = str_replace('control','',$folder);
							self::$adminmenu .= '<li><a href="'.PATH_WEB.'/'.$tmpdir.'/'.$foldername.'/list">'.$foldername.'</a></li>';
						}
						self::createMenu($dir.'/'.$folder);
					}
				}
			}
		}
		return self::getMenu();
	}
	
	private static function getMenu(){	
		$menuList = '<ul class="adminmenu">';
		$menuList .= self::$adminmenu;
		$menuList .= '<li class="headline">'.language::readType('LANGUAGE').'</li>';
		$menuList .= '<li>'.language::listFlags().'</li>';
		$menuList .= '</ul>';
		return $menuList;
	}
}
?>