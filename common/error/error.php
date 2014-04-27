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
			template::title(element::readElementPath(route::$url,'[TITLE]','404 - not found'));
			template::header(element::readElementPath(route::$url,'[HEADER]','404 - not found'));
			template::body(element::readElementPath(route::$url,'[BODY]','<br /><br />Bummer!! 404 - not found!'));
			
			template::footer(element::readElementPath(route::$url,'[FOOTER]',USERFOOTER));						
			template::replace('[COPY]',element::readElementPath(route::$url,'[COPY]',COPYFOOTER));
			template::replace('[PATH]',PATH_WEB);
			template::replace('[MENU]',menu::makeMenu());
		template::end();
	}
	
	private static function noAccess(){	
		template::initiate('main');
			template::title(element::readElementPath(route::$url,'[TITLE]','403 - Access denied'));
			template::header(element::readElementPath(route::$url,'[HEADER]','403 - Access denied'));
			template::body(element::readElementPath(route::$url,'[BODY]','<br /><br />Bummer!! 403 - Access denied!'));

			template::footer(element::readElementPath(route::$url,'[FOOTER]',USERFOOTER));						
			template::replace('[COPY]',element::readElementPath(route::$url,'[COPY]',COPYFOOTER));
			template::replace('[PATH]',PATH_WEB);
			template::replace('[MENU]',menu::makeMenu());
		template::end();
	}
}
?>