<?php
/**
 * This class handles zipcode .
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
 * @package    	postalcode
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 */

class zipcode{

	/**
	 * This method will search for a given city name given its postalcode
	 *
	 * @param int $zipcode 
	 *
	 * @return string $cityname on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function readSingleZipcode($zipcode){
		$database = new database("geography_zipcode");
		list($cityname) = $database->readSingle("City","Zipcode = ".$zipcode);
		return $cityname;	
	}

	/**
	 * This method will search for a given city's information given its id
	 *
	 * @param int $id 
	 *
	 * @return string $cityname on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function readZipcode($id){
		$database = new database("geography_zipcode");
		return $database->readSingle("FK_AreaID,FK_MunicipalityID,Zipcode,City","PK_ZipcodeID = ".$id);
	}
	
	/**
     * This method updates a area item with all the current parameters.
	 *
	 * @param int $id the rows id.
	 * @param int $area the areaid.
	 * @param int $municipality The teaser content of the news.
	 * @param string $zipcode The zipcode.
	 * @param string $city guess what :)
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function updateZipcode($id,$area,$municipality,$zipcode,$city){
		$database = new database("geography_zipcode");
		$data = array("FK_AreaID" => $area,
			"FK_MunicipalityID" => $municipality,
			"Zipcode" => "'".$zipcode."'",
			"City" => "'".$city."'");
		return $database->update($data,"PK_ZipcodeID = ".$id);
	}
	
	/**
     * This method creates a new row in the zipcode table.
	 *
	 * @param int $area the areaid.
	 * @param int $municipality The teaser content of the news.
	 * @param string $zipcode The zipcode.
	 * @param string $city guess what :)
	 *
	 * @return bool true on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function createNews($area,$municipality,$zipcode,$city){
		$database = new database("geography_zipcode");
		$data = array("FK_AreaID" => $area,
			"FK_MunicipalityID" => $municipality,
			"Zipcode" => "'".$zipcode."'",
			"City" => "'".$city."'");
		if(!$database->create($data)){
			return false;
		}
		return true;
	}
	
	/**
     * This method reads and returns the area table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listZipcodes(){
		$database = new database("geography_zipcode");
		return $database->read("PK_ZipcodeID,FK_MunicipalityID,FK_AreaID,Zipcode,City","","City");
	}
}
?>