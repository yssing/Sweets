<?php
class installcontrol{

	public static function indexAction(){
		route::redirect('common/install/');
	}
	
	public static function listcsvAction($args){
	
		//import::importCSV('csv/generic_language.csv','test');
		//import::importJSON('csv/generic_language.json','test');		
		//import::importXML('csv/generic_language.xml','test');
		$body = views::displayListview(import::importCSV('csv/generic_language.csv','test'));		
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();			
	}
}
?>