<?php
/**
 * This class handles themes and is used with the templating class.
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
 * @package		template
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'database.class.php'
 */

class theme{

	/**
     * This method list all themes
	 *
	 * @param int $cols Number of columns in the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function listerTheme($cols = 5){
		$foldercontent = array();
		$i = 1;
		$j = 0;
		if ($handle = opendir('template/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && is_dir('template/'.$entry)){
					if (file_exists('template/'.$entry.'/example.png')){
						$content = '<img src="'.PATH_WEB.'/template/'.$entry.'/example.png" width="180px;" />';	
					} else if (file_exists('template/'.$entry.'/example.jpg')){
						$content = '<img src="'.PATH_WEB.'/template/'.$entry.'/example.jpg" width="180px;" />';	
					} else {
						$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/plug_add.png" width="180px;" />';	
					}
					
					$foldercontent[$j][$i] = $content.'<br /><a href="'.PATH_WEB.'/modules/cms/theme/activate/'.$entry.'">'.language::readType('ENABLE').' '.$entry.'</a> 
					| <a href="'.PATH_WEB.'/cms/theme/delete/'.$entry.'">'.language::readType('DELETE').'</a>';
					if ($i >= $cols){
						$i = 1; 
						$j++;
					}
					$i++;
				}
			}
			closedir($handle);
		}		
		return $foldercontent;
	}
	
	/**
     * This method enables a theme.
	 * It will check if the key is present, and if not, it will create one.
	 * It finds the id of the template key, and uses that id to set the new key
	 *
	 * @param string $theme The theme.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function enableTheme($theme){
		if (!key::doesExist('TEMPLATE')){
			key::createKey('TEMPLATE','default');
		}
		list($keyid) = key::readKey('TEMPLATE');
		return key::updateKey($keyid,'TEMPLATE',$theme);
	}
	
	/**
     * This method reads data on a single theme based on the key setting.
	 *
	 * @return string $name.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function readTheme(){
		list($id,$key,$name) = key::readKey('TEMPLATE');
		return $name;
	}
}
?>