<?php
/**
 * This class handles key.
 * The key and value can be useful for storing data without creating dedicated tables and columns.
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
 * @package    	keys
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'database.class.php' 
 */
require_once('database.class.php');
class key /*extends database*/{

	/**
	 * This method creates an entry in the settings table.
	 * Key is used to store the name and Value to store the value.
	 *
	 * @param string $Key 
	 * @param string $Value 
	 *
	 * @return bool true on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	
	public static function createKey($Key,$Value){
		$database = new database('generic_key');
		$data = array("KeySetting" => "'".$Key."'" ,"ValueSetting" => "'".$Value."'");
		if(!$database->create($data)){
			return false;
		}
		return true;
	}
	
	/**
	 * This method will search for a given key to see if it is already stored
	 * in the database.
	 *
	 * @param string $key 
	 *
	 * @return int id on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function doesExist($key){
		$database = new database('generic_key');
		list($id) = $database->readSingle("PK_KeyID","KeySetting = '".$key."'");
		return $id;	
	}
	
	/**
     * This method finds the newest entry in the key table.
	 *
	 * @return int/bool id on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function findlast(){
		$database = new database('generic_key');
		list($id) = $database->readLastEntry();
		return $id;
	}	
	
	/**
	 * This method updates the value of a key
	 *
	 * @param string $Key
	 * @param string $Value
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateKey($id,$Key,$Value){
		$database = new database('generic_key');
		$data = array("ValueSetting" => "'".$Value."'", "KeySetting" => "'".$Key."'");
		$where = " PK_KeyID = ".$id;
		
		if(!$database->update($data,$where)){
			return false;
		}
		return true;
	}
	
	/**
	 * This method lists all the Keys in the table.
	 *
	 * @return array of keys on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function listKeys(){
		$database = new database('generic_key');
		return $database->read("PK_KeyID, KeySetting, ValueSetting");	
	}	
	
	/**
	 * This method reads and returns the selected row in the key table.
	 * It can take as key either the Key or the PK_ID for the row, if it finds 
	 * a value, then that value will be returned, if not it returns false.
	 *
	 * @param string $Key
	 *
	 * @return string/bool Value on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function readKey($Key){
		$database = new database('generic_key');
		if(!$Key){
			return false;
		}
		if(is_numeric($Key)){
			$where = "PK_KeyID = ".$Key;
		} else {
			$where = "KeySetting = '".$Key."'";
		}
		$Value = $database->readSingle('',$where); 
		if(is_array($Value)){
			return $Value;
		} else {
			return false;
		}		
	}	

	/**
	 * This method reads and returns only the value of a key.
	 * It can take as key either the Key or the PK_ID for the row, if it finds 
	 * a value, then that value will be returned, if not it returns false.
	 *
	 * @param string $Key
	 *
	 * @return string/bool Value on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public static function readValue($key){
		$database = new database('generic_key');
		if(!$key){
			return false;
		}
		if(is_numeric($key)){
			$where = "PK_KeyID = ".$key;
		} else {
			$where = "KeySetting = '".$key."'";
		}
		$Value = $database->readSingle('ValueSetting',$where); 
		if(is_array($Value)){
			list($Value) = $Value;
			return $Value;
		} else {
			return false;
		}
	}
	
	/**
	 * This method deletes a key from the database.
	 *
	 * @param string $Key 	 
	 *
	 * @return bool true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function destroyKey($Key){
		$database = new database('generic_key');
		if(!$Key){
			return false;
		}
		$where = "PK_KeyID = ".$Key;
		if(!$database->destroy($where)){
			return false;
		}
		return true;
	}	
}
?>