<?php
/**
 * This class handles loading of classes and methods.
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
 * @category	Generic system methods
 * @package		load
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */
 
class autoload{
	
	/**
	 * This method loads all the classes found in the class folder.
	 *
	 * As default it will load the system classes, but it can be given any
	 * path to any folder, where it will load and include any file found it that folder.
	 * It also loads the defines file, which is found in the system folder.
	 * If the defines are located anywhere else, then it must be located in the class's parent folder
	 *
	 * @param string $path what path to use for loading the classes.
	 * @param bool $defines load defines, default is true.
	 *
	 * @return bool true if path is a directory and false if not.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	
	/**
	 * This method starts loading all class files
	 * The classes must be located in either system/class or in any module in the module folder.
	 */
	//public static function load($syspath = 'system/class/', $modpath = 'modules'){
	public static function load($syspath = 'system/class/', $modpath = 'modules/'){
		if (is_file('settings/defines.php')){
			require_once('settings/defines.php');
		} else if (is_file('../settings/defines.php')){
			require_once('../settings/defines.php');
		} else if (is_file('../../settings/defines.php')){
			require_once('../../settings/defines.php');
		}
		self::loadSystem($syspath);		
		if($modpath){
			self::loadModules($modpath);
		}
	} 
	 
	/**
	 * This method loads all system classes.
	 */		 
	private static function loadSystem($path){
		if (is_dir($path)) {
			$objects = scandir($path);
			foreach ($objects as $object) {
				if ($object != "." && $object != ".."){
					require_once($path.$object);
				}
			}
		} else {
			return false;
		}
		return true;
	}
	
	/**
	 * This method loads all module classes.
	 */			
	private static function loadModules($path){
		$root = scandir($path);
		foreach($root as $value){
			if ($value != "." && $value != ".."){
				if (is_dir($path.'/'.$value)) {
					if (is_dir($path.'/'.$value.'/class')){
						$objects = scandir($path.'/'.$value.'/class');
						foreach ($objects as $object) {
							if ($object != "." && $object != ".."){
								require_once($path.'/'.$value.'/class/'.$object);
							}
						}
					} else {
						self::loadModules($path.'/'.$value);
					}
				} 
			}
		}
		return true;
	}
}
?>