<?php
/**
 * This class handles routing.
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
 * @package		route
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */

class route extends genericIO{

	/**
     * if any arguments are parsed, then this array will hold them using an associative array.
	 *
     * @var array args
     * @access public
     */	
	public static $args = array();
	
	/**
     * holds the url
	 *
     * @var string url
     * @access public
     */	
	public static $url = '';

	/**
     * holds the url in an array.
	 *
     * @var string url
     * @access public
     */		
	public static $urlArray = array();	

	/**
     * holds the name of the base file found in the dir
	 *
     * @var string basefile
     * @access public
     */		
	public static $baseFile = '';

	/**
     * holds the name of the url
	 *
     * @var string baseurl
     * @access public
     */		
	public static $basePath = '';
	
	/**
     * holds the name of the module
	 *
     * @var string module
     * @access public
     */			
	public static $module = '';
	
	public function __construct(){
		self::getBaseURL();
	}
	
	/**
     * This method will take the arguments parsed in the normal html format (? and &)
	 * and put them into an associative array, where the key is the left hand side of the '='
	 * and the value being the right hand side.
	 * It will also remove any arguments parsed with a '?' in the beginning, the
	 * arguments will be stored in the class global 'args'.
	 * They are stored in an associative array, this makes it easy to access the variables, without using 
	 * the normal $_GET or $_REQUEST.
	 *
	 * @return string $argsarray the arguments, or if no args, then an empty array.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function getArgs(){
		foreach($_REQUEST as $key => $value){
			self::$args[$key] = $value;
		}
		return self::$args;
	}
	
	/**
	 * Will build a route based on controller and method actions 
	 *
	 * @return bool true on success or false on failure.	
	 *
	 */
	public static function autoRoute(){
		$path = $_SERVER['DOCUMENT_ROOT'].'/';
		$relativePath = '';
		$classname = '';
		$function = '';
		$callIndex = 1;

		self::getURL();
		foreach(self::$urlArray as $single){			
			$path .= $single;
			$relativePath .= $single;

			if(is_dir(realpath($path.'control'))){
				$path .= 'control/';
				$relativePath .= 'control/';
				$single .= 'control';
			} else {
				self::$module = $single;
				$path .= '/';
				$relativePath .= '/';
			}
			
			if(is_dir(realpath($path))){
				// is dir, great next step
				self::$basePath = $relativePath;
				// lets check to see if there is a file with the same name
				// as the dir, if that is the case, then we include that file.
				if(is_file($path.$single.'.php')){
					include_once($path.'/'.$single.'.php');
					self::$baseFile = $single;
					if(class_exists($single)){
						// if the file in the dir also have a class, the dir, file and class should all 
						// have the same name in this case. 
						$classname = $single;
					} 
				}
			} elseif(is_file($path.'.php')){
				// now we make sure that we also gets the right class and action
				// if the full path and name is in the url
				// in theory, this step could be skipped completely
				include_once($path.'.php');
				self::$baseFile = $single;
				$classname = $single;
			} else {
				// find what action to call
				if(is_callable(array($classname,$single.'Action'))){
					$function = $single.'Action';
					$callIndex = 0;
				} else {
					// get the rest as arguments
					self::$args[] = $single;
				}
			}
		}
		if(class_exists($classname)){
			self::getArgs();
			if($callIndex){
				$classname::indexAction(self::$args);
				return true;
			} elseif($function) {
				$classname::$function(self::$args);
				return true;
			}
		} else {
			if(sizeof(self::$urlArray) <= 2){
				// url string contains only frontpage, so we try to redirect to the frontpage from the define.
				self::redirect(FRONTPAGE);	
			} else {
				// nothing found.
				// redirect to 404
				self::error('404');			
			}
		}
		return false;
	}

	/**
     * This method returns the basefile found in the autorouter.	
	 *
	 * @return string returns the basefile.	
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function returnBasefile(){
		return self::$baseFile;
	}	

	/**
     * This method returns the arguments found in the autorouter.	
	 *
	 * @return string returns the arguments.	
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function returnArgs(){
		return self::$args;
	}
	
	/**
     * This method returns the base path found in the autorouter.	
	 *
	 * @return string returns the basefile.		
	 *	 
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function returnBasePath(){
		return self::$basePath;
	}		
	
	/**
     * This method redirects to the given route.
	 *
	 * @param string $url The url to redirect to.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function redirect($url){
		header('location: '.PATH_WEB.'/'.$url);
	}	

	/**
     * This method returns the name of the module.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function returnModule(){
		return self::$module;
	}
	
	/**
     * This method redirects to the user error class.
	 *
	 * @param integer $error The error type.
	 *
	 * @access public
	 * @since Method available since Release 2013_12_27
     */		
	public static function error($error){
		header('location: '.PATH_WEB.'/common/error/'.$error);
	}		
		
	/**
	 * This method finds and returns the url found in the URL string.
	 *
	 * It will use php's REQUEST_URI and urldecode it.
	 * The method also strips any arguments parsed.
	 *
	 * @return string $url the formatted url string.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function getURL(){
		$url = str_replace('//','/',$_SERVER['REQUEST_URI']);
		if(strrpos($url,'/') == strlen($url)-1 && strlen($url) > 1){
			$url = substr_replace($url ,"",-1);
		}		
		$url = urldecode($url);
		self::$urlArray = explode("/",$url);
		return $url;
	}

	/**
	 * This method finds and returns the url found in the URL string, stripped of all arguments parsed.
	 *
	 * @return string $url the formatted url string.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function getBaseURL(){
		$url = $_SERVER['REQUEST_URI'];
		
		if(strrpos($url,'?')){ 
			$url = substr($url, 0, strpos($url, "?"));
		}
		if(strrpos($url,'/') == strlen($url)-1 && strlen($url) > 1){
			$url = substr_replace($url ,"",-1);
		}
		self::$url = $url;
		return $url;
	}	

	/**
	 * This method checks if a given url relates to a file in the cache folder.
	 * If an admin is logged in, then the caching will be turned of, in order to be able to edit the page.
	 *
	 * @return string/bool $filename returns the path and filename if a cached file is found or false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public static function isCached(){
		if(self::$adminid){
		 return false;
		}		
		$filename = 'cache/'.urlencode($_SERVER['REQUEST_URI']).$_SESSION['CountryCode'].'.htm';
		if(is_file($filename)){
			return $filename;
		} else {
			return false;
		}
	}	
}
?>