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
		$database = new database();
		$data = array("UserFirstName" => "'".$first."'","UserlastName" => "'".$last."'");
		$where = 'PK_UserID = '.$userid;
		if(!$database->update('user',$data,$where)){
			return false;
		}		
		return true;
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
		$database = new database();
		$data = array(
			"UserStreet" => "'".$street."'",
			"UserNumber" => "'".$number."'",
			"UserFloor" => "'".$floor."'",
			"UserDoor" => "'".$door."'",
			"UserZip" => "'".$zipcode."'",
			"UserCity" => "'".$city."'",
			"UserCountry" => "'".$country."'",
			"FK_AreaID" => $area);
		
		$where = 'PK_UserID = '.$userid;
		if(!$database->update('user',$data,$where)){
			return false;
		}		
		return true;
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
		$database = new database();
		$data = array(
			"UserPhone" => "'".$phone."'",
			"UserCell" => "'".$cell."'",
			"UserFax" => "'".$fax."'",
			"UserEMail" => "'".$mail."'");
		
		$where = 'PK_UserID = '.$userid;
		if(!$database->update('user',$data,$where)){
			return false;
		}		
		return true;
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
		$database = new database();
		$data = array(
		"AcceptNews" => $acceptnews,
		"AcceptMails" => $acceptmails);
		
		$where = 'PK_UserID = '.$userid;
		if(!$database->update('user',$data,$where)){
			return false;
		}		
		return true;
	}
}
?>