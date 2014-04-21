<?php
class query{
	public static function indexAction(){
		//echo "has no action!!!";
		template::initiate('main');
			template::noCache();
			template::body("Has no action!");
		template::end();
  
	}

	public static function emailAction($args){
		if(user::validateEMail($args[0])){
			echo trim(PATH_WEB."/template/".TEMPLATE."/icon/accept.png");
		} else {
			echo trim(PATH_WEB."/template/".TEMPLATE."/icon/stop.png");
		}
	}

	public static function usernameAction($args){
		if(user::countUserLogin($args[0]) || !$args[0]){
			echo trim(PATH_WEB."/template/".TEMPLATE."icon/stop.png");
		} else {
			echo trim(PATH_WEB."/template/".TEMPLATE."icon/accept.png");
		}	
	}

	/**
	 * This method finds the cityname corresponding to a zipcode
	 * For an easier route, use geography/zipcode/
	 */
	public static function zipcityAction($args){
		echo zipcode::readSingleZipcode($args[0]);	
	}
}
?>