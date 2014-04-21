<?php

class zipcodecontrol{

	public static function indexAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}		
		
		$body = views::displayEditListview(zipcode::listZipcodes());
		$body .= form::newButton();
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function findAction($args){	
		if(!$args[0]){
			return false;
		}
		echo zipcode::readSingleZipcode($args[0]);		
	}	
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		list($area,$municipality,$zipcode,$city) = zipcode::readZipcode($args[0]);

		$body = form::beginForm('update',PATH_WEB.'/geography/zipcode/update');
			$body .= form::fieldset('field1',language::readType('AREA'),form::input($area,'area',0,array("style" => "width:220px")));	
			$body .= form::fieldset('field2',language::readType('MUNICIPALITY'),form::input($municipality,'municipality',0,array("style" => "width:220px")));	
			$body .= form::fieldset('field3',language::readType('ZIPCODE'),form::input($zipcode,'zipcode',0,array("style" => "width:220px")));	
			$body .= form::fieldset('field4',language::readType('CITY'),form::input($city,'city',0,array("style" => "width:220px")));	
			$body .= form::input($args[0],'id',2);
		$body .= form::endForm('update');	
				
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();	
	}
	
	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}

		if(form::validate('update')){
			if($args['id']){
				zipcode::updateZipcode($args['id'],$args['area'],$args['municipality'],$args['zipcode'],$args['city']);
			} else {			
				zipcode::createZipcode($args['area'],$args['municipality'],$args['zipcode'],$args['city']);
			}
			route::redirect('geography/zipcode/list');
		}
	}	
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$database = new database();
		$what = array("Name" => "varchar(100)");
		$result = $database->createTable('geography_zipcode',$what,"PK_MunicipalityID");
	}	
}
?>