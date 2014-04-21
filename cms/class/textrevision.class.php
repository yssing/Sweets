<?php
/**
 * This class handles text revisions.
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

class textrevision{

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
	public static function createRevision($headline,$body,$key,$language,$textid){
		$database = new database();
		$data = array("Headline" => "'".$headline."'",
			"BodyText" => "'".$body."'",
			"TextKey" => "'".$key."'",
			"Language" => "'".$language."'",
			"FK_TextID" => $textid);
		if(!$database->create('cms_text_revision',$data)){			
			return false;
		}
		return true;
	}
	
	/**
     * This method reads and returns older revisions of the article.
	 *
	 * @param int $textid The foreign key of the text.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listRevisions($textid){
		$database = new database();
		return $database->read("cms_text_revision","TextKey,Headline,Language,CreateDate","FK_TextID = ".$textid,"Headline");
	}	
}
?>