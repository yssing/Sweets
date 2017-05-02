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
		$dbobject = new dbobject('geography_zipcode');
		$dbobject->read("City");
		$dbobject->where("Zipcode",$zipcode);
		list($city) = $dbobject->fetchSingle();
		return $city;
	}

	/**
	 * This method will search for a given city's information given its id
	 *
	 * @param int $id 
	 *
	 * @return array on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function readZipcode($id){
		$dbobject = new dbobject('geography_zipcode');
		$dbobject->read("FK_AreaID");
		$dbobject->read("FK_MunicipalityID");
		$dbobject->read("Zipcode");
		$dbobject->read("City");
		$dbobject->where("PK_ZipcodeID",$id);
		return $dbobject->fetchSingle();		
	}
	
	/**
	 * This method will search for a given city's information given its zipcode
	 *
	 * @param int $id 
	 *
	 * @return array on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 2015-04-17
	 */		
	public static function readZipcodeInformation($zipcode){
		$dbobject = new dbobject('geography_zipcode');
		$dbobject->read("FK_AreaID");
		$dbobject->read("FK_MunicipalityID");
		$dbobject->read("Zipcode");
		$dbobject->read("City");
		$dbobject->where("Zipcode",$zipcode);
		return $dbobject->fetchSingle();
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
		$dbobject = new dbobject('geography_zipcode');
		$dbobject->update('FK_AreaID',$area);
		$dbobject->update('FK_MunicipalityID',$municipality);
		$dbobject->update('Zipcode',$zipcode);
		$dbobject->update('City',$city);
		$dbobject->where("PK_ZipcodeID", $id);
		return $dbobject->commit();			
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
	public static function createzipcode($area,$municipality,$zipcode,$city){
		$dbobject = new dbobject('geography_zipcode');
		$dbobject->create('FK_AreaID',$area);
		$dbobject->create('FK_MunicipalityID',$municipality);
		$dbobject->create('Zipcode',$zipcode);
		$dbobject->create('City',$city);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;			
	}
	
	/**
     * This method reads and returns the area table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listZipcodes($searchval = ''){
		$dbobject = new dbobject('geography_zipcode');
		$dbobject->read("PK_ZipcodeID");
		$dbobject->read("FK_MunicipalityID");
		$dbobject->read("FK_AreaID");
		$dbobject->read("Zipcode");
		$dbobject->read("City");
		if ($searchval){
			$dbobject->wildcard("Zipcode",$searchval);
			$dbobject->wildcard("City",$searchval);
		}		
		$dbobject->orderby("City");
		return $dbobject->fetch();			
	}
	
	/**
     * This method deletes a zipcode entry in the database.
	 *
	 * @param int $zipcodeid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyZipcode($zipcodeid){
		$dbobject = new dbobject('geography_zipcode');
		$dbobject->destroy();
		$dbobject->where("PK_ZipcodeID",$zipcodeid);
		return $dbobject->commit();			
	}	
}
?>