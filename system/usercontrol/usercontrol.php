<?php

class usercontrol{
	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = views::displayEditListview(user::listUsers(1));			
		$body .= form::newButton();	
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));	
			template::body($body);	
		template::end();			
	}
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}

		if(isset($args[0]) && $args[0]){
			list($id,$firstName,$lastName,$username,$mail,$acceptNews,$acceptMails) = user::readUser($args[0]);		
			$newuser = form::input($username,'userName',HIDDEN);
		} else {
			$username = 'Ny bruger';
			$newuser = form::fieldset('field2','<h3>'.language::readType('USERNAME').'</h3>',form::input('','userName',TEXT,'',
			array("onKeyUp" => "loginReq(this.id,'imgUser')"))).'<img id="imgUser" src="/template/'.template::getTheme().'/icon/white.png" /><br />';
			$id = 0;
			$firstName = $lastName = $mail = $acceptNews = $acceptMails = '';
		}

		$body = form::beginField('field1','<h3>'.$username.'</h3>');
			$body .= form::beginForm('update',PATH_WEB.'/system/user/update');
				$body .= $newuser;
				$body .= form::fieldset('field3','<h3>'.language::readType('FIRSTNAME').'</h3>',form::input($firstName,'firstName',TEXT)).'<br />';
				$body .= form::fieldset('field4','<h3>'.language::readType('LASTNAME').'</h3>',form::input($lastName,'lastName',TEXT)).'<br />';
				$body .= form::fieldset('field5','<h3>'.language::readType('EMAIL').'</h3>',form::input($mail,'mail',TEXT,array("onKeyUp" => "emailReq(this.id,'imgMail')"))).
				'<img id="imgMail" src="/template/'.template::getTheme().'/icon/white.png" /><br />';
				$body .= form::fieldset('field6','<h3>'.language::readType('RECEIVENEWSMAIL').'</h3>',form::check($acceptNews,'acceptNews')).'<br />';
				$body .= form::fieldset('field7','<h3>'.language::readType('RECEIVESYSTEMMAIL').'</h3>',form::check($acceptMails,'acceptMails')).'<br />';
				$body .= form::fieldset('field8','<h3>'.language::readType('PASSWORD ').'</h3>',form::input('','password1',PASSWORD)).'<br />';
				$body .= form::fieldset('field9','<h3>'.language::readType('PASSWORD ').'*</h3>',form::input('','password2',PASSWORD)).'<br />';
				$body .= form::input($id,'id',HIDDEN);
			$body .= form::endForm('update');
		$body .= form::endField();	

		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));	
			template::body($body);	
		template::end();	
	}

	public static function updateAction($args){
		if(!userBase::validateAdmin()){
			route::error(403);
		}

		//if(form::validate('update')){
			if(userBase::doesExist($args['userName']) != ''){
				$id = $args['id'];
			} else {
				$id = userBase::createUser($args['userName'],'USER');
			}
			// update a user
			if($id){
				if($args['password1'] == $args['password2'] && $args['password1'] != ''){
					userBase::updateUserPassword($args['password1'],$id,'ADMIN_SECRET');
				}
				userBase::updateUserName($args['firstName'],$args['lastName'],$id);
				userBase::updateUserContact(0,0,0,$args['mail'],$id);
				if($args['acceptNews']){
					$acceptnews = 1;
				} else {
					$acceptnews = 0;
				}
				if($args['acceptMails']){
					$acceptmails = 1;
				} else {
					$acceptmails = 0;
				}		
				userBase::updateUserSettings($acceptnews,$acceptmails,$id);
			}
		//}		
		route::redirect('system/user/list');
	}	
	
	public static function installAction($rootname,$password){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$database = new database();
		$what = array("UserStatus" => "varchar(10)",
			"UID" => "varchar(45)",
			"Validated" => "tinyint(1)",	
			"UserFirstName" => "varchar(45)",
			"UserLastName" => "varchar(45)",
			"UserBirth" => "datetime",
			"UserStreet" => "varchar(45)",
			"UserNumber" => "varchar(45)",
			"UserFloor" => "varchar(45)",
			"UserDoor" => "varchar(45)",
			"UserZip" => "varchar(45)",
			"UserCity" => "varchar(45)",
			"UserCountry" => "varchar(45)",
			"UserPhone" => "varchar(45)",
			"UserCell" => "varchar(45)",
			"UserFax" => "varchar(45)",
			"UserEMail" => "varchar(45)",
			"UserLogin" => "varchar(45)",
			"UserPassword" => "varchar(45)",
			"AcceptNews" => "tinyint(1)",	
			"AcceptMails" => "tinyint(1)");
		$result = $database->createTable('user',$what,"PK_UserID");
		if($result){
			$what = array("UserLogin" => "datetime", "UserLogout" => "datetime");
			$result = $database->createTable('user_login',$what,"PK_UserLoginID");		
		}
		if($result){
			if($rootname && $password){
				if($id = userBase::createUser($rootname,'ADMIN')){
					userBase::updateUserPassword($password,$id);
				}
			}
		}	
	}		
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		user::destroyUser($args[0]);
		route::redirect('system/user/list');
	}	
}

?>