<?php		
class speechcontrol{

	public static function indexAction($args){
		require_once('contributions/Text2Speech.class.php');
		if (!user::validateAdmin()){
			route::error(403);
		}	
		$t2s = new Text2Speech(); 		
		$story = 'If you can hear this, it means that the speech module is working';
		
		$bodytext = '<audio controls="controls" autoplay="autoplay">
		<source src="'.PATH_WEB.'/'.$t2s->speak($story,'en').'" type="audio/mp3" /></audio>';

		template::initiate('admin');
			template::header('<h3>Speech</h3>');
			template::body($bodytext);
		template::end();
	}
	
	public static function readTextAction($args){
		require_once('contributions/Text2Speech.class.php');
		list($id,$key,$headline,$text) = text::readText($args[0]);

		$t2s = new Text2Speech(); 
		
		$bodytext = '<audio controls="controls" autoplay="autoplay">
		<source src="/'.$t2s->speak(baseclass::specialChars(strip_tags($text)),'da').'" type="audio/mp3" /></audio>';		
		
		template::initiate('base');
			template::header($headline);
			template::body($bodytext);
		template::end();		
	}
}	
?>