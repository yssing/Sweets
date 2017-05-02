<?php

class template_frontpage {
	public static function indexAction(){
		if (user::loginByCookie()){
			route::redirect('/modules/jobspecialisten/profile');
		}
		if (user::validateUser()){
			route::redirect('/modules/jobspecialisten/profile');
		}

		template::initiate('frontpage');
			template::replace('[ERROR_MSG]',' ');
			template::replace('[PROFIL]',text::readTextByKey('PROFIL'));
			template::replace('[KURSER]',text::readTextByKey('KURSER'));
			template::replace('[INFORMATION]',text::readTextByKey('INFORMATION'));
			template::replace('[FAQ]',text::readTextByKey('FAQ'));
			template::replace('[PATH]',PATH_WEB);
			template::replace('[MENU]',menu::makeMenu());
		template::end();	
	}
}
?>