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
		$dbobject = new dbobject('geography_municipality');
		$dbobject->read("Municipality");
		$dbobject->read("MunicipalityCode");
		$dbobject->where("PK_MunicipalityID",$municipalityid);
		return $dbobject->fetchSingle();		
	}

	/**
     * This method reads and returns the municipality table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listMunicipalities($searchval = ''){
		$dbobject = new dbobject('geography_municipality');
		$dbobject->read("PK_MunicipalityID");
		$dbobject->read("Municipality");
		$dbobject->read("MunicipalityCode");
		if ($searchval){
			$dbobject->wildcard("Municipality",$searchval);
			$dbobject->wildcard("MunicipalityCode",$searchval);
		}			
		$dbobject->orderby("Municipality");
		return $dbobject->fetch();			
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
		$dbobject = new dbobject('geography_municipality');
		$dbobject->update('Municipality',$municipality);
		$dbobject->update('MunicipalityCode',$municipalitycode);
		$dbobject->where("PK_MunicipalityID", $municipalityid);
		return $dbobject->commit();		
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
		$dbobject = new dbobject('geography_municipality');
		$dbobject->create('Municipality',$municipality);
		$dbobject->create('MunicipalityCode',$municipalitycode);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;			
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
		$dbobject = new dbobject('geography_municipality');
		$dbobject->destroy();
		$dbobject->where("PK_MunicipalityID",$municipalityid);
		return $dbobject->commit();			
	}
}
?>