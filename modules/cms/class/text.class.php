<?php
/**
 * This class handles text administration.
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
 * @package		text
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'database.class.php'
 */
 
class text{
	
	/**
     * This method creates a text entry in the database.
	 *
	 * @param string $headline Text headline.
	 * @param string $body The main text.
	 * @param string $key The key to the text.
	 * @param string $language The language of the text.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function createText($headline,$body,$key,$language = STD_LANGUAGE){
		$database = new database('cms_text');
		$data = array("Headline" => "'".$headline."'",
			"BodyText" => "'".$body."'",
			"TextKey" => "'".$key."'",
			"Language" => "'".$language."'");			
		if(!$database->create($data)){
			return false;
		}	
	}

	/**
     * This method finds the newest entry in the text table.
	 *
	 * @return int/bool id on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function findlast(){
		$database = new database('cms_text');
		list($id) = $database->readLastEntry();
		return $id;
	}
	
	/**
     * This method updates a text with all the current parameters.
	 *
	 * @param int $textid The id for the row in the table.
	 * @param string $key key to the article.
	 * @param string $headline Text headline.
	 * @param string $body The main text.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function updateText($textid,$key,$headline,$body,$language = STD_LANGUAGE){
		$database = new database('cms_text');
		$data = array("Headline" => "'".$headline."'",
			"Headline" => "'".$headline."'",
			"BodyText" => "'".$body."'",
			"TextKey" => "'".$key."'",
			"Language" => "'".$language."'");
		return $database->update($data,"PK_TextID = ".$textid);
	}	
	
	/**
     * This method reads and returns the text table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listText(){
		$database = new database('cms_text');
		return $database->read("PK_TextID,TextKey,Headline,Language,CreateDate","Language = '".$_SESSION['CountryCode']."'","Headline");
	}	
	
	/**
     * This method finds all the information relating to the id given.
	 * It also replaces the '../' path with the proper http path.
	 *
	 * @param int $textid The id to the table to the table.
	 *
	 * @return array The table row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function readText($textid){
		$database = new database('cms_text');
		list($id,$key,$headline,$bodytext,$language) = $database->readSingle("PK_TextID,TextKey,Headline,BodyText,Language","PK_TextID = ".$textid." AND Language = '".$_SESSION['CountryCode']."'");
		$bodytext = str_replace('../','/',$bodytext);
		if(!$language){
			$language = $_SESSION['CountryCode'];
		}
		return array($id,$key,$headline,$bodytext,$language);
	}

	/**
     * This method finds all the information relating to the given key.
	 * It also replaces the '../' path with the proper http path.
	 *
	 * @param string $TextKey The key to the table.
	 *
	 * @return array The table row found on success or false.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function readTextByKey($key){
		if(!$key){
			return false;
		}	
		$language = (isset($_SESSION['CountryCode'])) ? $_SESSION['CountryCode'] : STD_LANGUAGE;
		$database = new database('cms_text');
		if($bodytext = $database->readSingle("BodyText","TextKey = '".$key."' AND Language = '".$language."'")){
			list($bodytext) = $bodytext;
			return $bodytext;
		}
		return false;
	}
	
	/**
     * This method deletes a text entry in the database.
	 *
	 * @param int $textid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyText($textid){
		$database = new database('cms_text');
		if(!$database->destroy("PK_TextID = ".$textid)){
			return false;
		}
		return true;
	}
}
?>