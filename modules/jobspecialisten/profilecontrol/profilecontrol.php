<?php
class profilecontrol{
	public static function indexAction(){
		//template::initiate('main');
		template::initiate('jobspecialisten');
		//template::initiate();
			template::header('Job-Specialisten');
			template::body('Velkommen');
		template::end();	
	}
	
	public static function listAction(){
		template::initiate('admin');
			template::header('Job-Specialisten');
			template::body('Liste over profiler');
		template::end();	
	}	
	
	public static function updateadressAction($args){
		var_dump(user::readUser($args[0]));
		//$userbase->updateUserAddress($street,$number,$floor,$door,$zipcode,$city,$area,$country,$userid);
	}
}
?>