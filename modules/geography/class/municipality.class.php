<?php
/**
 * This class handles municipalities.
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
 * @category   	Geography system methods
 * @package    	Municipality
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 */

class municipality{
	/**
	 * This method will search for a given municipality name given its id
	 *
	 * @param int $municipalityid 
	 *
	 * @return string $areaname on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 1.0.0
	 */		
	public static function readSingleMunicipality($municipalityid){
		$database = new database();
		return $database->readSingle("geography_municipality","Municipality,MunicipalityCode","PK_MunicipalityID = ".$municipalityid);	
	}

	/**
     * This method reads and returns the municipality table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listMunicipalities(){
		$database = new database();
		return $database->read("geography_municipality","PK_MunicipalityID,Municipality,MunicipalityCode","","Municipality");
	}	
	
	/**
	 * finds the newest item.
	 */
	public static function findlast(){
		$database = new database();
		list($id) = $database->readLastEntry('geography_municipality');
		return $id;
	}
	
	/**
     * This method updates a municipality item with all the current parameters.
	 *
	 * @param int $municipalityid the news item.
  	 * @param string $municipality the name of the municipality.
	 * @param string $municipalitycode The code of the municipality.
	 *
	 * @return bool true on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function updateMunicipality($municipalityid,$municipality,$municipalitycode){
		$database = new database();
		$data = array("Municipality" => "'".$municipality."'","MunicipalityCode" => $municipalitycode);
		return $database->update("geography_municipality",$data,"PK_MunicipalityID = ".$municipalityid);
	}	
	
	/**
     * This method creates a new row in the municipality table.
	 *
  	 * @param string $municipality the name of the municipality.
	 * @param string $municipalitycode The code of the municipality.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function createMunicipality($municipality,$municipalitycode){
		$database = new database();
		$data = array("Municipality" => "'".$municipality."'","MunicipalityCode" => $municipalitycode);
		if(!$database->create('geography_municipality',$data)){
			return false;
		}
		return true;
	}
	
	/**
     * This method deletes a municipality entry in the database.
	 *
	 * @param int $municipalityid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyMunicipality($municipalityid){
		$database = new database();
		if(!$database->destroy("geography_municipality","PK_MunicipalityID = ".$municipalityid)){
			return false;
		}
		return true;
	}
}
?>