<?php

class areacontrol{

	public static function findAction($args){
		// search for area name by its id
		list($areaname) = area::readSingleArea($id);
		return $areaname;
	}

	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}		
		$body = views::displayEditListview(area::listAreas($searchVal));

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("AreaName" => "varchar(100)",
					"FK_ParentID" => "int(10)",
					"AreaCode" => "int(10)");
		$result = $databaseadmin->createTable('geography_area',$what,"PK_AreaID");
		$databaseadmin->importSQLfile('modules/geography/sql/');
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if (isset($args[0]) && $args[0]){
			$id = $args[0];		
			list($areaname,$areacode,$parentid) = area::readSingleArea($id);
		} else {
			$id = 0;
			$areaname = '';
			$parentid = 0;
			$areacode = 0;
		}
		
		$body = form::beginForm('update','modules/geography/areacontrol/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('AREA').'</h3>',form::input($areaname,'areaname',TEXT));	
			$body .= form::fieldset('field2','<h3>'.language::readType('AREA_CODE').'</h3>',form::input($areacode,'areacode',TEXT));	
			$body .= form::fieldset('field3','<h3>'.language::readType('PARENT').'</h3>',form::input($parentid,'parentid',TEXT));	
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();	
	}

	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		if (form::validate('update')){
			$parentid = ($args['parentid']) ? $args['parentid'] : 0;
			if ($args['id']){
				area::updateArea($args['id'],$args['areaname'],$args['areacode'],$parentid);
			} else {
				area::createArea($args['areaname'],$args['areacode'],$parentid);
			}
		}
		route::redirect('geography/areacontrol/list');
	}	
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		area::destroyArea($args[0]);
		route::redirect('geography/areacontrol/list');
	}
}
?>