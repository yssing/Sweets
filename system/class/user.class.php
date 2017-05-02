<?php
/**
 * This class handles different methods for user data manipulation.
 * This class only handles generic user methods, such as login, create and 
 * some updates which are essential for the system.
 * Methods and variables not found here should be added in extended classes.
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
include_once('salt.class.php');
include_once('dbobject.class.php');
class user {

	/**
	 * This method creates a new user.
	 * If the user is created successfully, then the userid will be found and the UID
	 * will then be updated using the userid.
	 *
	 * @param string $username the username.	 
	 * @param string $userstatus the users status, defaults to USER.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function createUser($username,$userstatus = 'USER'){
		if (!self::doesExist($username)){
			$dbobject = new dbobject('user');
			$dbobject->create('UserLogin',$username);
			$dbobject->create('UserStatus',$userstatus);
			if ($dbobject->commit()){
				$userid = $dbobject->readLastEntry();
				self::updateUID($userid);
				return $userid;
			} else {
				return false;
			}
		}
		return false;
	}
	
	/**
	 * This method validates a users email.
	 *
	 * @param string $email The users e-mail to validate
	 *
	 * @return bool true if mail is available and validates as mail else false
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	 
	public static function validateEMail($email){
		if (self::countUserEMail($email) || !$email){
			return false;
		} else {
			if (validation::isEMail($email)){
				return true;
			} else {
				return false;
			}
		}	
	}
	
	/**
     * This method checks if a given user login exists in the database.
	 *
	 * @param string $username What username to check.
	 *
	 * @return int The id matching the row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function doesExist($username){
		$dbobject = new dbobject('user');
		$dbobject->read("PK_UserID");
		$dbobject->where("UserLogin", $username);
		list($id) = $dbobject->fetchSingle();
		return ($id) ? ($id) : false;	
	}	

	/**
	 * This method updates the users unique id.
	 * The UID is created by md5 hashing the userid with time and a random number.
	 *
	 * @param int $userid the user id in the database.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function updateUID($userid){
		$dbobject = new dbobject('user');
		$dbobject->update('UID',md5($userid.time().mt_srand()));
		$dbobject->where("PK_UserID", $userid);
		return $dbobject->commit();		
		
	}
	
	/**
	 * This method updates the users status.
	 * The status is used to control a users permissions.
	 *
	 * @param int $status the new status.	 
	 * @param int $userid the user id in the database.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public static function updateUserStatus($status,$userid){
		$dbobject = new dbobject('user');
		$dbobject->update('UserStatus', $status);
		$dbobject->where("PK_UserID", $userid);
		return $dbobject->commit();			
	}
	
	/**
	 * This method updates the users password.
	 *
	 * The method will MD5 encrypt the pasword with a salt string stored
	 * in the database.
	 * 
	 * @param string $UserPassword the users password.	 
	 * @param int $userid the user id in the database.	 
	 * @param string $type the type of salt.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateUserPassword($userPassword,$userid,$type = 'USER_SECRET'){
		$salt = new salt();
		$dbobject = new dbobject('user');
		$dbobject->update('UserPassword', md5($salt->readSalt($type).$userPassword));
		$dbobject->where("PK_UserID", $userid);
		return $dbobject->commit();	
	}
		
	/**
	 * This method sets the user validation.
	 *
	 * @param int $userid the user id in the database.
	 * @param int $validate validated status, defaults to 1.
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateUserValidate($userid, $validate = 1){
		$dbobject = new dbobject('user');
		$dbobject->update('Validated', $validate);
		$dbobject->where("PK_UserID", $userid);
		if ($dbobject->commit()){
			self::updateUID($userid);
			return true;
		}
	}		
	
	/**
	 * This method lists all the user entries in the database. 
	 *
	 * @param String $searchval If any wildcard search is done.
	 *	 
	 * @return true or status on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */
	public static function listUsers($searchval = ''){
		$dbobject = new dbobject("user");
		$dbobject->read("PK_UserID");
		$dbobject->read("UserFirstName");
		$dbobject->read("UserLastName");
		$dbobject->read("UserLogin");
		$dbobject->read("UserEMail");
		if ($searchval){
			$dbobject->wildcard("UserFirstName",$searchval);
			$dbobject->wildcard("UserLastName",$searchval);
			$dbobject->wildcard("UserLogin",$searchval);
			$dbobject->wildcard("UserEMail",$searchval);
		}
		return $dbobject->fetch();
	}
	
	/**
	 * This method reads user information for admin editing.
	 *
	 * @param int $userid the users id.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public static function readUser($userid){
		$dbobject = new dbobject("user");
		$dbobject->read("PK_UserID");
		$dbobject->read("UserFirstName");
		$dbobject->read("UserLastName");
		$dbobject->read("UserLogin");
		$dbobject->read("UserEMail");
		$dbobject->read("AcceptNews");
		$dbobject->read("AcceptMails");
		$dbobject->read("UserStatus");
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->fetchSingle();
	}	
	
	/**
	 * This method reads a users e-mail.
	 *
	 * @param int $userid the users id.	 
	 *	 
	 * @return string/bool $email on success else false.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	
	public static function readUserMail($userid){
		$dbobject = new dbobject("user");
		$dbobject->read("UserEMail");
		$dbobject->where("PK_UserID",$userid);
		list($email) = $dbobject->fetchSingle();		
		return $email;		
	}
	
	/**
	 * This method counts the number of user with a specific status.
	 *
	 * @param string $status The type of the user status.	 
	 *	 
	 * @return int The amount found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */
	public static function countUser($status){
		$dbobject = new dbobject("user");
		$dbobject->where("UserStatus",$status);
		return $dbobject->count();
	}		
	
	/**
	 * This method counts the number of times a specifik email is listed.
	 * Any email should only be listed once, so theis method should never return more than 1
	 *
	 * @param int $email the users email.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	
	public static function countUserEMail($email){
		$dbobject = new dbobject("user");
		$dbobject->where("UserEMail",$email);
		return $dbobject->count();		
	}
	
	/**
	 * This method counts the number of times a specifik username is listed.
	 * Any username should only be listed once, so theis method should never return more than 1
	 *
	 * @param int $userlogin the users login name.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	
	public static function countUserLogin($userlogin){
		$dbobject = new dbobject("user");
		$dbobject->where("UserLogin",$userlogin);
		return $dbobject->count();			
	}
	
	/**
	 * This method reads a users credentials.
	 * The method is used in the recover-password code.
	 *
	 * @param string $email the users email.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	
	public static function readUserCredentials($email){
		$dbobject = new dbobject("user");
		$dbobject->read("UserLogin");
		$dbobject->read("PK_UserID");
		$dbobject->read("UID");

		$dbobject->where("UserEMail",$email);
		return $dbobject->fetchSingle();		
	}

	/**
	 * This method reads a users ID from the UID.
	 *
	 * @param string $UID the users unique ID.	 
	 *	 
	 * @return int The userid found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */	
	public static function readUserID($UID){
		$dbobject = new dbobject("user");
		$dbobject->read("PK_UserID");
		$dbobject->where("UID",$UID);
		list($userid) = $dbobject->fetchSingle();		
		return $userid;
	}
	
	/**
	 * This method reads a users UID from the ID.
	 *
	 * @param int $ID the users ID.	 
	 *	 
	 * @return string The UID found.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function readUID($userid){
		$dbobject = new dbobject("user");
		$dbobject->read("UID");
		$dbobject->where("PK_UserID",$userid);
		list($UID) = $dbobject->fetchSingle();		
		return $UID;		
	}
	
	/**
	 * This method can log in a user.
	 *
	 * The user information is pulled from the database.
	 * If the information is found, it looks at the users status
	 * and if the status is 0, then the user session is created.
	 * It uses the salt for password hashing and for session control.
	 * The method can also create a cookie for quick login
	 *
	 * @param string $username the users login name.
	 * @param string $password the users password.
	 * @param int $remember create a cookie for fast login.
	 * @param int $cookie login by cookie, then pw is already hashed.
	 *
	 * @return $id or status on success or false on failure.
	 * if status, then the user rights are wrong.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */			
	public static function userLogin($username,$password,$remember = 0,$cookie = 0){		
		$salt = new salt();
		$dbobject = new dbobject('user');
		
		if ($cookie){
			$passwordHashed = $password;
		} else {
			$passwordHashed = md5($salt->readSalt('USER_SECRET').$password);
		}
		
		$dbobject->read('PK_UserID');
		$dbobject->read('UserLogin');
		$dbobject->read('UserStatus');
		$dbobject->read('Validated');
		$dbobject->read('UserFirstName');
		$dbobject->read('UserLastName');		
		
		if (validation::isEMail($username)){
			$dbobject->where('UserEMail',$username);
		} else {
			$dbobject->where('UserLogin',$username);
		}	
		$dbobject->where('UserPassword',$passwordHashed);			
		$user = $dbobject->fetchSingle();
		
		if (is_array($user) && sizeof($user)){
			list($id,$login,$status,$valid,$first,$last) = $user;
			if ($status == "USER" && $valid == 1){
				if ($remember){
					// set cookie with a path '/' enables the cookie all over the site.
					setcookie("rememberlogin", $login.":".$passwordHashed.":".$salt->readSalt('COOKIE_SECRET'), (time()+3600*8760),'/');
				}	
				$_SESSION['userSession'] = array(
					"UserID" => $id,
					"UserLogin" => $login,
					"Validated" => $valid,
					"FirstName" => $first,
					"LastName" => $last,
					"Status" => $status,
					"SessionSecret" => $salt->readSalt('SESSION_SECRET'),
				);
				baseclass::$userid = $id;				
				return $id;
			} else {
				return $status;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * This method can log in a user as admin.
	 *
	 * The user information is pulled from the database.
	 * If the information is found, it looks at the users status
	 * and if the status is different from 0, the status is checked to see
	 * if it matches ADMIN.
	 * If it is an admin logging in, then the admin session is created.
	 *
	 * @param string $username the users login name.
	 * @param string $password the users password.
	 *
	 * @return true or status on success or false on failure.
	 * if status, then the user rights are wrong.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */			
	public static function adminLogin($username,$password){
		$salt = new salt();
		$dbobject = new dbobject('user');	
		
		$dbobject->read('PK_UserID');
		$dbobject->read('UserLogin');
		$dbobject->read('UserStatus');
		$dbobject->read('Validated');
		$dbobject->read('UserFirstName');
		$dbobject->read('UserLastName');		
		
		if (validation::isEMail($username)){
			$dbobject->where('UserEMail',$username);
		} else {
			$dbobject->where('UserLogin',$username);
		}	
		$dbobject->where('UserPassword',md5($salt->readSalt('ADMIN_SECRET').$password));			
		$user = $dbobject->fetchSingle();

		if (is_array($user) && sizeof($user)){
			list($id,$login,$status,$valid,$first,$last) = $user;
			if ($status == 'ADMIN'){
				$_SESSION['adminSession'] = array(
					"AdminID" => $id,
					"UserLogin" => $login,
					"Validated" => $valid,
					"FirstName" => $first,
					"LastName" => $last,
					"Status" => $status,
					"SessionSecret" => $salt->readSalt('SESSION_SECRET'),
				);
				baseclass::$adminid = $id;
				return true;
			} else {
				return $status;
			}
		} else {
			return false;
		}
	}	
	
	/**
	 * This method validates user secret and session.
	 *
	 * It checks if the user session is active and then
	 * It checks the database for the session secret and compare it
	 * with the secret stored in the session.
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function validateUser(){
		$salt = new salt();
		if (isset($_SESSION['userSession'])){
			$secret = $salt->readSalt('SESSION_SECRET');
			if ($_SESSION['userSession']['SessionSecret'] == $secret){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * This method validates admin secret.
	 *
	 * It checks if the admin session is active and then
	 * It checks the database for the session secret and compare it
	 * with the secret stored in the session.
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function validateAdmin(){
		$salt = new salt();
		if (isset($_SESSION['adminSession'])){
			$secret = $salt->readSalt('SESSION_SECRET');
			if ($_SESSION['adminSession']['SessionSecret'] == $secret){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}		

	/**
	 * This method can login a user with the cookie.
	 *
	 * It checks the database for the cookie secret and compare it
	 * with the secret stored in the cookie. If they match, then
	 * it will try and login the user with the data stored in the cookie.
	 * NB. for security reasons this only works for users not for admins.
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */			
	public static function loginByCookie(){
		if (isset($_COOKIE['rememberlogin'])){
			$salt = new salt();
			$cookiesecret = $salt->readSalt('COOKIE_SECRET');
			list($login,$password,$secret) = explode(':', $_COOKIE['rememberlogin']);
			if ($secret == $cookiesecret){
				if (self::userLogin($login,$password,1,1)){
					//re-set the cookie for an additional year
					//$value = $login.":".$password.':'.$cookiesecret;
					//setcookie("rememberlogin", $value, (time()+3600*8760));
					return true;
				} else {
					return false;
				}
			} else {
				setcookie("rememberlogin", '', (time()-3600));
				return false;
			}
		} else {
			return false;
		}		
	}
	
	/**
	 * This method fetches the last created user.
	 *
	 * @return bool/array Returns array of data on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function getLastUser(){
		$dbobject = new dbobject('user');
		$dbobject->count();
		return $dbobject->fetchSingle();
	}
	
	/**
     * This method deletes an entry in the user table.
	 *
	 * @param int $userid the user id in the table.
	 *
	 * @return TRUE on successful entry or FALSE on failure or if parameter is missing.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyUser($userid){
		if (!$userid){
			return false;
		} 

		$dbobject = new dbobject('user');
		$dbobject->destroy();
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->commit();		
	}	
}
?>