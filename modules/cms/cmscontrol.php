<?php

class cmscontrol{
	
	public static function indexAction(){
		
		$body = 'Nothing to see here, move along!';

		template::initiate('main');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}
}

?>