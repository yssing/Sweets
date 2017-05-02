<?php
class error {
	public static function indexAction($args){
		switch(intval($args[0])){
			case 404:
				self::notFound();
			break;
			case 403:
				self::noAccess();
			break;
		}
	}

	private static function notFound(){	
	
		template::initiate('main');
			template::title(route::$url,'[TITLE]','404 - not found');
			template::header(route::$url,'[HEADER]','404 - not found');
			template::body(route::$url,'[BODY]','<br /><br />Bummer!! 404 - not found!');

			template::footer(text::readTextByKey('FOOTER'));
			template::replace('[COPY]',text::readTextByKey('COPY'));
			
			template::replace('[PATH]',PATH_WEB);
			template::replace('[MENU]',menu::makeMenu());
		template::end();
	}

	private static function noAccess(){	
		template::initiate('main');
			template::title(route::$url,'[TITLE]','403 - Access denied');
			template::header(route::$url,'[HEADER]','403 - Access denied');
			template::body(route::$url,'[BODY]','<br /><br />Bummer!! 403 - Access denied!');

			template::footer(text::readTextByKey('FOOTER'));
			template::replace('[COPY]',text::readTextByKey('COPY'));

			template::replace('[PATH]',PATH_WEB);
			template::replace('[MENU]',menu::makeMenu());
		template::end();
	}
}
?>