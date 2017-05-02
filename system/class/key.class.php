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
include_once('baseclass.class.php');
class key extends baseclass{

	/**
	 * This method creates an entry in the settings table.
	 * Key is used to store the name and Value to store the value.
	 *
	 * @param string $key 
	 * @param string $value 
	 *
	 * @return bool true on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	
	public static function createKey($key,$value){
		$dbobject = new dbobject('generic_key');
		$dbobject->create('KeySetting',$key);
		$dbobject->create('ValueSetting',$value);

		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;	
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
		$dbobject = new dbobject('generic_key');
		$dbobject->read("PK_KeyID");
		$dbobject->where("KeySetting", $key);
		if ($id = $dbobject->fetchSingle()){
			list($id) = $dbobject->fetchSingle();
			return $id;
		}
		return false;
	}
	
	/**
	 * This method updates the value of a key
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateKey($id,$key,$value){
		$dbobject = new dbobject('generic_key');
		$dbobject->update('KeySetting',$key);
		$dbobject->update('ValueSetting',$value);
		$dbobject->where("PK_KeyID", $id);
		return $dbobject->commit();	
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
		$dbobject = new dbobject('generic_key');
		$dbobject->read("PK_KeyID");
		$dbobject->read("KeySetting");
		$dbobject->read("ValueSetting");
		return $dbobject->fetch();
	}	
	
	/**
	 * This method reads and returns the selected row in the key table.
	 * It can take as key either the Key or the PK_ID for the row, if it finds 
	 * a value, then that value will be returned, if not it returns false.
	 *
	 * @param string $key
	 *
	 * @return string/bool Value on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function readKey($key){
		if (!$key){
			return false;
		}
		$dbobject = new dbobject('generic_key');
		$dbobject->read("PK_KeyID");
		$dbobject->read("KeySetting");
		$dbobject->read("ValueSetting");
		if (is_numeric($key)){
			$dbobject->where("PK_KeyID", $key);
		} else {
			$dbobject->where("KeySetting", $key);
		}	
		return $dbobject->fetchSingle();
	}	

	/**
	 * This method reads and returns only the value of a key.
	 * It can take as key either the Key or the PK_ID for the row, if it finds 
	 * a value, then that value will be returned, if not it returns false.
	 *
	 * @param string $key
	 *
	 * @return string/bool Value on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public static function readValue($key){		
		if (!$key){
			return false;
		}	
		$dbobject = new dbobject('generic_key');
		$dbobject->read("ValueSetting");
		if (is_numeric($key)){
			$dbobject->where("PK_KeyID", $key);
		} else {
			$dbobject->where("KeySetting", $key);
		}
		$value = $dbobject->fetchSingle();

		if (is_array($value)){
			list($value) = $value;
			return $value;
		} else {
			return false;
		}
	}
	
	/**
	 * This method deletes a key from the database.
	 *
	 * @param string $key 	 
	 *
	 * @return bool true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function destroyKey($key){
		$dbobject = new dbobject('generic_key');
		$dbobject->destroy();
		$dbobject->where("PK_KeyID",$key);
		return $dbobject->commit();		
	}		
}
?>