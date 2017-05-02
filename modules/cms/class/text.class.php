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
 
class text extends template{	

	/**
     * This method creates a metadata icon list.
	 *
	 * @param integer $textid Text id.
	 *
	 * @return string result on success.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function getMetaData($textid){
		$return = '';
		if ($meta = textmeta::readMetaData($textid)){
			$return .= '<div class="metaicons">';
			list($speech,$mail,$pdf,$print) = $meta;
			if ($speech){
				$return .= '<a href="javascript:speechtext('.$textid.')" data-placement="bottom" data-toggle="tooltip" data-original-title="'.language::readType('TEXT_TO_SPEECH').'"><img src="/template/'.template::getTheme().'/icon/speak.png" border="0"></a>';
			}
			if ($pdf){
				$return .= '<a href="/modules/cms/pdf/export/'.$textid.'" target="_blank" data-placement="bottom" data-toggle="tooltip" data-original-title="'.language::readType('TEXT_TO_PDF').'"><img src="/template/'.template::getTheme().'/icon/pdf.png" border="0"></a>';
			}
			if ($mail){
				$return .= '<a href="javascript:mailtext('.$textid.')" data-placement="bottom" data-toggle="tooltip" data-original-title="'.language::readType('MAIL_TEXT').'"><img src="/template/'.template::getTheme().'/icon/mail.png" border="0"></a>';
			}
			if ($print){
				$return .= '<a href="javascript:printDiv(\'text_'.$textid.'\')" data-placement="bottom" data-toggle="tooltip" data-original-title="'.language::readType('PRINT_TEXT').'"><img src="/template/'.template::getTheme().'/icon/print.png" border="0"></a>';
			}
			$return .= '</div>';
		}
		return $return;
	}
	
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
	public static function createText($key,$headline,$body,$language = STD_LANGUAGE){
		$dbobject = new dbobject('cms_text');
		$dbobject->create('TextKey',$key);
		$dbobject->create('Headline',$headline);
		$dbobject->create('BodyText',$body);
		$dbobject->create('Language',$language);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;
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
		$dbobject = new dbobject('cms_text');
		$dbobject->update('TextKey',$key);
		$dbobject->update('Headline',$headline);
		$dbobject->update('BodyText',$body);
		$dbobject->update('Language',$language);
		$dbobject->where("PK_TextID",$textid);
		return $dbobject->commit();
	}	
	
	/**
     * This method reads and returns the text table.
	 *
	 * @param String $searchval If any wildcard search is done.
	 *	 
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function listText($searchval = ''){
		$dbobject = new dbobject('cms_text');
		$dbobject->read("PK_TextID");
		$dbobject->read("TextKey");
		$dbobject->read("Headline");
		$dbobject->read("Language");
		$dbobject->read("CreateDate");
		if ($searchval){
			$dbobject->wildcard("Headline",$searchval);
			$dbobject->wildcard("TextKey",$searchval);
		}
		$dbobject->where("Language",language::get());
		$dbobject->orderby("Headline");
		return $dbobject->fetch();
	}	
	
	/**
	 * This function simply replaces whitespace with an underscore
	 * It also prepend the result with a '/' backslash
	 *
	 * @param string $string The text to do the SEO optimisation
	 *
	 * @return string The resulting string.	 
	 *
	 * @access public
	 * @since Method available since Release 07-03-2017
     */
	public static function SEOHeader($string){
		$string = str_replace(' ', '_', $string);
		return '/'.$string;
	}
	
	/**
     * This method finds all the information relating to the id given.
	 *
	 * @param int $textid The id to the table to the table.
	 *
	 * @return array The table row found.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function readText($textid){
		$dbobject = new dbobject('cms_text');
		$dbobject->read("PK_TextID");
		$dbobject->read("TextKey");
		$dbobject->read("Headline");
		$dbobject->read("BodyText");
		$dbobject->read("Language");
		if (is_numeric($textid)){
			$dbobject->where("PK_TextID",$textid);
		} else {
			$dbobject->where("TextKey",$textid);
		}
		return $dbobject->fetchSingle();
	}

	/**
     * This method finds all the information relating to the given key.
	 * It also checks for text meta and add the options, that are described 
	 * in the meta data.
	 *
	 * This should probably be in a template block instead.
	 *
	 * @param string $TextKey The key to the table.
	 *
	 * @return array The table row found on success or false.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function readTextByKey($key){
		if (!$key){
			return false;
		}
		$return = '';
		$dbobject = new dbobject('cms_text');	
		$dbobject->read("BodyText");	
		$dbobject->read("PK_TextID");	
		$dbobject->where("TextKey",$key);
		$dbobject->where("Language",language::get());
		$obj = $dbobject->fetchSingle('FETCH_ASSOC');
		if ($obj["BodyText"]){

			if (isset($_REQUEST['editmode'])){
				if ($_REQUEST['editmode']){
					self::turnOnEdit();
				} else {
					self::turnOffEdit();
				}
			}
		
			$return .= '<div id="text_'.$obj["PK_TextID"].'">';
			$return .= self::getMetaData($obj["PK_TextID"]);

			if (self::$editmode && self::$adminid){
				$return .='<div class="editline"><span class="editicon">
				<a href="javascript:editText(\''.$obj["PK_TextID"].'\')">'.language::readType('EDIT').'</a></span>'.$obj["BodyText"].'</div>';
			} else {
				$return .= $obj["BodyText"];
			}			

			$return .= '</div>';
			return $return;		
		}
		return '{{'.$key.'}}';
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
		$dbobject = new dbobject('cms_text');
		$dbobject->destroy();
		$dbobject->where("PK_TextID",$textid);
		return $dbobject->commit();		
	}
}
?>