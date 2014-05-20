<?php
/**
 * This class handles the user view of the language class
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
 * @category   	CMS methods
 * @package		language
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'language.class.php'
 */

class userlanguage{

	/**
     * This method changes the language session
	 *
	 * @param string $language the table to use from the database.
	 *
	 * @return string $language the language string.
	 *
     * @access public
	 * @since Method available since 2013-11-03
     */		
	public static function setLanguage($language){
		if(!isset($_SESSION['CountryCode'])){
			netgeo::getNetGeo();
			$language = (netgeo::$CountryCode) ? netgeo::$CountryCode : STD_LANGUAGE;
			$_SESSION['CountryCode'] = strval($language);
		} else {
			$_SESSION['CountryCode'] = $language;
		}
		return $language;
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
		$database = new database("generic_language");
		$lan = '';
		foreach($database->read("DISTINCT Language") as $language){
			$lan .= '<a href="'.PATH_WEB.route::getURL().'?lan='.$language[0].'" class="flagcontrol '.strtolower($language[0]).'"></a>';
		}
		return $lan;
	}
}
?>