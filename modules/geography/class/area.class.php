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
		$dbobject = new dbobject('geography_area');
		$dbobject->read("AreaName");
		$dbobject->read("AreaCode");
		$dbobject->read("FK_ParentID");
		$dbobject->where("PK_AreaID",$areaid);
		return $dbobject->fetchSingle();		
	}
	
	/**
     * This method reads and returns the area table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function listAreas($searchval = ''){
		$dbobject = new dbobject('geography_area');
		$dbobject->read("PK_AreaID");
		$dbobject->read("FK_ParentID");
		$dbobject->read("AreaName");
		$dbobject->read("AreaCode");
		if ($searchval){
			$dbobject->wildcard("AreaName",$searchval);
			$dbobject->wildcard("AreaCode",$searchval);
		}		
		$dbobject->orderby("AreaName");
		return $dbobject->fetch();		
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
		$dbobject = new dbobject('geography_area');
		$dbobject->update('AreaName',$area);
		$dbobject->update('AreaCode',$areacode);
		$dbobject->update('FK_ParentID',$parentid);
		$dbobject->where("PK_AreaID", $areaid);
		return $dbobject->commit();
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
		$dbobject = new dbobject('geography_area');
		$dbobject->create('AreaName',$area);
		$dbobject->create('AreaCode',$areacode);
		$dbobject->create('FK_ParentID',$parentid);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;			
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
		$dbobject = new dbobject('geography_area');
		$dbobject->destroy();
		$dbobject->where("PK_AreaID",$areaid);
		return $dbobject->commit();			
	}
}
?>