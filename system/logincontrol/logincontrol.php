<?php
class logincontrol {
	
	public static function indexAction(){
		if(user::validateAdmin()){
			route::redirect('system/user/');
		} else {
			$body = form::beginForm('login',PATH_WEB.'/system/login/verify');
				$body .= form::fieldset('field1','<h3>'.language::readType('USERNAME').'</h3>',
				form::input(language::readType('USERNAME'),'admin',TEXT,array("onClick" => "removetext(this.id)")));
				$body .= form::fieldset('field2','<h3>'.language::readType('PASSWORD').'</h3>',
				form::input('','password',PASSWORD,array("onClick" => "removetext(this.id)")));
			$body .= form::endForm('login');		
		}
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('LOGINHERE'));
			template::body($body);
			template::replace('[MENU]','');	
		template::end();		
	}
	
	public static function adminAction(){		
		$body = form::beginForm('login',PATH_WEB.'/system/login/verify');
			$body .= form::fieldset('field1',language::readType('USERNAME'),
			form::input('','admin',TEXT,array("onClick" => "removetext(this.id)")),array("class" => "fieldset"));
			$body .= form::fieldset('field2',language::readType('PASSWORD'),
			form::input('','password',PASSWORD,array("onClick" => "removetext(this.id)")),array("class" => "fieldset"));
		$body .= form::endForm('login');			
		
		template::initiate('base');
			template::noCache();
			template::header(language::readType('LOGINHERE'));
			template::body($body);
		template::end();	
	}
	
	public static function verifyAction(){
		if(!user::validateAdmin()){
			if(form::validate('login')){
				user::adminlogin($_REQUEST['admin'],$_REQUEST['password']);
				userLogin::createLogin($userid);
				route::redirect('system/login/');
			}
		} else {
			route::redirect('');
		}
	}
	
	public static function logoutAction(){
		if(user::validateAdmin()){		
			userLogin::createLogout();
		}
		session_destroy();
		route::redirect('');	
	}
}
?>