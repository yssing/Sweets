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
 */

class language {
	
	/**
	 * This method set the language session
	 *
	 * @param string $value the value of the language code
	 */
	public static function set($value){
		$_SESSION['countryCode'] = $value;
	}

	/**
	 * This method returns the language session, if the session is set.
	 *
	 * @param string/bool the value of the language code or false on failure
	 */	
	public static function get(){
		if (isset($_SESSION['countryCode'])){
			return $_SESSION['countryCode'];		
		}
		return false;
	}
	
	public static function countLanguages(){
		$dbobject = new dbobject("generic_language");
		$dbobject->distinct("Language");
		$data = $dbobject->fetch();
		return sizeof($data);
	}
	
	/**
	 * This method list the flags that corresponds to the languages in the database.
	 *
	 * @return string $language the language string.
	 *
     * @access public
	 * @since Method available since 2013-11-03
     */
	public static function listFlags(){
		if (!$tmpData = caching::getKey('language_flags')){
			$lan = '';
			$dbobject = new dbobject("generic_language");
			$dbobject->distinct("Language");
			$data = $dbobject->fetch();
			if (is_array($data)){
				foreach($data as $language){
					$lan .= '<a href="'.PATH_WEB.route::getURL().'?lan='.$language[0].'" class="flagcontrol '.strtolower($language[0]).'"></a>';
				}
			}
			caching::setKey('language_flags',$lan);	
			return $lan;
		} else {
			return $tmpData;
		}	
	}	

	/**
	 * This method lists all the user entries in the database.
	 *
	 * @param String $searchval If any wildcard search is done.
	 *
	 * @return array/bool array on succes or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function listLanguage($searchval){
		$dbobject = new dbobject('generic_language');
		$dbobject->read('PK_LanguageID');
		$dbobject->read('Type');
		$dbobject->read('Language');
		$dbobject->read('String');
		if ($searchval){
			$dbobject->wildcard("Type",$searchval);
			$dbobject->wildcard("String",$searchval);
		}		
		return $dbobject->fetch();
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
	public static function createLanguage($type,$language,$string){
		$dbobject = new dbobject('generic_language');
		$dbobject->create('Type',$type);
		$dbobject->create('Language',$language);
		$dbobject->create('String',$string);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;		
	}

	/**
     * This method returns a sentence or string based on a
	 * Type and a language parameter, for what ever language
	 * needs to be displayed.
	 * If Language is not set, it will then look for the CountryCode session, and if
	 * this session is not set, the method will use the netgeo class and find the 
	 * proper language.
	 * The method will also create an array of language values in a session, so to
	 * avoid reloading the same value several times.
	 * If a language type is not found, the method will return the Type encapsulated in curly brackets {}
	 *
	 * @param string $type the table to use from the database.
	 *
	 * @return string Returns the language string found.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function readType($type){
		$countryCode = self::get();
		$dbobject = new dbobject("generic_language");
		$dbobject->read("String");
		$dbobject->where("Type",$type);
		$dbobject->where("Language",self::get());
		
		list($language) = $dbobject->fetchSingle();
		if (!$language){
			$language = '{'.$type.'}';
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
		$dbobject = new dbobject("generic_language");
		$dbobject->read("PK_LanguageID");
		$dbobject->read("Type");
		$dbobject->read("Language");
		$dbobject->read("String");
		$dbobject->where("PK_LanguageID",$languageid);		
		return $dbobject->fetchSingle();		
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
	public static function updateLanguage($languageid,$type,$language,$string){
		$dbobject = new dbobject('generic_language');
		$dbobject->update('Type',$type);
		$dbobject->update('Language',$language);
		$dbobject->update('String',$string);
		$dbobject->where("PK_LanguageID",$languageid);
		return $dbobject->commit();		
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
		$dbobject = new dbobject('generic_language');
		$dbobject->destroy();
		$dbobject->where("PK_LanguageID",$languageid);
		return $dbobject->commit();
	}
} 
?>