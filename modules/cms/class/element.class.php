<?php
/**
 * This class handles element manipulation.
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
 * @package		element
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'database.class.php'
 */

class element{
	
	/**
     * This method creates a new row in the element table.
	 *
	 * @param string $path What path to edit.
	 * @param string $body The content of the element.
	 * @param string $element What element to use.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function createElement($path,$body,$element){	
		$database = new database();
		$data = array("Path" => "'".$path."'",
			"BodyText" => "'".$body."'",
			"Element" => "'".$element."'",
			"Language" => "'".$_SESSION['CountryCode']."'");
		if(!$database->create('cms_element',$data)){
			return false;
		}
		return true;
	}	
	
	/**
     * This method checks if a given element exists in the database.
	 *
	 * @param string $path What path to edit.
	 * @param string $element What element to use.
	 *
	 * @return int The id matching the row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function doesExist($path,$element){
		$database = new database();
		list($id) = $database->readSingle("cms_element","PK_ElementID","Path = '".$path."' AND Element = '".$element."' AND Language = '".$_SESSION['CountryCode']."'");
		return $id;
	}
	
	/**
     * This method updates an element with all the current parameters.
	 *
	 * @param string $path What path to edit.
	 * @param string $body The content of the element.
	 * @param string $element What element to use.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function updateElementPath($path,$body,$element){
		$database = new database();
		$data = array("BodyText" => "'".$body."'");	
		return $database->update("cms_element",$data,"Path = '".$path."' AND Element = '".$element."' AND Language = '".$_SESSION['CountryCode']."'");
	}	
	
	/**
     * This method updates an element.
	 *
	 * @param string $id What path to edit.
	 * @param string $body The content of the element.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function updateElement($id,$body){
		$database = new database();
		$data = array("BodyText" => "'".$body."'");
		return $database->update("cms_element",$data,"PK_ElementID = ".$id);
	}

	/**
     * This method finds all the information relating to the path given.
	 *
	 * @param string $path What path to edit.
	 * @param string $element What element to use, defaults to '/'.
	 * @param string $alternative If nothing found in DB, then use this string.
	 *
	 * @return array The table row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function readElementPath($path,$element = '/',$alternative = ''){
		$database = new database();
		$tmp = $database->readSingle("cms_element","BodyText","Path = '".$path."' AND Element = '".$element."' AND Language = '".$_SESSION['CountryCode']."'");
		if(sizeof($tmp) > 0){
			list($text) = $tmp;
			return '<label>'.$text.'</label>';
		} else {
			return $alternative;
		}
	}
	
	/**
     * This method finds all the information relating to the path id.
	 *
	 * @param int $id What id to edit.
	 *
	 * @return array The table row found.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function readElement($id){
		$database = new database();
		return $database->readSingle("cms_element","PK_ElementID, BodyText","PK_ElementID = ".$id);
	}	

	/**
     * This method reads and returns the news table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function listElements(){
		$database = new database();
		return $database->read("cms_element","PK_ElementID, Path, Element","Language = '".$_SESSION['CountryCode']."'");
	}	
	
	/**
     * This method deletes an element entry in the database.
	 *
	 * @param int $elementid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyElement($elementid){
		$database = new database();
		if(!$database->destroy("cms_element","PK_ElementID = ".$elementid)){
			return false;
		}
		return true;
	}	
}
?>