<?php
/**
 * This class handles user data updating.
 * It extends the user system class.
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
 * @package		custom package
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */
include_once('user.class.php');
class userBase extends user{

	/**
	 * This method updates the users first and last name.
	 *
	 * @param string $first the users first name.	 
	 * @param string $last the users last name.	 
	 * @param int $userid the user id in the database.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateUserName($first,$last,$userid){
		$dbobject = new dbobject('user');
		$dbobject->update('UserFirstName',$first);
		$dbobject->update('UserlastName',$last);	
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->commit();			
	}

	/**
	 * This method reads a user's name.
	 *
	 * @param int $userid the users id.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function readUserName($userid){
		$dbobject = new dbobject('user');
		$dbobject->read("UserFirstName");
		$dbobject->read("UserlastName");
		$dbobject->where("PK_UserID", $userid);
		return $dbobject->fetchSingle();			
	}
	
	/**
	 * This method updates the users current profile image
	 *
	 * @param string $image the name of the image.
	 * @param int $userid the users id.
	 *
	 * @return bool true on success false on failure
	 */	
	public static function updateUserImage($image,$userid){
		$dbobject = new dbobject('user');
		$dbobject->update('UserImage',$image);
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->commit();
	}
	
	/**
	 * This method finds the users current profile image and also adds the UID path to the image
	 *
	 * @param int $userid the users id.
	 *
	 * @return string The UID with the user image
	 */
	public static function readUserImage($userid){
		$dbobject = new dbobject('user');
		$dbobject->read("UID");
		$dbobject->read("UserImage");
		$dbobject->where("PK_UserID", $userid);
		$data = $dbobject->fetchSingle();
		return $data[0].'/'.$data[1];
	}	
	
	/**
	 * This method updates a users birthdate.
	 *
	 * @param string $birth the users birth (yyyy-mm-dd).	 
	 * @param int $userid the users id.	 
	 *	 
	 * @return bool true on success false on failure
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateUserBirth($birth,$userid){
		$dbobject = new dbobject('user');
		$dbobject->update('UserBirth',$birth);
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->commit();
	}	

	/**
	 * This method finds the users birthdate and uses the calendar parsedate to 
	 * format the date in the correct localised format.
	 *
	 * @param int $userid the users id.
	 *
	 * @return string The UID with the user image
	 */	
	public static function readUserBirth($userid){
		$dbobject = new dbobject('user');
		$dbobject->read("UserBirth");
		$dbobject->where("PK_UserID", $userid);
		$data = $dbobject->fetchSingle();
		return calendar::parsedate($data[0],0);
	}
	
	/**
	 * This method updates the users address.
	 *
	 * @param string $street the user street.	 
	 * @param string $number the user number.	 
	 * @param string $floor the user floor.	 
	 * @param string $door the user door.	 
	 * @param string $zipcode the user zipcode.	 
	 * @param string $city the user city.	 
	 * @param int $area a pointer to the generic_area table.	 
	 * @param string $country the users last name.	 
	 * @param int $userid the user id in the database.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateUserAddress($street,$number,$floor,$door,$zipcode,$city,$area,$country,$userid){
		$dbobject = new dbobject('user');
		$dbobject->update('UserStreet',$street);
		$dbobject->update('UserNumber',$number);
		$dbobject->update('UserFloor',$floor);
		$dbobject->update('UserDoor',$door);
		$dbobject->update('UserZip',$zipcode);
		$dbobject->update('UserCity',$city);
		$dbobject->update('UserCountry',$country);
		$dbobject->update('FK_AreaID',$area);		
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->commit();		
	}	

	/**
	 * This method reads a user's address.
	 *
	 * @param int $userid the users id.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function readUserAddress($userid){
		$dbobject = new dbobject('user');
		$dbobject->read("UserStreet");
		$dbobject->read("UserNumber");
		$dbobject->read("UserFloor");
		$dbobject->read("UserDoor");
		$dbobject->read("UserZip");
		$dbobject->read("UserCity");
		$dbobject->read("UserCountry");
		$dbobject->read("FK_AreaID");
		$dbobject->where("PK_UserID", $userid);
		return $dbobject->fetchSingle();		
	}	
	
	/**
	 * This method updates the users contact information.
	 *
	 * @param string $phone the user street.	 
	 * @param string $cell the user number.	 
	 * @param string $fax the user floor.	 
	 * @param string $mail the user door.
	 * @param int $userid the user id in the database.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateUserContact($phone,$cell,$fax,$mail,$userid){
		$dbobject = new dbobject('user');
		$dbobject->update('UserPhone',$phone);
		$dbobject->update('UserCell',$cell);
		$dbobject->update('UserFax',$fax);
		$dbobject->update('UserEMail',$mail);
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->commit();
	}

	/**
	 * This method reads a user's contact information.
	 *
	 * @param int $userid the users id.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function readUserContact($userid){
		$dbobject = new dbobject('user');
		$dbobject->read("UserPhone");
		$dbobject->read("UserCell");
		$dbobject->read("UserFax");
		$dbobject->read("UserEMail");
		$dbobject->where("PK_UserID", $userid);
		return $dbobject->fetchSingle();			
	}		
	
	/**
	 * This method updates the users settings.
	 *
	 * @param string $acceptnews does the user accept news mails.	 
	 * @param string $acceptmails does the user accept system mails.	 
	 * @param int $userid the user id in the database.	 
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function updateUserSettings($acceptnews,$acceptmails,$userid){
		$dbobject = new dbobject('user');
		$dbobject->update('AcceptNews',$acceptnews);
		$dbobject->update('AcceptMails',$acceptmails);
		$dbobject->where("PK_UserID",$userid);
		return $dbobject->commit();		
	}

	/**
	 * This method reads a user's settings.
	 *
	 * @param int $userid the users id.	 
	 *	 
	 * @return array The table row found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public static function readUserSettings($userid){
		$dbobject = new dbobject('user');
		$dbobject->read("AcceptNews");
		$dbobject->read("AcceptMails");
		$dbobject->where("PK_UserID", $userid);
		return $dbobject->fetchSingle();			
	}			
}
?>