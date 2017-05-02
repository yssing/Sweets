<?php
/**
 * Most of these actions are used in conjuction with JQuery, to dynamically find server-side data
 */
 
class query{
	public static function indexAction(){
		//echo "has no action!!!";
		template::initiate('main');
			template::noCache();
			template::body("Has no action!");
		template::end();
	}
	
	public static function printAction($args){
		template::initiate('print');
			template::vanilla();
			template::noCache();
			template::body($args['divElements']);
		template::end();
	}	
	
	/**
	 * Creates and echoes a captcha image
	 */		
	public static function captchaAction(){
		include_once('system/utils/captcha.class.php');
		captcha::createImage();
	}

	/**
	 * Validates a users e-mail, based on checksums and the database
	 * It echoes an icon as response
	 */		
	public static function emailAction($args){
		if(isset($args[0])){
			if ($userid = baseclass::$userid){
				$userEMail = user::readUserMail($userid);
				if ($userEMail == $args[0]){
					echo trim(PATH_WEB."/template/".TEMPLATE."/icon/accept.png");
				} else {
					if (user::validateEMail($args[0])){
						echo trim(PATH_WEB."/template/".TEMPLATE."/icon/accept.png");
					} else {
						echo trim(PATH_WEB."/template/".TEMPLATE."/icon/stop.png");
					}
				}
			} else {
				if (user::validateEMail($args[0])){
					echo trim(PATH_WEB."/template/".TEMPLATE."/icon/accept.png");
				} else {
					echo trim(PATH_WEB."/template/".TEMPLATE."/icon/stop.png");
				}
			}
		} else {
			echo trim(PATH_WEB."/template/".TEMPLATE."/icon/stop.png");
		}
	}
	
	/**
	 * Reads a text value and echoes
	 */	
	public static function getTextAction($args){
		$args = strtoupper($args[0]);
		echo language::readType($args);
	}

	/**
	 * Validates a users name, to see if it is available
	 * It echoes an icon as response
	 */
	public static function usernameAction($args){
		if (user::countUserLogin($args[0]) || !$args[0]){
			echo trim(PATH_WEB."/template/".TEMPLATE."/icon/stop.png");
		} else {
			echo trim(PATH_WEB."/template/".TEMPLATE."/icon/accept.png");
		}	
	}

	/**
	 * This method finds the cityname corresponding to a zipcode
	 * For an easier route, use geography/zipcode/
	 */
	public static function zipCityAction($args){
		echo trim(zipcode::readSingleZipcode($args[0]));	
	}
	
	public static function daysInMonthAction($args){
		echo form::select(calendar::listDays($args[0]),1,$args[1],0);
	}

	/**
	 * php info
	 */	
	public static function infoAction(){
		phpinfo();
	}
	
	/**
	 * memcache info
	 */		
	public static function memcachedAction(){
		$memcache = new Memcache;
		$memcache->connect('localhost', 11211)
				or die ("Could not connect");
		$version = $memcache->getVersion();
		echo "Server's version: " . $version . "\n";
	}	
}
?>