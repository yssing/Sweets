<?php

class template_frontpage {
	public static function indexAction(){
		template::initiate('frontpage');
			//template::noCache();
			template::body(text::readTextByKey('BODY'));
			template::title(text::readTextByKey('TITLE'));
			template::footer(text::readTextByKey('FOOTER'));
			template::replace('[COPY]',text::readTextByKey('COPY'));			
			
			template::replace('[PATH]',PATH_WEB);
			template::replace('[MENU]',menu::makeMenu());
		template::end();			
	}
}
?>