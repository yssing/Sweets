<?php
/**
 * This class handles user status.
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
 * @category   	User methods
 * @package    	userbase class
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'salt.class.php'
 * @require		'database.class.php'
 */
 
class userStatus{
	
	public static function updateStatus($statusid, $status){
		$dbobject = new dbobject('user_status');
		$dbobject->update('UserStatus',$status);	
		$dbobject->where("PK_UserStatusID",$statusid);
		return $dbobject->commit();			
	}

	/** 
	 * This method list user status.
	 *
	 * @return array The table row found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	 
	public static function listStatus(){
		$dbobject = new dbobject('user_status');
		$dbobject->read("PK_UserStatusID");
		$dbobject->read("UserStatus");
		return $dbobject->fetch();		
	}
	
	/** 
	 * This method list user status packed in a 2D array
	 *
	 * @return array The table row found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function listSelect(){
		$dbobject = new dbobject('user_status');
		$dbobject->read("UserStatus");
		$dbobject->fetch();
		
		foreach($dbobject->fetch() as $value){
			foreach($value as $single){
				$return[] = array($single,$single);
			}
		}
		return $return;
	}

	/** 
	 * This method reads a user status based on id.
	 *
	 * @return array The table row found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	 
	public static function readStatus($statusid){
		$dbobject = new dbobject('user_status');
		$dbobject->read("PK_UserStatusID");
		$dbobject->read("UserStatus");
		$dbobject->where("PK_UserStatusID", $statusid);
		return $dbobject->fetchSingle();		
	}	
	
	/**
     * This method checks if a given user status exists in the database.
	 *
	 * @param string $username What username to check.
	 *
	 * @return int The id matching the row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function doesExist($status){
		$dbobject = new dbobject('user_status');
		$dbobject->read("PK_UserStatusID");
		$dbobject->where("UserStatus", $status);
		list($id) = $dbobject->fetchSingle();
		if ($id){
			return $id;
		} else {
			return false;
		}
	}	
	
	/**
	 * This method creates a new user status entry.
	 *
	 * @param string $username the username.	 
	 * @param string $userstatus the users status, defaults to USER.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function createStatus($userstatus){
		$dbobject = new dbobject('user_status');
		$dbobject->create('UserStatus',$userstatus);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;		
	}	
	
	/**
     * This method deletes an entry in the user status table.
	 *
	 * @param int $statusid the status id in the table.
	 *
	 * @return TRUE on successful entry or FALSE on failure or if parameter is missing.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function destroyStatus($statusid){
		$dbobject = new dbobject('user_status');
		$dbobject->destroy();
		$dbobject->where("PK_UserStatusID",$statusid);
		return $dbobject->commit();			
	}	
}
?>