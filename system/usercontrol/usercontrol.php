<?php

class usercontrol{
	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}
		$body = views::displayEditListview(user::listUsers($searchVal));

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		if (isset($args[0]) && $args[0]){
			list($id,$firstName,$lastName,$username,$mail,$acceptNews,$acceptMails,$status) = user::readUser($args[0]);
			list($phone, $cell, $fax, $mail) = userBase::readUserContact($args[0]);
			$newuser = form::input($username,'userName',HIDDEN);
		} else {
			$username = 'Ny bruger';
			$newuser = form::fieldset('field2','<h3>'.language::readType('USERNAME').'</h3>',form::input('','userName',TEXT,'',
			array("onKeyUp" => "loginReq(this.id,'imgUser')"))).'<img id="imgUser" src="/template/'.template::getTheme().'/icon/white.png" /><br />';
			$id = 0;
			$firstName = $lastName = $mail = $acceptNews = $acceptMails = $phone = $cell = $fax = '';
			$status = 0;
		}
		$statuslist = userStatus::listSelect();

		$body = '<h3>'.$username.'</h3><div class="col-md-6">';
			$body .= form::beginForm('update',PATH_WEB.'/system/user/update');
				$body .= $newuser;
				$body .= form::fieldset('field3','<h3>'.language::readType('FIRSTNAME').'</h3>',form::input($firstName,'firstName',TEXT)).'<br />';
				$body .= form::fieldset('field4','<h3>'.language::readType('LASTNAME').'</h3>',form::input($lastName,'lastName',TEXT)).'<br />';
				$body .= form::fieldset('field5','<h3>'.language::readType('EMAIL').'</h3>',form::inputControl($mail,'mail',
				'<img id="imgMail" src="/template/'.template::getTheme().'/icon/stop.png" />',array("onKeyUp" => "emailReq(this.id,'imgMail')"))).'<br />';
				
				$body .= form::fieldset('field4','<h3>'.language::readType('PHONE').'</h3>',form::input($phone,'phone',TEXT)).'<br />';
				$body .= form::fieldset('field4','<h3>'.language::readType('CELL').'</h3>',form::input($cell,'cell',TEXT)).'<br />';
			
			$body .= '</div><div class="col-md-6">';
				$body .= form::fieldset('field4','<h3>'.language::readType('FAX').'</h3>',form::input($fax,'fax',TEXT)).'<br />';
				$body .= form::fieldset('field6','<h3>'.language::readType('STATUS').'</h3>',form::select($statuslist,$status,'status',0)).'<br />';
				$body .= form::fieldset('field7','<h3>'.language::readType('RECEIVE_NEWS_MAIL').'</h3>',form::check($acceptNews,'acceptNews')).'<br />';
				$body .= form::fieldset('field8','<h3>'.language::readType('RECEIVE_SYSTEM_MAIL').'</h3>',form::check($acceptMails,'acceptMails')).'<br />';
				$body .= form::fieldset('field9','<h3>'.language::readType('PASSWORD').'</h3>',form::input('','password1',PASSWORD)).'<br />';
				$body .= form::fieldset('field10','<h3>'.language::readType('PASSWORD').'*</h3>',form::input('','password2',PASSWORD)).'<br />';
				$body .= form::input($id,'id',HIDDEN);
			$body .= form::endForm('update');
		$body .= '</div>';
		$body .= '<div class="col-md-12"><a href="/system/userlogin/list/?userid='.$id.'">'.language::readType('LOGIN_LIST').'</a></div>';

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}

	public static function updateAction($args){
		if (!userBase::validateAdmin()){
			route::error(403);
		}

		if (form::validate('update')){
			if (userBase::doesExist($args['userName']) != ''){
				$id = $args['id'];
			} else {
				$id = userBase::createUser($args['userName'],'USER');
			}
			// update a user
			if ($id){
				if ($args['password1'] == $args['password2'] && $args['password1'] != ''){
					// check if user is admin or not
					if ($args['status'] == 'ADMIN'){
						userBase::updateUserPassword($args['password1'],$id,'ADMIN_SECRET');
					} else {
						userBase::updateUserPassword($args['password1'],$id,'USER_SECRET');
					}
				}
				userBase::updateUserName($args['firstName'],$args['lastName'],$id);
				userBase::updateUserContact($args['phone'],$args['cell'],$args['fax'],$args['mail'],$id);
				if ($args['acceptNews']){
					$acceptnews = 1;
				} else {
					$acceptnews = 0;
				}
				if ($args['acceptMails']){
					$acceptmails = 1;
				} else {
					$acceptmails = 0;
				}		
				userBase::updateUserSettings($acceptnews,$acceptmails,$id);
				userBase::updateUserValidate($id);
			}
		}		
		route::redirect('system/user/list');
	}	
	
	
	public static function sendMailAction($args){
		if ($args['callback']){
			route::redirect($args['callback']);
		} else {
			route::redirect('system/user/');
		}
	}
	
	public static function installAction(){
		$databaseadmin = new databaseadmin();

		$what = array("FK_AreaID" => "int(10)",
					  "UserStatus" => "varchar(10)",
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
					  "UserImage" => "varchar(45)",
					  "AcceptNews" => "tinyint(1)",	
					  "AcceptMails" => "tinyint(1)");
		$result = $databaseadmin->createTable('user',$what,"PK_UserID");

		$fields = array("UserLogin" => "datetime", 
					  "UserLogout" => "datetime", 
					  "IP" => "varchar(45)", 
					  "ForwardIP" => "varchar(45)");
		$result = $databaseadmin->createTable('user_login',$fields,"PK_UserLoginID");		
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		user::destroyUser($args[0]);
		route::redirect('system/user/list');
	}
}
?>