<?php
/**
 * This class handles a very simple templating system.
 *
 * It currently uses 4 standard methods for replacing content in the template
 * those methods can change title of the page and 3 defined divs, header, body and footer.
 * The class also deals with loading any external php file and with error displaying.
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
 * @package		template
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'generic.IO.class.php'
 * @require		'menu.class.php'
 */

class template extends genericIO{
	
	/**
     * it holds the temporary result of the template.
	 *
     * @var string result
     * @access protected
     */
	protected static $result = '';
		
	/**
     * Used to tell if edit javascript is already injected.
	 *
     * @var bool editInjected
     * @access public
     */
	public static $editInjected = false;

	/**
     * Holds the editmode state of a template.
	 * It's used to tell the class to display the edit boxes
	 *
     * @var bool editmode
     * @access public
     */
	public static $editmode = false;
	
	/**
     * This can overwrite the caching.
	 * If its set to false, caching will be turned off
	 *
     * @var bool caching
     * @access public
     */
	public static $caching = true;	
	
	/**
     * This holds the name of the theme.
	 *
     * @var string theme
     * @access public
     */		
	public static $theme = 'default';

	/**
     * This holds the content of the template block.
	 *
     * @var string theme
     * @access public
     */		
	public static $blockData = '';
	
	/**
	 * This method sets the theme
	 */	
	public static function setTheme($theme){
		self::$theme = $theme;
	}	

	/**
	 * This method gets the theme
	 */		
	public static function getTheme(){
		return self::$theme;
	}
	
	/**
	 * This method switches off caching
	 */	
	public static function noCache(){
		self::$caching = false;
	}	
	
	/**
	 * This method switches on the edit mode
	 */
	public static function turnOnEdit(){
		self::$editmode = true;
	}

	/**
	 * This method switches off the edit mode
	 */
	public static function turnOffEdit(){
		self::$editmode = false;
	}	

	/**
	 * This method loads a template in to the memory
	 * It will use the basepath and basefile to determine if there is
	 * a local template in the directory found by the autorouter.
	 * if one is found, then this template will be used.
	 *
	 * @param string $template What template should we use.
	 * @param string $prepend Is the template stored in another path, then which.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function initiate($template = 'main',$prepend = ''){
		if(isset($_REQUEST['editmode'])){
			if($_REQUEST['editmode']){
				self::turnOnEdit();
			} else {
				self::turnOffEdit();
			}
		}

		if(is_file($_SERVER['DOCUMENT_ROOT'].route::returnBasePath().'/template/'.route::returnBasefile().'.tpl.html')){
			$template = $_SERVER['DOCUMENT_ROOT'].route::returnBasePath().'/template/'.route::returnBasefile().'.tpl.html';
		} else if(is_file(route::returnModule().'/template/'.$template.'.tpl.html')){			
			$template = route::returnModule().'/template/'.$template.'.tpl.html';
		} else {
			$template = $prepend.'template/'.self::$theme.'/'.$template.'.tpl.html';
		}

		if(!self::$result){
			self::$result = file_get_contents($template, FILE_USE_INCLUDE_PATH);
		} 
	}

	/**
	 * This method cache the result in the cache folder.
	 *
	 * This can be very useful for fixed pages, that only an admin updates.
	 * When cached, the file will be minimized.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function cache(){
		self::checkfolder('','cache');
		$result = self::minimize();
		if(self::$caching){
			file_put_contents('cache/'.urlencode($_SERVER['REQUEST_URI']).$_SESSION['CountryCode'].'.htm', $result); 
		}
	}
	
	/**
	 * This method clears the cache folder
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function clearCache(){
		self::rrmdir('cache');
		self::checkfolder('','cache');
	}

	/**
	 * This method echos the template, changed or not.
	 * Once its printed, the buffer is emptied, thus it can only 
	 * be ended and displayed once per initialization.
	 * If the user is logged in as an admin, then the edit bar is displayed.
	 * If Template, menu, path, footer, copyright footer and title has not been set, the method will
	 * set them to default values.
	 *
	 * @param bool $cache Cache the result? default to false.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function end($minimize = true){
		self::replace('[TEMPLATE]',PATH_WEB.'/template/'.self::getTheme());
		self::replace('[MENU]',adminmenu::createMenu($_SERVER['DOCUMENT_ROOT'].'/'));
		self::replace('[PATH]',PATH_WEB);
		self::replace('[FOOTER]',USERFOOTER);
		self::replace('[COPYFOOTER]',COPYFOOTER);
		self::replace('[TITLE]',PATH_WEB);
		self::plugins();

		if(!self::$adminid){
			if($minimize && !self::$ERROR_REPORT){
				self::$result = self::minimize();
			}
			self::cache();
		} else {
			self::noCache();
		}
		echo self::$result;

		self::$result = '';
	}	
	
	/**
	 * This method injects the plugins in the header
	 * The method also includes the analytics key, since it will be treated like at plugin anyway.
	 *
	 * @return bool true on success or false on failure
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function plugins(){
		$plugin = new plugin();
		$plugins = $plugin->retrievePlugin();
		if(key::doesExist('[ANALYTICS]')){
			$plugins .= key::readValue('[ANALYTICS]');
		}
		
		if(stripos(self::$result,'</head>')){
			$plugins = $plugins.'</head>';
			self::$result = str_replace('</head>',$plugins,self::$result);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * This method minimizes the html sent to the user.
	 *
	 * It does this by removing all the tab, line feeds and carriage return.
	 * Those are not needed for viewing the html, and just takes up space.
	 *
	 * @return string $tmp the minimized result
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function minimize(){
		$tmp = self::$result;
		$tmp=str_replace("\r", "", $tmp);
		$tmp=str_replace("\n", "", $tmp);
		$tmp=str_replace("\t", "", $tmp);
		return $tmp;
	}	
	
	/**
	 * This method returns the URL, stripped of the editmode arg.
	 *
	 * @return string $path the stripped URL
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function path(){
		$route = new route();
		$path = $route->getURL();
		$path = str_replace('?editmode=1','',$path);
		$path = str_replace('?editmode=0','',$path);
		
		if(strrpos($path,'/') == strlen($path)-1 && strlen($path) > 1){
			$path = substr_replace($path ,"",-1);
		}
		
		return $path;
	}
	
	/**
	 * This method loads template block so it can be used in a loop
	 * or in a local view.
	 *
	 * @param string $block the template block to be used.
	 * @param array $data the array of data used in the block.
	 *
	 * @return string $result the rendered block
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function useBlock($block,$data){
		$result = '';
		$tmp = '';
		$template = 'template/'.self::$theme.'/blocks/'.$block.'.tpl.html';
		if (!self::$blockData){
			self::$blockData = file_get_contents($template, FILE_USE_INCLUDE_PATH);
		}

		foreach($data as $row){
			$tmp = str_replace('[PATH_WEB]',PATH_WEB,self::$blockData);
			foreach($row as $key => $value){
				$tmp = str_replace('['.$key.']',$value,$tmp);
			}
			$result .= $tmp;
			$tmp = '';
		}
		return $result;
	}
	
	/**
	 * This method replaces the [TITLE] in the template
	 *
	 * @param string $title the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function title($title){
		//$title = str_replace('<p>','',$title);
		//$title = str_replace('</p>','',$title);
		self::replace('[TITLE]',$title);
	}

	/**
	 * This method replaces the [HEADER] in the template
	 *
	 * @param string $header the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function header($header){
		self::replace('[HEADER]',$header);
	}

	/**
	 * This method replaces the [FOOTER] in the template
	 *
	 * @param string $footer the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function footer($footer){
		self::replace('[FOOTER]',$footer);
	}

	/**
	 * This method replaces the [BODY] in the template
	 *
	 * @param string $body the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function body($body){
		self::replace('[BODY]',$body);
	}

	/**
	 * This method replaces a key/needle in the template
	 *
	 * @param string $needle the needle in the buffer.
	 * @param string $content the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function replace($needle,$content){
		$route = new route();
		if(self::$editmode && $needle != '[TITLE]' && $needle != '[PATH]' && $needle != '[TEMPLATE]' && $needle != '[MENU]'){
			if(self::$editmode && self::$adminid){
				self::$result = str_replace($needle,'<div class="editline"><span class="editicon">
				<a href="javascript:editElement(\''.self::path().'\',\''.$needle.'\')"> Edit</a></span>'.$content.'</div>',self::$result);
			} else {
				self::$result = str_replace($needle,$content,self::$result);
			}
		} else {
			self::$result = str_replace($needle,$content,self::$result);
		}
	}

	/**
	 * This method injects javascript in to the html header
	 *
	 * It checks to see if it needs to create a new javascript section or
	 * if it just needs to inject the javascript into an existing section.
	 *
	 * @param string $javascript the javascript to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function injectJavascript($javascript){
		if(stripos(self::$result,'<script type="text/javascript">')){
			$javascript = '<script type="text/javascript">'.chr(13).$javascript;
			self::$result = str_replace('<script type="text/javascript">',$javascript,self::$result);
		} else {
			$javascript = '<script type="text/javascript">'.chr(13).$javascript.chr(13).'</script>';
			if(stripos(self::$result,'</head>')){
				$javascript .= '</head>';
				self::$result = str_replace('</head>',$javascript,self::$result);
			} else {
				self::$result .= $javascript;
			}
		}
	}

	/**
	 * This method injects any kind of script, into the header
	 *
	 * If it finds an html header, then it simply prepends a string to the header.
	 *
	 * @param string $string the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function injectIntoHeader($string){
		if(stripos(self::$result,'</head>')){
			self::$result = str_replace('</head>',$string,self::$result);
		}
	}
}
?>