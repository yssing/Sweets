<?php
/**
 * This class handles gallery in ePhoto.
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
 * @since      	File available since Release 25-11-2015
 */

class ephotoGallery{

	/**
	 * This method will search for a given gallery given its name.
	 *
	 * @param string $name 
	 *
	 * @return string $cityname on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 25-11-2015
	 */		
	public static function readSingleGallery($name){
		$dbobject = new dbobject('ephoto_gallery');
		$dbobject->read("name");
		$dbobject->read("imagelist");
		$dbobject->where("name",$name);
		list($gallery) = $dbobject->fetchSingle();
		return $gallery;
	}

	/**
	 * This method will search for a given gallerys's information given its id
	 *
	 * @param int $id 
	 *
	 * @return array on success or false on failure.
	 *
     * @access public	 
	 * @since Method available since Release 25-11-2015
	 */		
	public static function readGallery($id){
		$dbobject = new dbobject('ephoto_gallery');
		$dbobject->read("name");
		$dbobject->read("imagelist");
		$dbobject->where("PK_GalleryID",$id);
		return $dbobject->fetchSingle();		
	}
		
	/**
     * This method updates a gallery with all the current parameters.
	 *
	 * @param int $id the id.
	 * @param string $name
	 * @param string $imagelist
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 25-11-2015
     */		
	public static function updateGallery($id,$name,$imagelist){
		$dbobject = new dbobject('ephoto_gallery');
		$dbobject->update('name',$name);
		$dbobject->update('imagelist',$imagelist);
		$dbobject->where("PK_GalleryID", $id);
		return $dbobject->commit();			
	}
	
	/**
     * This method creates a new gallery.
	 *
	 * @param string $name
	 * @param string $imagelist
	 *
	 * @return bool true on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 25-11-2015
     */
	public static function createGallery($name,$imagelist){
		$dbobject = new dbobject('ephoto_gallery');
		$dbobject->create('name',$name);
		$dbobject->create('imagelist',$imagelist);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;			
	}
	
	public static function listDropDown(){
		$dbobject = new dbobject('ephoto_gallery');
		$dbobject->read("PK_GalleryID");
		$dbobject->read("name");
		$dbobject->orderby("name");
		return $dbobject->fetch();
	}	
	
	/**
     * This method reads and returns the gallery table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 25-11-2015
     */		
	public static function listGalleries($searchval = ''){
		$dbobject = new dbobject('ephoto_gallery');
		$dbobject->read("PK_galleryID");
		$dbobject->read("name");
		if ($searchval){
			$dbobject->wildcard("name",$searchval);
			$dbobject->wildcard("imagelist",$searchval);
		}		
		$dbobject->orderby("name");
		return $dbobject->fetch();			
	}
	
	/**
     * This method deletes a gallery in the database.
	 *
	 * @param int $galleryid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 25-11-2015
     */		
	public static function destroyGallery($galleryid){
		$dbobject = new dbobject('ephoto_gallery');
		$dbobject->destroy();
		$dbobject->where("PK_GalleryID",$galleryid);
		return $dbobject->commit();			
	}	
}
?>