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
     * This method creates a revision entry in the database.
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
		$dbobject = new dbobject('cms_text_revision');
		$dbobject->create('Headline',$headline);
		$dbobject->create('BodyText',$body);	
		$dbobject->create('TextKey',$key);	
		$dbobject->create('Language',$language);	
		$dbobject->create('FK_TextID',$textid);
		if ($dbobject->commit()){
			return true;
		} 
		return false;		
		
	}
	
	/**
     * This method reads a single revision.
	 *
	 * @param int $revisionid The key to the revision
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function readRevision($revisionid){
		$dbobject = new dbobject('cms_text_revision');
		$dbobject->read("Headline");
		$dbobject->read("BodyText");
		$dbobject->read("CreateDate");
		$dbobject->read("FK_TextID");
		$dbobject->where("PK_TextRevisionID",$revisionid);
		return $dbobject->fetchSingle();		
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
		$dbobject = new dbobject('cms_text_revision');
		$dbobject->short('ctr');
		$dbobject->join("user","PK_UserID","FK_UserID");
		$dbobject->read("PK_TextRevisionID");
		$dbobject->read("UserLogin");
		$dbobject->read(TPREP."ctr.Headline");
		$dbobject->read(TPREP."ctr.Language");
		$dbobject->read(TPREP."ctr.CreateDate");
		$dbobject->where("FK_TextID",$textid);
		$dbobject->orderby('PK_TextRevisionID','DESC');
		return $dbobject->fetch();		
	}	
	
	/**
     * This method deletes a single revision
	 *
	 * @param int $revid The key to the revision.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 23-01-2017	
     */		
	public static function destroyRevision($revid){
		$dbobject = new dbobject('cms_text_revision');
		$dbobject->destroy();
		$dbobject->where("PK_TextRevisionID",$revid);
		return $dbobject->commit();		
	}	
	
	/**
     * This method deletes all revisions related to a text
	 *
	 * @param int $textid The foreign key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0	
     */		
	public static function destroyRevisions($textid){
		$dbobject = new dbobject('cms_text_revision');
		$dbobject->destroy();
		$dbobject->where("FK_TextID",$textid);
		return $dbobject->commit();		
	}	
}
?>