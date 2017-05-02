<?php
/**
 * This class handles a very simple templating system.
 *
 * It currently uses 4 standard methods for replacing content in the template
 * those methods can change title of the page and 3 defined divs: header, body and footer.
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
 */

class template extends baseclass{
	
	/**
     * it holds the temporary result of the template.
	 *
     * @var string result
     * @access protected
     */
	public static $result = '';
		
	/**
     * Used to tell if edit javascript is already injected.
	 *
     * @var bool editInjected
     * @access 
     */
	public static $editInjected = false;

	/**
     * Holds the editmode state of a template.
	 * It's used to tell the class to display the edit boxes
	 *
     * @var bool editmode
     * @access protected
     */
	public static $editmode = false;
	
	/**
     * This can overwrite the caching.
	 * If its set to false, caching will be turned off
	 *
     * @var bool caching
     * @access private
     */
	public static $caching = true;	
	
	/**
     * This can be used to turn of plugins and other injections
	 *
     * @var bool plugins
     * @access private
     */	
	public static $vanilla = false;
	
	/**
     * This holds the name of the theme.
	 *
     * @var string theme
     * @access private
     */		
	public static $theme = 'default';

	/**
     * This holds the content of the template block.
	 *
     * @var string blockData
     * @access private
     */		
	public static $blockData = '';
	
	/**
     * This holds the values that will be used in the template blocks.
	 *
     * @var array values
     * @access private
     */			
	public static $values = array();
	
	/**
	 * This method sets a value with a key
	 */	
	public static function setValue($key,$value){
		self::$values[$key] = $value;
	}
	
	/**
	 * This method returns values
	 */		
	public static function getValues(){
		return self::$values;
	}
	
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
	 * This method is used to turn off plugins and other injections
	 */	
	public static function vanilla(){
		self::$vanilla = true;
	}	

	/**
	 * This method is used to find and insert the correct css
	 *
	 * It will first look in the style folder in the template folder.
	 * If it is found here, then that css file path is returned
	 * If not found here it will look in the in the modules template style folder.
	 *
	 * @param string $stylename What style should we use.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 07-12-2015
	 */
	public static function style($stylesheet){
		if (is_file('template/'.self::$theme.'/style/'.$stylesheet)){
			$stylename = '/template/'.self::$theme.'/style/'.$stylesheet;			
		} else if (is_file('modules/'.route::returnModule().'/template/style/'.$stylesheet)){
			$stylename = '/modules/'.route::returnModule().'/template/style/'.$stylesheet;
		} else {
			$stylename = '';
		}

		self::replace('[STYLE]',$stylename);
	}

	/**
	 * This method loads a template in to the memory
	 * 
	 * The method will look in the template folder for a template, if a template is found there,
	 * then this template will be used, if not, it will use a matching template in the modules
	 * template folder.
	 *
	 * @param string $template What template should we use.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function initiate($template = 'main'){
		if (isset($_REQUEST['editmode'])){
			if ($_REQUEST['editmode']){
				self::turnOnEdit();
			} else {
				self::turnOffEdit();
			}
		}

		if (is_file('template/'.self::$theme.'/'.$template.'.tpl.html')){
			$template = 'template/'.self::$theme.'/'.$template.'.tpl.html';
		} else if (is_file('modules/'.route::returnModule().'/template/'.$template.'.tpl.html')){
			$template = 'modules/'.route::returnModule().'/template/'.$template.'.tpl.html';
		}

		if (!baseclass::$adminid){
			if ($templateData = caching::getKey('tData'.$template)){
				self::$result = $templateData;
			} else {
				self::$result = file_get_contents($template, FILE_USE_INCLUDE_PATH);
				caching::setKey('tData'.$template, self::$result);
			}
		} else {
			self::$result = file_get_contents($template, FILE_USE_INCLUDE_PATH);
		}
/*
		if (!self::$vanilla){
			self::replace('[FOOTER]',USERFOOTER);
			self::replace('[COPYFOOTER]',COPYFOOTER);
			self::replace('[TITLE]',PATH_WEB);
		}	
*/		
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
		if (self::$caching){
			file_put_contents('cache/'.urlencode($_SERVER['REQUEST_URI']).language::get().'.htm', $result); 
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
		self::replace('[MENU]',adminmenu::createMenu($_SERVER['DOCUMENT_ROOT'].'/'));
		self::replace('[TEMPLATE]',PATH_WEB.'/template/'.self::getTheme());
		self::replace('[PATH]',PATH_WEB);
		self::replace('[URL]',route::$url);
		self::replace('[DESCRIPTION]','');
		self::replace('[APP_ID]','');
		self::replace('[WEBTYPE]','Website');

		self::plugins();

		if (!self::$adminid){
			if ($minimize && !self::$ERROR_REPORT){
				self::$result = self::minimize();
			}
			if(self::$caching){
				self::cache();
			}
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
		if (!self::$vanilla){
			$plugins = plugin::retrievePlugin();
			if (key::doesExist('[ANALYTICS]')){
				$plugins .= key::readValue('[ANALYTICS]');
			}
			
			if (stripos(self::$result,'</head>')){
				$plugins = $plugins.'</head>';
				self::$result = str_replace('</head>',$plugins,self::$result);
				return true;
			}
		}
		return false;
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
		$tmp = str_replace("\r", "", $tmp);
		$tmp = str_replace("\n", "", $tmp);
		$tmp = str_replace("\t", "", $tmp);
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
		
		if (strrpos($path,'/') == strlen($path)-1 && strlen($path) > 1){
			$path = substr_replace($path ,"",-1);
		}
		
		return $path;
	}
	
	/**
	 * This function will get the header from either a template or from
	 * a default fallback
	 *
	 * @return string $path the stripped URL
	 *
     * @access public
	 * @static
	 * @since Method available since Release 05-01-2017
	 */	
/*	 
	public static function getHeader($header = ''){
		if (is_file('template/'.self::$theme.'/blocks/header.tpl.html')){
			
			if (baseclass::$userid){
				template::setValue('menu',submenu::makeMenu('LOGGEDIN'));
			} else {
				template::setValue('menu',submenu::makeMenu('MENU'));
			}			
			
			template::setValue('title',text::readTextByKey('TITLE'));
			template::setValue('header',$header);
			return self::useBlock('header');
		} else {
			return false;
		}
	}
	
	public static function getFooter($footer = ''){
		if (is_file('template/'.self::$theme.'/blocks/footer.tpl.html')){

			template::setValue('references',text::readTextByKey('REFERENCES'));
			template::setValue('contact_us',text::readTextByKey('CONTACT_US'));
			template::setValue('follow_us',text::readTextByKey('FOLLOW_US'));
			template::setValue('about_us',text::readTextByKey('ABOUT_US'));
			template::setValue('newsletter',text::readTextByKey('NEWSLETTER'));			
			template::setValue('footer',$footer);			
			
			return self::useBlock('footer');
		} else {
			return false;
		}
	}
*/	

	/**
	 * This method loads a template block so it can be used in a local view.
	 * 
	 * It uses the keys and values set using the setValue() method
	 *
	 * @param string $block the template block to be used.
	 * @param boolean $ignorevalue used to tell the method to ignore values.
	 *
	 * @return string $result the rendered block
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function useBlock($block, $ignorevalue = false){
		$result = '';
		$template =	'';
		
		if (is_file('template/'.self::$theme.'/blocks/'.$block.'.tpl.html')){
			$template = 'template/'.self::$theme.'/blocks/'.$block.'.tpl.html';
		} else if (is_file('modules/'.route::returnModule().'/template/blocks/'.$block.'.tpl.html')){
			$template = 'modules/'.route::returnModule().'/template/blocks/'.$block.'.tpl.html';
		} else {
			return false;
		}
		
		if (!baseclass::$adminid){
			if ($result = caching::getKey('bData'.$template)){
				self::$blockData = $result;
			} else {
				self::$blockData = file_get_contents($template, FILE_USE_INCLUDE_PATH);	
				$result = str_replace('[PATH_WEB]',PATH_WEB,self::$blockData);			
				caching::setKey('bData'.$template, $result);
			}
		} else {
			self::$blockData = file_get_contents($template, FILE_USE_INCLUDE_PATH);	
			$result = str_replace('[PATH_WEB]',PATH_WEB,self::$blockData);			
		}
		
		if (!$ignorevalue){
			foreach (self::$values as $key => $value){
				$result = str_replace('['.strtoupper(trim($key)).']',$value,$result);
			}
			
			self::$values = array();
		}
		return $result;		
	}
	
	/**
	 * Just a wrapper for echo, at least for now.
	 *
	 * @param string $string.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 29-12-2015
	 */	
	public static function render($string){	
		echo $string;			
	}
	
	/**
	 * This method looks for a few functions in the template.
	 * It is kept simple, since the template is meant to be a small system
	 *
	 * @param string $data the data stream to use
	 *
	 * @return string $data the data after applying functions
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		 
	public static function templateFunctions($data){
		// Loop in the template
		if ($loopbegin = strpos($data,'<{loop')) {
			$endtag = strpos($data,'}>')+2;
			$vars = substr($data,$loopbegin,($endtag-$loopbegin));
			$varlist = explode(' ',$vars);
			foreach ($varlist as $single){
				list($key,$value) = explode('=',$single);
				$value = trim($value);
				switch($key){
					case 'to':
						$to = $value;
					break;
					case 'from':
						$from = $value;
					break;
				}
			}
			$replacestring = substr($data, $loopbegin, (strpos($data,'<{/loop}>') - $loopbegin)+9);
			$subdata = substr($data, $endtag, (strpos($data,'<{/loop}>') - $endtag));
			$tmp = self::templateLoop($subdata,$from,$to);
			$data = str_replace($replacestring, $tmp, $data);
		}
		
		return $data;
	}
	
	/**
	 * This method simply creates a row based on the data stream
	 * 
	 * @param string $data The string to use in the loop
	 * @param int $from Where to start
	 * @param int $to Where to stop
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0	 
	 */
	public static function templateLoop($data,$from,$to){
		$retval = '';
		for ($i = $from; $i <= $to; $i++){
			$retval .= $data;
		}
		return $retval;
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
	 * This method replaces the [COPY] in the template
	 *
	 * @param string $copy the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function copy($copy){
		self::replace('[COPY]',$copy);
	}	

	/**
	 * This method replaces the [MENU] in the template
	 *
	 * @param string $menu the string to be injected.
	 *
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function menu($menu){
		self::replace('[MENU]',$menu);
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
		if (self::$editmode && $needle != '[TITLE]' && $needle != '[PATH]' && $needle != '[TEMPLATE]' && $needle != '[MENU]'){
			if (self::$editmode && self::$adminid){
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
		if (stripos(self::$result,'<script type="text/javascript">')){
			$javascript = '<script type="text/javascript">'.chr(13).$javascript;
			self::$result = str_replace('<script type="text/javascript">',$javascript,self::$result);
		} else {
			$javascript = '<script type="text/javascript">'.chr(13).$javascript.chr(13).'</script>';
			if (stripos(self::$result,'</head>')){
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
		if (stripos(self::$result,'</head>')){
			self::$result = str_replace('</head>',$string,self::$result);
		}
	}
}
?>