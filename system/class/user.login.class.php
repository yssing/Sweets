<?php
/**
 * This class keeps track of when a user logs in and out.
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
 * @category	User methods
 * @package		User logging
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'database.class.php' 
 */
require_once('database.class.php');
class userLogin /*extends database*/{

	/**
     * This function creates a new entry in the login table.
	 * 
	 * It uses the userid from the base class, if the user session is active
	 *
	 * @return TRUE on successfull entry or FALSE on failure or if no id is found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function createLogin(){
		$database = new database();
		if(genericIO::$userid || genericIO::$adminid){
			if(!$database->create('user_login',array("UserLogin" => "NOW()"))){
				return false;
			}
		} else {
			return false;
		}
		return true;		
	}

	/**
	 * This method creates a logout date.
	 *
	 * It finds the id of the last login and then updates the 
	 * logout date with the current timetamp.
	 *
	 * @return bool TRUE on success or FALSE on failure or if no user id is found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */			
	public function createLogout(){
		$database = new database();
		if(genericIO::$userid){
			list($loginid) = $database->readLastEntry("user_login",array("FK_UserID" => genericIO::$userid,"UserLogout" => "'0000-00-00 00:00:00'"));		
		} else if (genericIO::$adminid){
			list($loginid) = $database->readLastEntry("user_login",array("FK_UserID" => genericIO::$adminid,"UserLogout" => "'0000-00-00 00:00:00'"));	
		} else {
			return false;
		}
		if(!$database->update("user_login",array("UserLogout" => "NOW()"),"PK_UserLoginID = ".$loginid)){
			return false;
		}		
		return true;		
	}	

	/**
	 * This method finds the login entries stored in the table.
	 *
	 * @param int $userid the users id used to make the look up.
	 * @param int $limit how many rows do we want.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function listUserLogin($userid = 0,$limit = 25){
		$database = new database();
		if($userid){
			return $database->read("user_login","UserLogin, UserLogout","FK_UserID = ".$userid,"",$limit);		
		}
		return false;
	}
	
	/**
	 * This method finds the latest login entry stored in the table.
	 *
	 * @return string/bool $lastlogin on success or FALSE no id is found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public static function readLastLogin(){
		$database = new database();
		if(genericIO::$userid){
			list($id,$userid,$lastlogin) = $database->readLastEntry("user_login","FK_UserID = ".genericIO::$userid);	
		} else if (genericIO::$adminid){
			list($id,$userid,$lastlogin) = $database->readLastEntry("user_login","FK_UserID = ".genericIO::$adminid);
		} else {
			return false;
		}
		return $lastlogin;
	}
}
?>