<?php
/**
 * This class handles country .
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
 * @package    	Geography
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 */

class country{
	/**
	 * This method will search for a given country name given its id
	 *
	 * @param int $countryid 
	 *
	 * @return string $countryname on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public static function readSingleCountry($countryid){
		$dbobject = new dbobject('geography_country');
		$dbobject->read("countryName");
		$dbobject->read("countryCode");
		$dbobject->where("PK_countryID",$countryid);
		return $dbobject->fetchSingle();		
	}
	
	/**
     * This method reads and returns the country table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function listCountries($searchval = ''){
		$dbobject = new dbobject('geography_country');
		$dbobject->read("PK_countryID");
		$dbobject->read("countryName");
		$dbobject->read("countryCode");
		if ($searchval){
			$dbobject->wildcard("countryName",$searchval);
			$dbobject->wildcard("countryCode",$searchval);
		}		
		$dbobject->orderby("countryName");
		return $dbobject->fetch();		
	}
	
	public static function listDropDown($usename = 0){		
		$dbobject = new dbobject('geography_country');
		if($usename){
			$dbobject->read("countryName");
		} else {
			$dbobject->read("PK_countryID");			
		}
		$dbobject->read("countryName");
		$dbobject->orderby("countryName");
		return $dbobject->fetch();
	}		
	
	/**
     * This method updates a country item with all the current parameters.
	 *
	 * @param int $countryid the news item.
  	 * @param string $country the name of the country.
  	 * @param int $countrycode the code to the country.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function updateCountry($countryid,$country,$countrycode){
		$dbobject = new dbobject('geography_country');
		$dbobject->update('countryName',$country);
		$dbobject->update('countryCode',$countrycode);
		$dbobject->where("PK_countryID", $countryid);
		return $dbobject->commit();
	}	
	
	/**
     * This method creates a new row in the country table.
	 *
  	 * @param string $country the name of the country.
	 * @param int $countrycode The code of the country.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function createCountry($country,$countrycode){
		$dbobject = new dbobject('geography_country');
		$dbobject->create('countryName',$country);
		$dbobject->create('countryCode',$countrycode);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;			
	}	
	
	/**
     * This method deletes an country entry in the database.
	 *
	 * @param int $countryid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyCountry($countryid){
		$dbobject = new dbobject('geography_country');
		$dbobject->destroy();
		$dbobject->where("PK_countryID",$countryid);
		return $dbobject->commit();			
	}
}
?>