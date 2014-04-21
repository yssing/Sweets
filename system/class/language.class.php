<?php
/**
 * This class handles all the language settings and displaying.
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
 * @package    	language
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'database.class.php' 
 */
class language/* extends database*/{

	/**
	 * This method lists all the user entries in the database.
	 *	 
	 * @return array/bool array on succes or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function listLanguage(){
		$database = new database();
		return $database->read("generic_language","PK_LanguageID,Type,Language,String","","Type, Language");
	}
	
	/**
     * This function creates a language Type in the database.
	 *
	 * This method is used mainly for adding new languages to the system.
	 * The new entry can bind to an existing Type or a new Type.
	 *
	 * @param string $Type Is used to tell what text label it relates to.
	 * @param string $Language Holds the language code eg. 'DK' or 'DE'. 
	 * @param string $String Holds the actual text to insert.
	 *
	 * @return TRUE on successfull entry or FALSE on failure or if parameter is missing.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function createLanguage($Type,$Language,$String){
		if(!$Type || !$Language || !$String){
			return false;
		}
		$database = new database();
		$data = array("Type" => "'".$Type."'","Language" => "'".$Language."'","String" => "'".$String."'");	

		if(!$database->create('generic_language',$data)){
			return false;
		}		
		return true;		
	}

	/**
     * This method returns a sentence or string based on a
	 * Type and a language parameter, for what language
	 * needs to be displayed.
	 * If Language is not set, it will then look for the CountryCode session, and if
	 * this session is not set, the method will use the netgeo class and find the 
	 * proper language.
	 * The method will also create an array of language values in a session, so to
	 * avoid reloading the same value several times.
	 *
	 * @param string $Type the table to use from the database.
	 *
	 * @return string Returns the language string found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function readType($Type){
		$database = new database();
		if(!isset($_SESSION['CountryCode'])){
			netgeo::getNetGeo();
			$Language = netgeo::$CountryCode;
			$_SESSION['CountryCode'] = strval($Language);
		} else {
			$Language = $_SESSION['CountryCode'];
		}
		if(!isset($_SESSION['LanguageVars'][$Type])){
			list($language) = $database->readSingle("generic_language","String",array("Type = '".$Type."'","Language = '".$Language."'"));
			if($language){
				$_SESSION['LanguageVars'][$Type] = $language;
			} else {
				$language = $Type;
			}
		} else {
			$language = $_SESSION['LanguageVars'][$Type];
		}
		return $language;
	}
	
	/**
     * This method finds all the information of an single language entry relating to the id given.
	 *
	 * @param int $languageid The private Type to the table.
	 *
	 * @return array The table row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function readLanguage($languageid){
		$database = new database();
		return $database->readSingle("generic_language","PK_LanguageID,Type,Language,String","PK_LanguageID = ".$languageid);
	}

	/**
     * This method updates a language entry with all the current parameters.
	 *
	 * @param int $languageid The id for the row in the table.
	 * @param string $Type The string Type.
	 * @param string $Language The language Type.
	 * @param string $String The actual string.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function updateLanguage($languageid,$Type,$Language,$String){
		$database = new database();
		$data = array("Type" => "'".$Type."'","Language" => "'".$Language."'","String" => "'".$String."'");	
		return $database->update("generic_language",$data,"PK_LanguageID = ".$languageid);		
	}
	
	/**
     * This method finds the newest entry in the language table.
	 *
	 * @return int/bool id on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function findlast(){
		$database = new database();
		list($id) = $database->readLastEntry('generic_language');
		return $id;
	}	
	
	/**
     * This method deletes an entry in the language table.
	 *
	 * Be carefull with the parsed parameters, if one of them is empty the method
	 * will delete several rows of data.
	 * This can be used to remove an entire language or all the entries
	 * to a specific Type.
	 *
	 * @param string $Type refers to the Type, which can have several languages attached.
	 * @param string $language the specific language Type to delete.
	 *
	 * @return TRUE on successful entry or FALSE on failure or if parameter is missing.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyLanguage($languageid){
		if(!$languageid){
			return false;
		} 
		$database = new database();
		if(!$database->destroy("generic_language", "PK_LanguageID = ".$languageid)){
			return false;
		}
		return true;
	}
} 
?> 