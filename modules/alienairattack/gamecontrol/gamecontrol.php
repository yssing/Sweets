<?php
class gamecontrol{
	public static function indexAction(){
		template::initiate('index');
			template::noCache();
			template::header();		
			template::body(views::displayListview(aaa::listScores()));
		template::end();	
	}
	
	public static function listAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
		template::initiate('admin');
			template::header('AAA scores');	
			template::body(views::displayEditListview(aaa::listAdminScores()));
		template::end();		
	}
	
	public static function saveAction($args){
		aaa::saveScore($args['playername'],$args['score']);
		route::redirect('modules/alienairattack/game');
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		aaa::deleteScore($args['0']);
		route::redirect('modules/alienairattack/game/list');
	}
	
	public static function installAction(){
		$databaseadmin = new databaseadmin();
		$what = array("Name" => "varchar(45)","Points" => "int(10)");
		$result = $databaseadmin->createTable('aaa_scores',$what,"PK_ScoreID");	
	}	
}
?>