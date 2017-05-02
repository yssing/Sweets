<?php
class admin {

	public static function indexAction(){
		if (user::validateAdmin()){
			route::redirect('system/user/');
		} else {
			$body = form::beginForm('login',PATH_WEB.'/admin/verify','post',array('class' => 'col-md-6'));
				$body .= '<h4>'.language::readType('USERNAME').'</h4>' .
				form::input('','admin',TEXT,array("placeholder" => language::readType('USERNAME')));
				$body .= '<h4>'.language::readType('PASSWORD').'</h4>' .
				form::input('','password',PASSWORD,array("placeholder" => language::readType('PASSWORD')));
			$body .= form::endForm('login');
		}
				
		template::initiate('adminlogin');
			template::noCache();
			template::header(language::readType('LOGINHERE'));
			template::body($body);
			template::replace('[ADMIN_LOGIN_TEXT]',language::readType('ADMIN_LOGIN_TEXT'));
			template::replace('[MENU]','');	
		template::end();
	}

	public static function verifyAction($args){
		if (!user::validateAdmin()){
			//if (form::validate('login')){
				user::adminLogin($args['admin'],$args['password']);
				userLogin::createLogin();
				route::redirect('system/user/');
			//}
		} 
		route::redirect('admin/');
	}

	public static function logoutAction(){
		if (user::validateAdmin()){
			userLogin::createLogout();
		}
		session_destroy();
		route::redirect('admin/');
	}
}
?>