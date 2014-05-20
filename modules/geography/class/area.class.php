<?php
/**
 * This class handles area .
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

class area{
	/**
	 * This method will search for a given area name given its id
	 *
	 * @param int $areaid 
	 *
	 * @return string $areaname on success or false on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public static function readSingleArea($areaid){
		$database = new database("geography_area");
		return $database->readSingle("AreaName,AreaCode,FK_ParentID","PK_AreaID = ".$areaid);
	}
	
	/**
     * This method reads and returns the area table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function listAreas(){
		$database = new database("geography_area");
		return $database->read("PK_AreaID,FK_ParentID,AreaName,AreaCode","","AreaName");
	}
	
	/**
	 * finds the newest item.
	 */
	public static function findlast(){
		$database = new database("geography_area");
		list($id) = $database->readLastEntry();
		return $id;
	}
	
	/**
     * This method updates a area item with all the current parameters.
	 *
	 * @param int $areaid the news item.
  	 * @param string $area the name of the area.
  	 * @param int $areacode the code to the area.
	 * @param int $parentid The parent id.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function updateArea($areaid,$area,$areacode,$parentid){
		$database = new database("geography_area");
		$data = array("AreaName" => "'".$area."'","AreaCode" => $areacode,"FK_ParentID" => $parentid);
		return $database->update($data,"PK_AreaID = ".$areaid);
	}	
	
	/**
     * This method creates a new row in the area table.
	 *
  	 * @param string $area the name of the area.
	 * @param int $areacode The code of the area.
	 * @param int $parentid The parent id.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function createArea($area,$areacode,$parentid){
		$database = new database("geography_area");
		$data = array("AreaName" => "'".$area."'","AreaCode" => $areacode,"FK_ParentID" => $parentid);
		if(!$database->create($data)){
			return false;
		}
		return true;
	}	
	
	/**
     * This method deletes an area entry in the database.
	 *
	 * @param int $areaid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyArea($areaid){
		$database = new database("geography_area");
		if(!$database->destroy("PK_AreaID = ".$areaid)){
			return false;
		}
		return true;
	}
}
?>