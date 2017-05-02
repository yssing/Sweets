<?php
/**
 * This class handles plugins
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
 * @package    	plug-ins
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 */

class plugin{
	
	/**
	 * This method lists all the plug-in entries in the database.
	 *
	 * @return array on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function listPlugin(){
		$dbobject = new dbobject('generic_plugin');
		$dbobject->read("PK_PluginID");
		$dbobject->read("Name");
		$dbobject->read("Activated");
		$dbobject->orderby("Name");
		return $dbobject->fetch();
	}	
	
	/**
     * This method creates a plug-in entry in the table.
	 *
	 * In order to activate a plug-in, it must be added to the table, it will then
	 * be activated and ready to use.
	 *
	 * @param string $plugin The name of the plug-in.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function addPlugin($plugin){
		caching::deleteKey("plugindata");
		$dbobject = new dbobject('generic_plugin');
		$dbobject->create('Name',$plugin);
		$dbobject->create('Activated',1);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;		
		
	}

	/**
     * This method de-activates a plug-in.
	 *
	 * @param int $plugin The id of the plug-in.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function disablePlugin($pluginid){
		caching::deleteKey("plugindata");
		$dbobject = new dbobject('generic_plugin');
		$dbobject->update('Activated',0);
		$dbobject->where("PK_PluginID", $pluginid);
		return $dbobject->commit();		
	}

	/**
     * This method activates a plug-in.
	 *
	 * @param int $plugin The id of the plug-in.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function enablePlugin($pluginid){
		caching::deleteKey("plugindata");
		$dbobject = new dbobject('generic_plugin');
		$dbobject->update('Activated',1);
		$dbobject->where("PK_PluginID", $pluginid);
		return $dbobject->commit();	
	}	
	
	/**
     * This method finds and returns a single plug-in row in from the database.
	 *
	 * @param int $plugin The id of the plug-in.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function readPlugin($pluginid){
		$dbobject = new dbobject('generic_plugin');
		$dbobject->read("PK_PluginID");
		$dbobject->read("Activated");
		$dbobject->read("Name");
		$dbobject->where("PK_PluginID", $pluginid);
		return $dbobject->fetchSingle();		
	}

	/**
     * This method lists all the plug-ins in the plug-in folder.
	 *
	 * @param int $cols The number of columns, defaults to 5.
	 *
	 * @return array $foldercontent with all the plug-ins.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listerPlugin($cols = 5){
		$foldercontent = array();
		$i = 1;
		$j = 0;
		if ($handle = opendir('plugins/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && is_dir('plugins/'.$entry)){
					$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/plug_add.png" width="128px;" />';
					$foldercontent[$j][$i] = $content.'<br /><a href="/system/plugin/toggle/'.$entry.'">'.$entry.'</a> 
					| <a href="/system/plugin/remove/'.$entry.'">Slet</a>';
					if ($i >= $cols){
						$i = 0; 
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
	 * This method will search for a given key to see if it is already stored
	 * in the database.
	 *
	 * @param string $Key 
	 *
	 * @return int id on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function doesExist($plugin){
		$dbobject = new dbobject('generic_plugin');
		$dbobject->read("PK_PluginID");
		$dbobject->where("Name", $plugin);
		list($id) = $dbobject->fetchSingle();		
		return $id;	
	}	
	
	/**
     * This method finds all the Activated plug-ins and puts them in a 2D array.
	 *
	 * @return array $plugins with all the plug-ins.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function retrievePlugin(){
		$plugins = '';
		
		if ($pluginData = caching::getKey('plugindata')){
			$plugins = $pluginData;
		} else {
			$dbobject = new dbobject('generic_plugin');
			$dbobject->read("Name");
			$dbobject->where("Activated", 1);
			$pluginlist = $dbobject->fetch();
			
			if (is_array($pluginlist)){
				foreach($pluginlist as $plugin){
					if (is_file('plugins/'.$plugin[0].'/'.$plugin[0].'.css')){
						$plugins .= '<link href="'.PATH_WEB.'/plugins/'.$plugin[0].'/'.$plugin[0].'.css" type="text/css" rel="stylesheet" />'.chr(13);
					} else if (is_file('plugins/'.$plugin[0].'/'.$plugin[0].'.less')){
						$plugins .= '<link href="'.PATH_WEB.'/plugins/'.$plugin[0].'/'.$plugin[0].'.less" type="text/css" rel="stylesheet/less" />'.chr(13);
					}
					if (is_file('plugins/'.$plugin[0].'/'.$plugin[0].'.js')){
						$plugins .= '<script src="'.PATH_WEB.'/plugins/'.$plugin[0].'/'.$plugin[0].'.js" type="text/javascript"></script>'.chr(13);
					} else if (is_file('plugins/'.$plugin[0].'/'.$plugin[0].'.min.js')){
						$plugins .= '<script src="'.PATH_WEB.'/plugins/'.$plugin[0].'/'.$plugin[0].'.min.js" type="text/javascript"></script>'.chr(13);
					} 
				}
				caching::setKey('plugindata', $plugins);
			}
		}		
		return $plugins;
	}
	
	/**
     * This method deletes a plugin entry in the database.
	 *
	 * @param int $pluginid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyPlugin($pluginid){
		caching::deleteKey("plugindata");
		$dbobject = new dbobject('generic_plugin');
		$dbobject->destroy();
		$dbobject->where("PK_PluginID",$pluginid);
		return $dbobject->commit();		
	}
}
?>