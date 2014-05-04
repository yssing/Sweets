<?php
/**
 * This class handles news or blog administration.
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
 * @package		news/blog
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'database.class.php'
 */

class news {

	/**
     * This method creates a new row in the news table.
	 *
	 * @param string $headline the headline.
	 * @param string $teaser The teaser content of the news.
	 * @param string $body The full content of the news.
	 * @param datetime $online guess what :)
	 * @param datetime $offline guess what :)
	 * @param string $icon if any picture is being used
	 * @param int $sticky does the news stay on top.
	 *
	 * @return bool true on success or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function createNews($headline,$teaser,$body,$online,$offline,$icon,$sticky){
		$database = new database();
		$data = array("Headline" => "'".$headline."'",
			"Teaser" => "'".$teaser."'",
			"BodyText" => "'".$body."'",
			"OnlineDate" => "'".$online."'",
			"OfflineDate" => "'".$offline."'",
			"Icon" => "'".$icon."'",
			"Sticky" => $sticky,
			"Language" => "'".$_SESSION['CountryCode']."'");
		if(!$database->create('cms_news',$data)){
			return false;
		}
		return true;
	}
		
	/**
	 * finds the latest news item.
	 */
	public static function findlast(){
		$database = new database();
		list($id) = $database->readLastEntry('cms_news');
		return $id;
	}	
	
	/**
     * This method updates a news item with all the current parameters.
	 *
	 * @param int $newsid the news item.
  	 * @param string $headline the headline.
	 * @param string $teaser The content of the news.
	 * @param string $body The complete content of the news.
	 * @param datetime $online guess what :)
	 * @param datetime $offline guess what :)
	 * @param string $icon if any picture is being used
	 * @param int $sticky does the news stay on top.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function updateNews($newsid,$headline,$teaser,$body,$online,$offline,$icon,$sticky){
		$database = new database();
		$data = array("Headline" => "'".$headline."'",
			"Teaser" => "'".$teaser."'",
			"BodyText" => "'".$body."'",
			"OnlineDate" => "'".$online."'",
			"OfflineDate" => "'".$offline."'",
			"Icon" => "'".$icon."'",
			"Sticky" => $sticky,
			"Language" => "'".$_SESSION['CountryCode']."'");
		return $database->update("cms_news",$data,"PK_NewsID = ".$newsid);
	}

	/**
     * This method reads and returns the news table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function listNews(){
		$database = new database();
		return $database->read("cms_news","PK_NewsID,Headline,Teaser","Language = '".$_SESSION['CountryCode']."'");
	}
	
	/**
     * This method reads and returns the news table in a manner that is suited for the RSS feeder
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function listRssNews(){
		$return = array();
		$database = new database();
		$result = $database->readAssociative("cms_news","PK_NewsID,Headline,Teaser,Icon,FK_UserID,CreateDate","Language = '".$_SESSION['CountryCode']."'","PK_NewsID",10);
		foreach($result as $row){
			$line = array();
			foreach($row as $key => $value){
				switch($key){
					case 'FK_UserID':
						list($a,$first,$last) = user::readUser($value);
						$line['FK_UserID'] = $first.' '.$last;					
					break;
					case 'CreateDate':
						$line['CreateDate'] = calendar::parsedate($value);
					break;
					default:
						$line[$key] = $value;
					break;
				}
			}
			$return[] = $line;
		}
		return $return;
	}	
	
	/**
     * This method finds news items and organise them in an associative array.
	 *
	 * @param int $from Where to start the view.
	 * @param int $to Where to stop the view.
	 *
	 * @return items The news found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function listUserNews($from = 0,$to = 5){
		$return = array();
		$database = new database();
		$result = $database->readAssociative("cms_news","PK_NewsID,Headline,Teaser,BodyText,Icon,FK_UserID,CreateDate","Language = '".$_SESSION['CountryCode']."'","PK_NewsID",$from.",".$to);
		foreach($result as $row){
			$line = array();
			foreach($row as $key => $value){
				switch($key){
					case 'PK_NewsID':
						$line['PK_NewsID'] = $value;
					break;
					case 'FK_UserID':
						list($a,$first,$last) = user::readUser($value);
						$line['FK_UserID'] = $first.' '.$last;					
					break;
					case 'CreateDate':
						$line['CreateDate'] = calendar::parsedate($value);
					break;
					case 'BodyText':
						$line['BodyText'] = '<a href="/cms/news/'.$line['PK_NewsID'].'">['.language::readType('READMORE').']</a>';
					break;
					default:
						$line[$key] = $value;
					break;
				}
			}
			$return[] = $line;
		}
		return $return;
	}	
	
	/**
     * This method finds all the information relating to the id given.
	 *
	 * @param int $newsid The private key to the table.
	 *
	 * @return array The table row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function readUserNews($newsid){
		$return = array();
		$database = new database();
		$result = $database->readSingleAssociative("cms_news","PK_NewsID,Headline,Teaser,BodyText,OnlineDate,OfflineDate,Icon,Sticky,CreateDate,FK_UserID","PK_NewsID = ".$newsid." AND Language = '".$_SESSION['CountryCode']."'");
		
		foreach($result as $row){
			$line = array();
			foreach($row as $key => $value){
				switch($key){
					case 'PK_NewsID':
						$line['PK_NewsID'] = $value;
					break;
					case 'FK_UserID':
						list($a,$first,$last) = user::readUser($value);
						$line['FK_UserID'] = $first.' '.$last;					
					break;
					case 'CreateDate':
						$line['CreateDate'] = calendar::parsedate($value);
					break;
					default:
						$line[$key] = $value;
					break;
				}
			}
			$return[] = $line;
		}
		return $return;
	}
	
	/**
	 * the same as above, but with out the associative array
	 */
	public static function readNews($newsid){
		$database = new database();
		return $database->readSingle("cms_news","PK_NewsID,Headline,Teaser,BodyText,OnlineDate,OfflineDate,icon,Sticky,CreateDate,FK_UserID","PK_NewsID = ".$newsid." AND Language = '".$_SESSION['CountryCode']."'");	
	}	
	
	/**
     * This method deletes a news entry in the database.
	 *
	 * @param int $newsid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyNews($newsid){
		$database = new database();
		if(!$database->destroy("cms_news","PK_NewsID = ".$newsid)){
			return false;
		}
		return true;
	}
}
?>