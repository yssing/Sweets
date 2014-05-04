<?php
include_once('contributions/Text2Speech.class.php');		
class speechcontrol{

	public static function indexAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		$t2s = new Text2Speech(); 		
		$story = 'If you can hear this, it means that the speech module is working';
		
		$bodytext = '<audio controls="controls" autoplay="autoplay">
		<source src="'.PATH_WEB.'/'.$t2s->speak($story).'" type="audio/mp3" />
		</audio>';
			
		template::initiate('admin');
			template::header('<h3>Speech</h3>');
			template::body($bodytext);
			template::header(language::readType('EDIT'));
		template::end();
	}
}	
?>