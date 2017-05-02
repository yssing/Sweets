<?php
/**
 * This class handles photo administration.
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
 * @category   	Photo methods
 * @package		photo
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2015 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */
 
class photo{
	/**
     * This method creates a photo entry in the database.
	 *
	 * @param string $Name Photo name.
	 * @param string $Description Photo description.
	 * @param string $URL Photo URL.
	 * @param string $language The language of the text.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function createPhoto($Name,$Description,$URL,$language = STD_LANGUAGE){
		$dbobject = new dbobject('ephoto');
		$dbobject->create('Name',$Name);
		$dbobject->create('Description',$Description);
		$dbobject->create('URL',$URL);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;
	}
	
	public static function readPhoto($URL){	
		$dbobject = new dbobject('ephoto');
		$dbobject->read('PK_PhotoID');
		$dbobject->read('Name');
		$dbobject->read('Description');
		$dbobject->read('URL');
		$dbobject->where("URL",$URL);
		return $dbobject->fetchSingle();
	}
	
	public static function small($path){
		$path = str_replace('/full','/small',$path);
		return str_replace('/medium','/small',$path);
	}
	
	public static function medium($path){
		$path = str_replace('/full','/medium',$path);
		return str_replace('/small','/medium',$path);
	}

	public static function full($path){
		$path = str_replace('/small','/full',$path);
		return str_replace('/medium','/full',$path);
	}	
	
	public static function showControl($icon = '', $selection = 'single'){
		$ephoto = array("onClick" => "showePhotoSel('icon','".$selection."')");		
		return form::fieldset('field4',language::readType('ICON'),form::inputControl($icon,'icon','<img src="[TEMPLATE]/icon/view.png">',$ephoto));		
	}
	
	public static function listPhotoFolders($path = ''){
		$folders = '';
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' ){
					if (is_dir($path.'/'.$entry)){
						$folders .= '<li id="photofolder'.$entry.'"><a href="javascript:tSubPhotoView('.$entry.')">'.$entry.'</a>';
						if($sub = self::listSubPhotoFolders($path.'/'.$entry)){
							$folders .= '<ul id="subfolder'.$entry.'" style="display:none">'.$sub.'</ul>';
						}
						$folders .= '</li>';
					}
				}
			}
			closedir($handle);
		}
		
		if ($folders){
			$folders = '<ul id="photolist">'.$folders.'</ul>';
		}		
		
		return $folders;
	}
	
	public static function findThumbs($path,$id = 0,$cols=5){

		$foldercontent = array();
		$i = 1;
		$j = 0;
	
		$dbobject = new dbobject('ephoto');
		$dbobject->read('URL');
		$dbobject->wildcard("Name",$path);
		$dbobject->wildcard("Description",$path);
		$dbobject->wildcard("URL",$path);
		$imgarray = $dbobject->fetch();
		
		foreach($imgarray as $img){
			$url = self::small($img[0]);
			
			$imgDel = explode('/',$url);
			$entry = $imgDel[sizeof($imgDel) - 1];
			$delPath = str_replace('full/'.$imgDel[sizeof($imgDel) - 1],'small',$img[0]);
			
			if (!$id){
				$content = '<img onclick="showImage(\''.baseclass::encodeSpace(PATH_WEB.'/'.urldecode($img[0])).'\')" src="'.PATH_WEB.'/'.urldecode($url).'" />';
				$foldercontent[$j][$i] = $content.'<br /><a href="javascript:description(\''.($img[0]).'\')">'.language::readType('DESCRIPTION').'</a> 
				| <a href="javascript:deleteImage(\''.urlencode($delPath).'\',\''.urlencode($entry).'\')">'.language::readType('DELETE').'</a>';
			} else {
				$content = '<img src="'.PATH_WEB.'/'.urldecode($url).'" />';
				//$url = str_replace('/full/','/medium/',$img[0]);
				//$url = $img[0];
				$url = self::medium($img[0]);
				$foldercontent[$j][$i] = $content.'<br /><a href="javascript:selectImage(\''.urldecode($url).'\',\''.$id.'\')">'.language::readType('SELECT').'</a>';			
			}
			
			if ($i >= $cols){
				$i = 0; 
				$j++;
			}
			$i++;
		}
		return $foldercontent;		
	}
	
	public static function listThumbs($path,$cols=5){
		$foldercontent = array();
		$i = 1;
		$j = 0;
		if ($handle = @opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..'){
					$entry = iconv("ISO-8859-1//IGNORE", "UTF-8", $entry);
					$filetype = form::getFileExtension($path.'/'.$entry);
					
					if (!is_dir($path.'/'.$entry)){
						if ($filetype == 'jpg' || $filetype == 'gif' || $filetype == 'jpeg' || $filetype == 'png'){
							$content = '<img onclick="showImage(\''.baseclass::encodeSpace(PATH_WEB.'/'.$path.'/'.$entry).'\')" src="'.PATH_WEB.'/'.$path.'/'.$entry.'" />';							
						}
						$foldercontent[$j][$i] = $content.'<br /><a href="javascript:description(\''.urlencode($path.'/'.$entry).'\')">'.language::readType('DESCRIPTION').'</a> 
						| <a href="javascript:deleteImage(\''.urlencode($path).'\',\''.urlencode($entry).'\')">'.language::readType('DELETE').'</a>';
					}
					
					if ($i >= $cols){
						$i = 0; 
						$j++;
					}
					$i++;
				}
			}
			closedir($handle);
		}
		return $foldercontent;
	}
	
	public static function listThumbsSelec($path,$selID,$selecType='multiple',$cols=5){
		$foldercontent = array();
		$i = 1;
		$j = 0;
		$x = 0;
		if ($handle = @opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..'){
					$entry = iconv("ISO-8859-1//IGNORE", "UTF-8", $entry);
					$filetype = form::getFileExtension($path.'/'.$entry);
					
					if (!is_dir($path.'/'.$entry)){
						if ($filetype == 'jpg' || $filetype == 'gif' || $filetype == 'jpeg' || $filetype == 'png'){
							$content = '<img src="'.PATH_WEB.'/'.$path.'/'.$entry.'" />';							
						}
						if($selecType == 'single'){
							$content .= '<br /><a href="javascript:selectImage(\''.self::medium($path).'/'.baseclass::encodeSpace($entry).'\',\''.$selID.'\')">'.language::readType('SELECT').'</a><br />';
							$foldercontent[$j][$i] = $content;
						} else {
							$action = 'onclick="addImages(this.id,\''.$selID.'\')"';
							$lbl = ' <label id="lbl'.$x.'"></label>';
							$foldercontent[$j][$i] = $content.language::readType('SELECT').': <input type="checkbox" id="img'.$x.'" sort="0" data-image="'.self::medium($path).'/'.baseclass::encodeSpace($entry).'" '.$action.'>'.$lbl;
						}
					}
					
					if ($i >= $cols){
						$i = 0; 
						$j++;
					}
					$i++;
					$x++;
				}
			}
			closedir($handle);
		}
		return $foldercontent;		
	}
	
	public static function listDates(){
		template::setValue('ephoto_folder_list',photo::listPhotoFolders('uploads/photo'));
		template::setValue('template',template::getTheme());
		$body = template::useBlock('dates');		
		return $body;
	}		
	
	public static function updatePhotoInfo($URL = '', $Name = '', $Description = ''){
		if (!self::doesExist($URL)){
			return self::createPhoto($Name,$Description,$URL);	
		} else {
			$dbobject = new dbobject('ephoto');
			$dbobject->update('Name',$Name);
			$dbobject->update('Description',$Description);
			$dbobject->where("URL",$URL);
			return $dbobject->commit();
		}
	}
	
	private static function listSubPhotoFolders($path = ''){
		$folders = '';
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' ){
					if (is_dir($path.'/'.$entry)){
						$folders .= '<li id="photosubfolder'.$entry.'"><a href="javascript:showMonthImg(\''.$path.'/'.$entry.'\')">'.$entry.'</a></li>';
					}
				}
			}
			closedir($handle);
		}
		return $folders;
	}
	
	/**
	 * Checks if an entry exists.
	 *
	 * @param string $key
	 *
	 * @return integer/bool id on success or false on failure.	 
	 *
	 * @access private
	 * @since Method available since Release 2015-04-06
     */
	private static function doesExist($key){
		$dbobject = new dbobject('ephoto');
		$dbobject->read("URL");
		$dbobject->where("URL",$key);
		list($id) =  $dbobject->fetchSingle();	
		if ($id){
			return $id;
		} else {
			return false;
		}
	}		
	
	/**
     * This method deletes a photo entry in the database.
	 * It uses the URL to the photo when deleting, rather than an ID
	 *
	 * @param int $URL The URL. stored in the database
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 2015-04-06	
     */		
	public static function destroyPhoto($URL){
		$dbobject = new dbobject('ephoto');
		$dbobject->destroy();
		$dbobject->where("URL",$URL);
		return $dbobject->commit();		
	}
}
?>