<?php

class template_frontpage {
	public static function indexAction(){
		template::initiate('frontpage');
			template::header(text::readTextByKey('HEADER'));
			template::body(text::readTextByKey('BODY'));
			template::title(text::readTextByKey('TITLE'));
			template::replace('[REFERENCES]',text::readTextByKey('REFERENCES'));
			template::replace('[CONTACT_US]',text::readTextByKey('CONTACT_US'));
			template::replace('[FOLLOW_US]',text::readTextByKey('FOLLOW_US'));
			template::replace('[ABOUT_US]',text::readTextByKey('ABOUT_US'));
			template::replace('[NEWSLETTER]',text::readTextByKey('NEWSLETTER'));

			template::copy(text::readTextByKey('COPY'));
			template::replace('[PATH]',PATH_WEB);
			template::menu(submenu::makeMenu());
		template::end();
	}
}
?>