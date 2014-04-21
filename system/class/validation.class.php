<?php
/**
 * This class handles validations of input fields.
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
 * @package    	validation
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'generic.io.class.php'
 */

class validation{

	/**
	 * This method checks if a string patterns matches that of an e-mail.
	 * It uses the php regexp to validate the pattern.
	 *
	 * @param string $email the string to be checked.  
	 *
	 * @return bool Returns TRUE if it matches an e-mail or FALSE if not.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function isEMail($email){
		$email = trim($email);
		if (strlen($email) >= 1 ){
			if(preg_match("/^[a-zA-Z]\w+(\.\w+)*\@\w+(\.[0-9a-zA-Z]+)*\.[a-zA-Z]{2,4}$/", $email) == false){
				return false;
			} else {
				return true;
			}	
		}
		return true;
	}	
	
	/**
	 * This method trims a string then checks for any content.
	 *
	 * @param string $string the string to be checked.  
	 *
	 * @return bool Returns TRUE if string is empty or FALSE if not.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function isEmpty($string){
		if(sizeof(trim($string))){
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * This method trims a string then uses is_numeric
	 * to test if the string is a numeric value
	 *
	 * @param string $string the string to be checked.  
	 *
	 * @return bool Returns TRUE if string is a number or FALSE if not.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function isNumber($string){
		if(is_numeric(trim($string))){
			return true;
		} else {
			return false;
		}
	}	
}	
?>