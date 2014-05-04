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
}
?>