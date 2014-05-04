<?php

class areacontrol{

	public static function findAction($args){
		// search for area name by its id
		echo 'test';
	}

	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = views::displayEditListview(area::listAreas());
		$body .= form::newButton();
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$database = new database();
		$what = array("AreaName" => "varchar(100)","FK_ParentID" => "int(10)","AreaCode" => "int(10)");
		$result = $database->createTable('geography_area',$what,"PK_AreaID");
	}	
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		if(isset($args[0]) && $args[0]){
			$id = $args[0];		
			list($areaname,$areacode,$parentid) = area::readSingleArea($id);
		} else {
			$id = 0;
			$areaname = '';
			$parentid = 0;
			$areacode = 0;
		}
		
		$body = form::beginForm('update',PATH_WEB.'/geography/areacontrol/update');
			$body .= form::fieldset('field1','<h3>Område</h3>',form::input($areaname,'areaname',TEXT));	
			$body .= form::fieldset('field2','<h3>Område kode</h3>',form::input($areacode,'areacode',TEXT));	
			$body .= form::fieldset('field3','<h3>Forældre</h3>',form::input($parentid,'parentid',TEXT));	
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();	
	}

	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}

		if(form::validate('update')){
			$parentid = ($args['parentid']) ? $args['parentid'] : 0;
			if($args['id']){
				area::updateArea($args['id'],$args['areaname'],$args['areacode'],$parentid);
			} else {
				area::createArea($args['areaname'],$args['areacode'],$parentid);
			}
		}
		route::redirect('geography/areacontrol/list');
	}	
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		area::destroyArea($args[0]);
		route::redirect('geography/areacontrol/list');
	}
}
?>