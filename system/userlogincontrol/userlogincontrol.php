<?php
class userlogincontrol{

	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$userid = 0;
		$body = '';
		if (isset($args['userid'])){
			$userid = $args['userid'];
			$body .= views::displayEditListview(userLogin::listUserLogin($userid),'listview',0,PAGING,array('userid' => $userid));
		}
		$body .= form::beginForm('Select',PATH_WEB.'/system/userlogin/list','post');		
			$body .= form::fieldset('field5','<h3>'.language::readType('SELECT_USER').'</h3>',form::select(user::listUsers(),$userid,'userid')).'<br />';
		$body .= form::endForm('Select');		
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
}
?>