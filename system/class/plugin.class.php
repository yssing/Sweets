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

class plugin /*extends database*/{
	
	/**
	 * This method lists all the plug-in entries in the database.
	 *
	 * @return array on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function listPlugin(){
		$database = new database();
		return $database->read("cms_plugins","PK_PluginID,Name,Activated","","Name");
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
		$database = new database();
		$data = array("Name" => "'".$plugin."'","Activated" => "1");			
		$database->TransactionBegin();
		if($database->create('cms_plugins',$data)){
			$database->TransactionEnd();
		} else {
			$database->TransactionRollback();
			return false;
		}		
		return true;
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
		$database = new database();
		return $database->update("cms_plugins","Activated=0","PK_PluginID = ".$pluginid);			
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
		$database = new database();
		return $database->update("cms_plugins","Activated=1","PK_PluginID = ".$pluginid);		
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
		$database = new database();
		return $database->readSingle('cms_plugins','PK_PluginID, Activated, Name','PK_PluginID = '.$pluginid);
	}
	
	/**
     * This method deletes a plug-in entry in the database.
	 *
	 * @param int $pluginid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function removePlugin($pluginid){
		$database = new database();
		$database->TransactionBegin();
		if($database->destroy("cms_plugins","PK_PluginID = ".$pluginid)){
			$database->TransactionEnd();
		} else {
			$database->TransactionRollback();
			return false;
		}		
		return true;
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
				if($entry != '.' && $entry != '..' && is_dir('plugins/'.$entry)){
					$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/plug_add.png" width="128px;" />';
					$foldercontent[$j][$i] = $content.'<br /><a href="'.PATH_SYS.'/plugincontrol/toggle/'.$entry.'">'.$entry.'</a> 
					| <a href="'.PATH_SYS.'/plugincontrol/remove/'.$entry.'">Slet</a>';
					if($i >= $cols){
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
		$database = new database();
		list($id) = $database->readSingle("cms_plugins","PK_PluginID","Name = '".$plugin."'");
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
		$database = new database();
		$plugins = '';
		$pluginlist = $database->read("cms_plugins","Name","Activated = 1");
		foreach($pluginlist as $plugin){
			$plugins .= '<link href="'.PATH_WEB.'/plugins/'.$plugin[0].'/'.$plugin[0].'.css" type="text/css" rel="stylesheet" />'.chr(13);
			$plugins .= '<script src="'.PATH_WEB.'/plugins/'.$plugin[0].'/'.$plugin[0].'.js" type="text/javascript"></script>'.chr(13);;
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
		$database = new database();
		if(!$database->destroy("cms_plugins","PK_PluginID = ".$pluginid)){
			return false;
		}
		return true;
	}
}
?>