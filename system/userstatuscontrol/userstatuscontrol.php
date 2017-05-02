<?php

class userstatuscontrol{

	public static function indexAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$body = views::displayEditListview(userStatus::listStatus());
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		if (isset($args[0]) && $args[0]){
			list($id,$status) = userStatus::readStatus($args[0]);
		} else {
			$id = '';
		}

		$body = form::beginForm('update',PATH_WEB.'/system/userstatus/update');
			$body .= form::fieldset('field3','<h3>'.language::readType('STATUS').'</h3>',form::input($status,'status',TEXT)).'<br />';
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');

		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));	
			template::body($body);
		template::end();
	}
	
	public static function installAction(){
		/*if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}*/
		$databaseadmin = new databaseadmin();
		$what = array("UserStatus" => "varchar(45)");
		$result = $databaseadmin->createTable('user_status',$what,"PK_UserStatusID");
		if ($result){
			userStatus::createStatus('USER');
			userStatus::createStatus('ADMIN');
		}
	}
/*
	public static function listAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$body = '<div id="edit"></div>';
		$body .= views::displayEditListview(userStatus::listStatus());
		$body .= form::newButton();
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}	*/
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if ($args['id'] != ''){	
			userStatus::updateStatus($args['status'],$args['id']);
		} else {
			userStatus::createStatus($args['status']);
		}
		route::redirect('system/userstatus/list');
	}	
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		userStatus::destroyStatus($args[0]);
		route::redirect('system/userstatus/list');
	}
}
?>