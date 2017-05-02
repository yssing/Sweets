<?php
/**
 * This class handles different methods for salting passwords, sessions and cookies
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
 * @category   	Salt methods
 * @package    	database class
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'database.class.php'
 */
//require_once('database.class.php'); 
class salt {

	/**
	 * This method creates a new salt entry in the database.
	 *
	 * @param string $name What type of salt are we dealing with.
	 * @param string $value The value of the salt key.
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 */
	public function createSalt($name,$value){
		$dbobject = new dbobject('generic_salt');
		$dbobject->create('Salt_type',$name);
		$dbobject->create('Salt',$value);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;
	}

	/**
	 * This method lists all the salt entries in the database.
	 *
	 * @return salt value on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public function listSalt(){
		$dbobject = new dbobject('generic_salt');
		return $dbobject->fetch();
	}

	/**
	 * This method find a salt value based on id or type.
	 * If the id paramter is an integer, the method checks on the private key.
	 * If its a string, the it checks on the key.
	 *
	 * @param string/integer $id What key to read.
	 *
	 * @return string salt value on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function readSalt($id){
		if (!$id){
			return false;
		}
		$dbobject = new dbobject('generic_salt');
		$dbobject->read("Salt");
		if (is_numeric($id)){
			$dbobject->where("PK_SaltID", $id);
		} else {
			$dbobject->where("Salt_type", $id);
		}
		list($salt) = $dbobject->fetchSingle();
		return $salt;
	}		
	
	/**
	 * This method updates the value of a salt entry.
	 * If the id paramter is an integer, the method checks on the private key.
	 * If its a string, the it checks on the key.	
	 *
	 * @param string $id What key are we updating with.
	 * @param string $value The value of the salt key.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public function updateSalt($id,$salt){
		$dbobject = new dbobject('generic_salt');
		$dbobject->update('Salt',$salt);
		if (is_numeric($id)){
			$dbobject->where("PK_SaltID", $id);
		} else {
			$dbobject->where("Salt_type", $id);
		}
		return $dbobject->commit();
	}
	
	/**
	 * This method deletes a salt entry from the database.
	 *
	 * @param string $saltid 	 
	 *
	 * @return bool true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function destroySalt($saltid){
		$dbobject = new dbobject('generic_salt');
		$dbobject->destroy();
		$dbobject->where("PK_SaltID",$saltid);
		return $dbobject->commit();
	}	 	
}
?>