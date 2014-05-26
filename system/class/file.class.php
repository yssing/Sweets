<?php
/**
 * This class handles different file and folder actions.
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
 * @category	Generic system methods
 * @package		files
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */
require_once('generic.IO.class.php');
class files extends genericIO{ 
	
	/**
	 * Read a folder and creates a 2D array based on found files. it does not
	 * include the two parent indicators.
	 *
	 * @param string $path the path to then folder
	 *
	 * @return array/bool $foldercontent Returns array on success or false on failure.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	
	public static function listFolderContent($path){
		$foldercontent = array();
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if($entry != '.' && $entry != '..'){
					$foldercontent[] = array($entry,$entry);
				}
			}
			closedir($handle);
			return $foldercontent;
		} else {
			return false;
		}
	}

	/**
	 * This method reads the files in a folder and put the result in an array.
	 * It will display images using the original image, but downsized in html.
	 * For other file types, it will display an appropriate icon.
	 * It can deal with the most common file types found on the web. 
	 * It will display only files!
	 * It will only list files, not folders!
	 *
	 * @param string $path the path to the folder, must be a relative path.	 
	 * @param int $cols how many columns, default to 5.	 
	 * @param int $width the width of the icons, defaults to 128.	 
	 *
	 * @return string $foldercontent The array with the content.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function fileLister($path,$cols=5,$width='128'){
		$foldercontent = array();
		$i = 1;
		$j = 0;
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if($entry != '.' && $entry != '..'){
					$entry = iconv("ISO-8859-1//IGNORE", "UTF-8", $entry);
					$filetype = form::getFileExtension($path.'/'.$entry);
					
					if(!is_dir($path.'/'.$entry)){
						if($filetype == 'jpg' || $filetype == 'gif' || $filetype == 'jpeg' || $filetype == 'png'){
							$content = '<img src="'.PATH_WEB.'/'.$path.'/'.$entry.'" width="'.$width.'px;" />';							
						} else {
							$content = self::getIcon($filetype,$width);
						}	
						$foldercontent[$j][$i] = $content.'<br /><a href="edit/?path='.urlencode($path.'/'.$entry).'">'.$entry.'</a> 
						| <a href="delete/'.$entry.'">Slet</a>';
					}
					
					if($i >= $cols){
						$i = 1; 
						$j++;
					}
					$i++;
				}
			}
			closedir($handle);
		}
		return $foldercontent;
	}
	
	/**
	 * This method reads files and folders in a folder and put the result in an array.
	 * If file is an image, it will display images using the original image, but downsized in html.
	 * For other file types, it will display an appropriate icon.
	 * It can deal with the most common file types found on the web. 
	 *
	 * @param string $path the path to the folder.	 
	 * @param bool $showClass show class folders or not.	 
	 *
	 * @return string $foldercontent The array with the content.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function folderLister($path,$showClass = true){
		$foldercontent = array();
		$width = '32';
		$j = 0;
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if($entry != '.' && $entry != '..' && (($entry != 'class' && strpos($entry,'control') === false && !$showClass) || $showClass)){
					$filetype = form::getFileExtension($path.$entry);
					if(is_dir($path.'/'.$entry)){
						$content = '<img src="'.PATH_WEB.'/template/'.TEMPLATE.'/icon_big/folder.png" width="'.$width.'px;" />';
						$foldercontent[$j] = array(form::check(0,form::encode($entry)),$content,
						'<a href="/system/structure/edit/?path='.urlencode($path.'/'.$entry).'">'.$entry.'</a>');
					} else {
						$foldercontent[$j] = array(form::check(0,form::encode($entry)),self::getIcon($filetype,$width),
						'<a href="/system/structure/edit/?path='.urlencode($path.'/'.$entry).'">'.$entry.'</a>');					
					}
					$j++;
				}
			}
			closedir($handle);
		}		
		return $foldercontent;
	}	
	
	/**
	 * This method simply returns an icon path based on the file type and width.. 
	 *
	 * @param string $filetype the type of file.	 
	 * @param int $width width of the icon.	 
	 *
	 * @return string $content The html formatted icon.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			

	private static function getIcon($filetype,$width){
		switch($filetype){
			case 'png':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/photo_landscape.png" width="'.$width.'px;" />';
				break;
			case 'jpg':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/photo_landscape.png" width="'.$width.'px;" />';
				break;
			case 'jpeg':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/photo_landscape.png" width="'.$width.'px;" />';
				break;
			case 'gif':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/photo_landscape.png" width="'.$width.'px;" />';
				break;
			case 'pdf':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/document.png" width="'.$width.'px;" />';
				break;
			case 'doc':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/document.png" width="'.$width.'px;" />';
				break;
			case 'docx':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/document.png" width="'.$width.'px;" />';
				break;
			case 'xl':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/document_chart.png" width="'.$width.'px;" />';
				break;
			case 'xls':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/document_chart.png" width="'.$width.'px;" />';
				break;
			case 'txt':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/document.png" width="'.$width.'px;" />';
				break;	
			case 'htm':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/code.png" width="'.$width.'px;" />';
				break;
			case 'html':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/code.png" width="'.$width.'px;" />';
				break;
			case 'css':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/css_green.png" width="'.$width.'px;" />';
				break;
			case 'php':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/code_php.png" width="'.$width.'px;" />';
				break;	
			case 'js':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/code_javascript.png" width="'.$width.'px;" />';
				break;
			case 'mp3':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/music_blue.png" width="'.$width.'px;" />';
				break;
			case 'wmv':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/movie.png" width="'.$width.'px;" />';
				break;
			case 'mpg':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/movie.png" width="'.$width.'px;" />';
				break;
			case 'rar':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/box_closed.png" width="'.$width.'px;" />';
				break;		
			case 'zip':
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/box_closed.png" width="'.$width.'px;" />';
				break;
			default:
				$content = '<img src="'.PATH_WEB.'/template/'.template::getTheme().'/icon_big/unknown.png" width="'.$width.'px;" />';
				break;
		}
		return $content;
	}
}
?>