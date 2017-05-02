<?php
class saltcontrol{
	public static function indexAction(){
		route::error(403);
	}
	
	public static function listAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}
		$salt = new salt();	
		$body = views::displayEditListview($salt->listSalt());	
		
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
		$salt = new salt();
		$input = array("onclick" => "randomString('saltvalue')");	

		$body = form::beginForm('update',PATH_WEB.'/system/salt/update');
		$body .= form::fieldset('field1','<h3>'.language::readType('SALT').'</h3>',form::inputControl($salt->readSalt(intval($args[0])),'saltvalue','<img src="[TEMPLATE]/icon/replace.png">','',$input));		
		
		$body .= form::input($args[0],'id',2);
		$body .= form::endForm('update');
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));	
			template::body($body);
		template::end();		
	}
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$salt = new salt();

		if (form::validate('update')){
			$salt->updateSalt(intval($args['id']),$args['saltvalue']);			
		}
		route::redirect('system/salt/list');
	}		
	
	public static function installAction(){
		/*if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}*/
		$databaseadmin = new databaseadmin();
		$what = array("Salt_Type" => "varchar(45)", 
					  "Salt" => "varchar(45)");
		$result = $databaseadmin->createTable('generic_salt',$what,"PK_SaltID");

		if ($result){
			$salt = new salt();
			$salt->createSalt('USER_SECRET',baseclass::generateRandStr(32));
			$salt->createSalt('ADMIN_SECRET',baseclass::generateRandStr(32));
			$salt->createSalt('SESSION_SECRET',baseclass::generateRandStr(32));
			$salt->createSalt('COOKIE_SECRET',baseclass::generateRandStr(32));
		}
	}	
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		salt::destroySalt($args[0]);
		route::redirect('system/salt/list');
	}	
}
?>