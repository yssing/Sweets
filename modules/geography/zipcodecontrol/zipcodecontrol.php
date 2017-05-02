<?php

class zipcodecontrol{

	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		
		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}
		$body = views::displayEditListview(zipcode::listZipcodes($searchVal));

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function findAction($args){	
		if (!$args[0]){
			return false;
		}
		echo zipcode::readSingleZipcode($args[0]);		
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if(isset($args[0])){
			$id = $args[0];
			list($area,$municipality,$zipcode,$city) = zipcode::readZipcode($id);
		} else {
			$id = $area = $municipality = $zipcode = $city = '';
		}

		$body = form::beginForm('update','modules/geography/zipcode/update');
			$body .= form::fieldset('field1',language::readType('AREA'),form::input($area,'area',TEXT,array("style" => "width:220px")));	
			$body .= form::fieldset('field2',language::readType('MUNICIPALITY'),form::input($municipality,'municipality',TEXT,array("style" => "width:220px")));	
			$body .= form::fieldset('field3',language::readType('ZIPCODE'),form::input($zipcode,'zipcode',TEXT,array("style" => "width:220px")));	
			$body .= form::fieldset('field4',language::readType('CITY'),form::input($city,'city',TEXT,array("style" => "width:220px")));	
			$body .= form::input($id,'id',HIDDEN);
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

		if (form::validate('update')){
			if ($args['id']){
				zipcode::updateZipcode($args['id'],$args['area'],$args['municipality'],$args['zipcode'],$args['city']);
			} else {			
				zipcode::createZipcode($args['area'],$args['municipality'],$args['zipcode'],$args['city']);
			}
			route::redirect('geography/zipcode/list');
		}
	}	
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("FK_AreaID" => "int(10)",
					"FK_MunicipalityID" => "int(10)",
					"Zipcode" => "varchar(6)",
					"City" => "varchar(50)");
		$result = $databaseadmin->createTable('geography_zipcode',$what,"PK_ZipcodeID");

		$databaseadmin->importSQLfile('modules/geography/sql/');
	}	
}
?>