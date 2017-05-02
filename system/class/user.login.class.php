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

class userLogin{

	/**
     * This method creates a new entry in the login table.
	 * 
	 * It uses the userid from the base class, if the user session is active
	 *
	 * @return TRUE on successfull entry or FALSE on failure or if no id is found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function createLogin(){
		if (baseclass::$userid || baseclass::$adminid){
			$dbobject = new dbobject('user_login');
			$dbobject->create('UserLogin',calendar::now());
			$dbobject->create('IP',($_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '0'));
			$dbobject->create('ForwardIP',($_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '0'));
			return $dbobject->commit();	
		} else {
			return false;
		}	
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
	public static function createLogout(){	
		if (baseclass::$userid){
			$userid	= baseclass::$userid;
		} else if (baseclass::$adminid){
			$userid = baseclass::$adminid;	
		} else {
			return false;
		}		
		$dbobject = new dbobject('user_login');
		$dbobject->where("FK_UserID", $userid);
		$dbobject->where("UserLogout", "0000-00-00 00:00:00");
		$dbobject->orderby("PK_UserLoginID","DESC");
		$dbobject->limit(1);
		list($loginid) = $dbobject->fetchSingle();

		$dbobject1 = new dbobject('user_login');
		$dbobject1->update('UserLogout',calendar::now());
		$dbobject1->where("PK_UserLoginID", $loginid);
		return $dbobject1->commit();			
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
	public static function listUserLogin($userid = 0){
		$dbobject = new dbobject('user_login');
		$dbobject->read("PK_UserLoginID");
		$dbobject->read("UserLogin");
		$dbobject->read("UserLogout");
		$dbobject->read("IP");
		$dbobject->read("ForwardIP");
		$dbobject->where("FK_UserID", $userid);
		$dbobject->orderby("PK_UserLoginID","DESC");
		return $dbobject->fetch();
	}
}
?>